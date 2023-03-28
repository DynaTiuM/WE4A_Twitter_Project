<?php


class User
{
    private $email;
    private $username;
    private $lastname;
    private $firstname;
    private $date_of_birth;
    private $password;
    private $avatar;
    private $organisation;
    private $bio;
    private $conn;
    private $db;

    private static $instance;

    public function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    // Modèle singleton
    public static function getInstance($conn, $db) {
        if (self::$instance === null) {
            self::$instance = new User($conn, $db);
        }

        return self::$instance;
    }

    protected function getTableName() {
        return 'utilisateur';
    }

    public function verifyUnicity($parameter) {
        $query = "(SELECT username FROM utilisateur WHERE username = ?) UNION (SELECT id FROM animal WHERE id = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $parameter, $parameter);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            return false;
        }
        return true;
    }

    public static function exists($conn, $username) {
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function updateProfile($prenom, $nom, $date, $password, $bio) {
        $query = "UPDATE utilisateur SET prenom = '$prenom', nom = '$nom', date_de_naissance = '$date', mot_de_passe = '$password', bio = '$bio' WHERE username = '" . $this->username . "'";
        $this->conn->query($query);
    }

    public function isFollowing($auteur_username) {
        $sql = "SELECT COUNT(*) as count 
            FROM suivre
            WHERE utilisateur_username = ?
              AND (
                  (suivi_type = 'utilisateur' AND suivi_id_utilisateur = ?)
                OR (suivi_type = 'animal' AND suivi_id_animal IN (SELECT animal_id FROM message_animaux WHERE message_id IN (SELECT id FROM message WHERE auteur_username = ?)))
                  )";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $this->username, $auteur_username, $auteur_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }

        return false;
    }

    public function checkLogin(): array {
        $error = NULL;
        $loginSuccessful = false;

        if(isset($_POST["username"]) && isset($_POST["password"])){
            $this->username = $this->db->secureString_ForSQL($_POST["username"]);
            $this->password = $_POST["password"];
            $loginAttempted = true;
        }
        elseif ( isset( $_COOKIE["username"] ) && isset( $_COOKIE["password"] ) ) {
            $this->username = $_COOKIE["username"];
            $this->password = $_COOKIE["password"];
            $loginAttempted = true;
        }
        else {
            $loginAttempted = false;
        }

        if ($loginAttempted){
            $query = "SELECT * FROM `utilisateur` WHERE username = '".$this->username."'";
            $result = $this->conn->query($query);

            if ($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $hashed_password = $row['mot_de_passe'];

                if (password_verify($this->password, $hashed_password)) {
                    $this->createLoginCookie($hashed_password);
                    $loginSuccessful = true;
                } else {
                    $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte";
                }
            }
            else {
                $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte";
            }
        }

        return array($loginSuccessful, $loginAttempted, $error);
    }
    public function isLoggedIn(): bool {
        $loginAttempted = false;
        if (isset($_COOKIE["username"]) && isset($_COOKIE["password"])) {
            $this->username = $_COOKIE["username"];
            $this->password = $_COOKIE["password"];
            $loginAttempted = true;
        }
        if ($loginAttempted) {
            $query = "SELECT * FROM `utilisateur` WHERE username = ? AND mot_de_passe = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $this->username, $this->password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getUsername() {
        return $this->username;
    }

    public function createLoginCookie($encryptedPasswd) {
        setcookie("username", $this->username, time() + 24*3600 );
        setcookie("password", $encryptedPasswd, time() + 24*3600);
    }
    public function getUserInformation() {
        $query = "SELECT * FROM utilisateur WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }

    public function changePassword($conn, $new_password) {
        $this->password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE username = ?");
        $stmt->bind_param("ss", $this->password, $this->username);
        $stmt->execute();
    }
    public function numFollowing($conn) {
        $query = "SELECT COUNT(*) FROM suivre WHERE utilisateur_username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_Column();
    }
    public function follow_unfollow($conn, $to_follow, $type) {
        if($type == 'user') $type = 'utilisateur';
        else $type = 'animal';

        $stmt = $conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
        $stmt->bind_param("ss", $this->username, $to_follow);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
            $stmt->bind_param("ss", $this->username, $to_follow);
            $stmt->execute();
            return;
        }

        $stmt = $conn->prepare("INSERT INTO suivre (utilisateur_username, suivi_type, suivi_id_$type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->username, $type, $to_follow);
        $stmt->execute();
    }
    public function checkFollow($conn, $to_follow, $type): bool {
        $stmt = $conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_type = ? AND suivi_id_$type = ?");
        $stmt->bind_param("sss", $this->username, $type, $to_follow);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function checkNewAccountForm(): array {
        $creationAttempted = false;
        $creationSuccessful = false;
        $error = NULL;
        $completed = isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["confirm"])
            && isset($_POST["prenom"]) && isset($_POST["nom"])
            && isset($_POST["date_de_naissance"]) &&  isset($_POST["organisation"]);

        //Données reçues via formulaire?
        if($completed && $this->verifyUnicity($_POST['username'])){

            $creationAttempted = true;

            //Form is only valid if password == confirm, and username is at least 4 char long
            if ( strlen($_POST["username"]) < 4 ){
                $error = "Un nom utilisateur doit avoir une longueur d'au moins 4 lettres";
            }
            elseif ( $_POST["password"] != $_POST["confirm"] ){
                $error = "Le mot de passe et sa confirmation sont différents";
            }
            else {
                $this->email = $this->db->secureString_ForSQL($_POST["email"]);
                $this->username = $this->db->secureString_ForSQL($_POST["username"]);
                $this->lastname = $this->db->secureString_ForSQL($_POST["nom"]);
                $this->firstname = $this->db->secureString_ForSQL($_POST["prenom"]);
                $this->date_of_birth = $_POST["date_de_naissance"];
                $this->organisation = $_POST["organisation"];
                $this->avatar = file_get_contents('../images/default_avatar.png');
                $avatarBLOB = mysqli_real_escape_string($this->conn, $this->avatar);
                $this->password = password_hash($_POST["password"], PASSWORD_DEFAULT);

                $query = "INSERT INTO `utilisateur` VALUES ('$this->email', '$this->username', '$this->lastname', '$this->firstname', '$this->date_of_birth', '$this->password', '$avatarBLOB', '$this->organisation', null )";
                $this->conn->query($query);

                if( mysqli_affected_rows($this->conn) == 0 )
                {
                    $error = "Erreur lors de l'insertion SQL. Essayez un nom/password sans caractères spéciaux";
                }
                else {
                    $creationSuccessful = true;
                }
            }
        }
        else {
            $error = "Nom d'utilisateur déjà existant";
        }

        return array($creationAttempted, $creationSuccessful, $error);
    }

    function displayPets() {
        $query = "SELECT * FROM animal WHERE maitre_username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function loadAvatar($conn) {
        $sql = "SELECT avatar FROM utilisateur WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["avatar"];
        }

        return "Aucune image trouvée.";
    }
}
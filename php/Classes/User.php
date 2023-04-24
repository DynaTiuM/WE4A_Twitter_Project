<?php

require_once ("Entity.php");

class User extends Entity
{
    private $email;
    private $lastName;
    private $firstName;
    private $dateOfBirth;
    private $organisation;
    private $bio;

    private static $instance;

    public function __construct($conn, $db) {
        parent::__construct($conn, $db);
    }

    /**
     * Méthode au model singleton qui permet de récupéreer l'instance d'un utilisateur, ou d'en créer une nouvelle si elle n'existe pas
     *
     * @param $conn
     * @param $db
     * @return User
     */
    public static function getInstance($conn, $db) {
        if (self::$instance === null) {
            self::$instance = new User($conn, $db);
        }

        return self::$instance;
    }

    /**
     * Méthode static permettant de récupérer l'instance d'un utilisateur par rapport à son username
     *
     * @param $conn
     * @param $db
     * @param $username
     * @return User|null
     */
    public static function getInstanceById($conn, $db, $username) {

        // Nous créons une nouvelle instance de la classe user
        $user = new User($conn, $db);

        // Nous préparons la requête, qui permettra de trouver l'utilisateur par rapport à son username
        $query = "SELECT * FROM utilisateur WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Si un utilisateur est trouvé
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Nous mettons à jour les paramètres de la classe user
            $user->username = $row['username'];
            $user->email = $row['email'];
            $user->organisation = $row['organisation'];
            $user->bio = $row['bio'];
            $user->dateOfBirth = $row['date_de_naissance'];
            $user->lastName = $row['nom'];
            $user->firstName = $row['prenom'];

            // Nous retournons ainsi l'utilisateur trouvé
            return $user;
        }
        // Sinon, si aucun utilisateur n'est retrouvé dans le recherche SQL, nous retournons null
        else {
            return null;
        }
    }

    /**
     * Méthode permettant de savoir si un utilisateur suit un utilisateur auteur d'un message ou un animal qui a été tagué dans un message
     *
     * @param $auteur_username
     * @return bool
     */
    public function isFollowing($auteur_username) : bool {
        // La requete SQL ci dessous permet de récupérer le nombre de lignes de la table suivre où l'utilisateur est égal à l'utilisateur qui suit potentiellement l'auteur de message
        /* Il est nécessaire d'ajouter une autre clause de vérification pour connaitre s'il s'agit bien de l'auteur du message qu'il suit
        /* Or, l'auteur du suivi peut soit être un animal, ou un utilisateur
        /* Car rappelons qu'un utilisateur peut suivre un animal ou un utilisateur
        /* Donc il faut d'abord vérifier par l'intermédiaire du suivi_type = 'utilisateur' si le username de l'auteur du message concorde
        /* Ou du coté de l'animal avec suivi_id_animal = 'animal'
        /* Mais il y a également une autre démarche à réaliser : Il faut vérifier si l'animal est tagué dans un message par rapport à l'auteur du message passé en paramètres
        /* Cela se réalise en vérifiant qu'il y ai au moins 1 username d'animal où cet username se trouve dans un message dont le username est l'auteur du message (clause IN (SELECT....))
        */
        $query = "SELECT COUNT(*) as count 
            FROM suivre
            WHERE utilisateur_username = ?
              AND (
                  (suivi_type = 'utilisateur' AND suivi_id_utilisateur = ?)
                OR (suivi_type = 'animal' AND suivi_id_animal IN (SELECT animal_id FROM message_animaux WHERE message_id IN (SELECT id FROM message WHERE auteur_username = ?)))
                  )";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $this->username, $auteur_username, $auteur_username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Si le nombre de lignes retournée est supérieur à 0, c'est que c'est vrai
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row['count'] > 0)
                return true;
        }

        // Sinon c'est faux
        return false;
    }

    /**
     * Méthode simple permettant de vérifier l'état de connexion d'un utilisateur
     *
     * @return array
     */
    public function checkLogin(): array {
        $error = NULL;
        $loginSuccessful = false;

        // Si le formulaire de connexion a été envoyé et que le username et password on été renseignés :
        if(isset($_POST["username"]) && isset($_POST["password"])){
            // On affecte les valeurs de username et password dans des variables
            $this->username = $this->db->secureString_ForSQL($_POST["username"]);
            // Ici password n'est pas stocké dans un attribut privé de la classe, pour des questions de sécurité : le mot de passe doit sortir le moins possible de la base de données
            $password = $_POST["password"];
            // On informe ainsi que l'utilisateur a essayé de se connecter
            $loginAttempted = true;
        }
        // Sinon, si une session est déjà active,
        elseif (isset($_SESSION["username"] )) {
            // On ajoute le nom d'utilisateur dans l'attribut privé
            $this->username = $_SESSION["username"];
            // Et on informe ainsi qu'une connexion a été tentée
            $loginAttempted = true;
        }
        else {
            // Sinon, aucun essai de connexion n'a été effectué
            $loginAttempted = false;
        }

        // Dans le cas où une connexion a été tentée, il est important de vérifier si elle est correcte, il faut donc interroger la BDD
        if ($loginAttempted){
            // On cherche un utilisateur possédant le username informé
            $query = "SELECT * FROM `utilisateur` WHERE username = '".$this->username."'";
            $result = $this->conn->query($query);

            // S'il y a un utilisateur qui match :
            if ($result->num_rows > 0){
                $row = $result->fetch_assoc();
                // On regarde si le mot de passe a été renseigné,
                if(!isset($password)) {
                    // Si c'est le cas, on utilise une méthode qui permet de hasher le mot de passe par salage, plus sécurisé que le chiffrage MD5
                    $password = password_hash($row['mot_de_passe'], PASSWORD_DEFAULT);
                }
                // D'un autre coté, on récupère le mot de passe de la base de données
                $hashed_password = $row['mot_de_passe'];

                // Les deux mots de passe étant salés de leur coté, il ne reste plus qu'à vérifier s'ils correspondent grâce à une fonction implémentée directement en PHP :
                if (password_verify($password, $hashed_password)) {
                    // Si ca concorde, la connexion est réussie, on lance une session !
                    session_start();
                    // On donne la valeur de session username grâce à l'attribut username
                    $_SESSION['username'] = $this->username;
                    // Finalement, on informe que la connexion est réussie
                    $loginSuccessful = true;
                } else {
                    // Sinon, on informe l'utilisateur que le couple n'existe pas
                    $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte";
                }
            }
            else {
                // Le mot de passe n'existe pas :
                $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte";
            }
        }
        // On retourne un tableau de toutes les informations stockées pour pouvoir les manipuler
        return array($loginSuccessful, $loginAttempted, $error);
    }

    /**
     * Méthode permettant de savoir si un utilisateur est actuellement connecté sur le site
     *
     * @return bool
     *
     */
    public function isLoggedIn(): bool {
        // On commence par initialiser loginAttempted à false
        $loginAttempted = false;

        // Si une session existe, on réalise le meme procédé que précédemment :
        if (isset($_SESSION["username"])) {
            $this->username = $_SESSION["username"];
            $loginAttempted = true;
        }

        // Au final, si une connexion est tentée :
        if ($loginAttempted) {
            // Il est nécessaire ici de décomposer le processus en deux parties :
            // Tout d'abord on récupère le mot de passe en fonction du username de la session actuelle
            $query = "SELECT mot_de_passe FROM utilisateur WHERE username = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $this->username);
            $stmt->execute();
            $result = $stmt->get_result();

            // Si aucune ligne n'est récupérée, c'est que le nom d'utilisateur n'existe pas. La session dont l'utilisateur essaye de se connecter est donc fausse
            if(!$result) {
                return false;
            }
            // Dans le cas contraire, l'utilisateur est forcément connecté
            return true;
        }

        // S'il n'y a pas eu d'essai de connexion, l'utilisateur est forcément déconnecté.
        return false;
    }

    // Getter
    public function getUsername() {
        return $this->username;
    }

    /**
     * Méthode permettant de récupérer les informations d'un utilisateur
     *
     * @return array|false|void|null
     */
    public function getUserInformation() {
        // Cette méthode est clairement très simple, elle récupère tout simplement toutes les informations d'un utilisateur en fonction de son username
        $query = "SELECT * FROM utilisateur WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();

        $result = $stmt->get_result();

        // On renvoie, s'il y a une ligne de récupérée, les informations de l'utilisateur
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }

    // Getters
    public function getFirstName() {
        return $this->firstName;
    }
    public function getLastName() {
        return $this->lastName;
    }
    public function getDateOfBirth() {
        return $this->dateOfBirth;
    }
    public function getBio() {
        return $this->bio;
    }

    public function isOrganization() {
        return $this->organisation == 1;
    }

    /**
     * Méthode permettant à un utilisateur de liker un message
     *
     * @param $id_message
     * @return void
     */
    public function likeMessage($id_message) {
        $id_message = $this->db->secureString_ForSQL($id_message);
        $date = date('Y-m-d H:i:s');

        // Nous créons une instanciation d'un message
        $message = new Message($this->conn, $this->db);
        $message->setId($id_message);

        // Nous vérifions si le message est déjà liké par l'utilisateur
        if (!$message->isMessageLikedByUser($this->username)) {
            // Si ce n'est pas le cas, on insère le like du message dans la base de données, avec la date du like
            $stmt = $this->conn->prepare("INSERT INTO like_message (message_id, utilisateur_username, date) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $id_message, $this->username, $date);
            $stmt->execute();
            $stmt->close();

            // Par la suite, on récupère l'auteur du message grâce à l'id du message. Cela servira à la création d'une notification pour l'informer qu'un utilisateur a liké son message
            $stmt = $this->conn->prepare("SELECT auteur_username FROM  message WHERE id = ?");
            $stmt->bind_param("s", $id_message);
            $stmt->execute();
            $result = $stmt->get_result();
            $author_username = $result->fetch_assoc()['auteur_username'];
            $stmt->close();

            require_once ("../Classes/Notification.php");
            $notification = new Notification($this->conn, $this->db);
            // On vérifie si la notification a déjà été envoyée à l'auteur du message
            if(!$notification->isAlreadySent($author_username, $id_message))
                // Si ce n'est pas le cas, on crée la notification à l'auteur du message
                $notification->createNotificationForLike($author_username, $id_message);
        } else {
            // Dans le cas où le message est déjà liké par l'utilisateur, on le supprime. En effet, lorsqu'on clique une deuxième fois sur un like, le like est supprimé
            $stmt = $this->conn->prepare("DELETE FROM like_message WHERE message_id = ? AND utilisateur_username = ?");
            $stmt->bind_param("ss", $id_message, $this->username);
            // On exécute la requete, et le like est enlevé de la base de données
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Méthode permettant de changer le mot de passe d'un utilisateur dans la base de données
     *
     * @param $conn
     * @param $new_password
     * @return void
     */
    public function changePassword($conn, $new_password) {
        // Il est important de chiffrer le mot de passe avant de l'envoyer dans la base de données
        $password = password_hash($new_password, PASSWORD_DEFAULT);
        // Finalement, on effectue une requete SQL simple permettant de changer le mot de passe par rapport à un username d'utilisateur
        $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE username = ?");
        $stmt->bind_param("ss", $password, $this->username);
        $stmt->execute();
    }

    /**
     * Méthode permettant de mettre à jour le profil d'un utilisateur
     *
     * @param $avatar
     * @param $firstName
     * @param $lastName
     * @param $dateOfBirth
     * @param $bio
     * @param $newPassword
     * @param $confirmationNewPassword
     * @return string
     */
    public function updateProfile($avatar =null, $firstName, $lastName, $dateOfBirth, $bio, $newPassword, $confirmationNewPassword) {

        // Le mécanisme ici est identique à celui de la classe Animal.
        // J'invite le lecteur à regarder les commentaires plus détaillés de la méthode updateProfile() de la classe Animal pour comprendre le mécanisme de concaténation des strings
        $query = "UPDATE utilisateur SET prenom = ?, nom = ?, date_de_naissance = ?, bio = ?";

        $params = array($firstName, $lastName, $dateOfBirth, $bio);
        $types = "ssss";

        if(isset($avatar) && is_uploaded_file($avatar['tmp_name'])) {
            require_once ("../Classes/Image.php");
            $avatar = new Image($avatar);
            $avatar->formatImage();
            $query .= ", avatar = ?";
            $params[] = $avatar->getFormatedImage();
            $types .= "s";
        }

        // Si l'utilisateur souhaite modifier son mot de passe :
        if (!empty($newPassword)) {
            // Il est nécessaire de comparer les deux mots de passe pour voir s'ils concordent (entre le nouveau mot de passe et sa confirmation)
            if(!$this->comparePasswords($newPassword, $confirmationNewPassword)) {
                // S'ils sont différents, on informe l'utilisateur
                return "Le nouveau mot de passe et sa confirmation ne sont pas identiques !";
            }
            // Sinon on ajoute le nouveau mot de passe dans la requete SQL
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query .= ", mot_de_passe = ?";
            $params[] = $hashedPassword;
            $types .= "s";
        }

        $query .= " WHERE username = ?";

        $stmt = $this->conn->prepare($query);
        $params[] = $this->username;
        $types .= "s";

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        // Si une ligne a été affectée (donc qu'un profil utilisateur a été modifiée), on met à jour ses attributs privés.
        if ($stmt->affected_rows > 0) {
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->dateOfBirth = $dateOfBirth;
            $this->bio = $bio;
        }

        // On informe l'utilisateur que son profil a été modifié avec succès.
        return "Profil modifié avec succès !";
    }


    /**
     * Méthode permettant de retourner le nombre d'abonnements d'un utilisateur
     *
     * @return mixed
     */
    public function numFollowing() {
        // Requete SQL simple permettant de récupérer le nombre de lignes où l'utilisateur est abonné à n'importe quel compte :
        $query = "SELECT COUNT(*) FROM suivre WHERE utilisateur_username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        // On retourne ainsi le nombre de lignes trouvées
        return $result->fetch_column();
    }

    /**
     * Méthode permettant de vérifier l'état de la création d'un nouveau compte utilisateur
     *
     * @return array
     * @throws Exception
     */
    public function checkNewAccountForm(): array {
        $creationAttempted = false;
        $creationSuccessful = false;
        $error = NULL;
        // Pour que l'inscription soit complete, tous les champs doivent être remplis :
        $completed = isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["confirm"])
            && isset($_POST["prenom"]) && isset($_POST["nom"])
            && isset($_POST["date_de_naissance"]) &&  isset($_POST["organisation"]);

        //Si tout est rempli et que l'unicité du nom d'utilisateur est vérifiée :
        if($completed && $this->verifyUnicity($_POST['username'])){
            // On informe qu'une création de compte a été essayée
            $creationAttempted = true;

            // Le formulaire est valide si et seulement si le nom d'utilisateur fait au minimum 4 lettres
            if ( strlen($_POST["username"]) < 4 ){
                // On informe de l'erreur
                $error = "Un nom utilisateur doit avoir une longueur d'au moins 4 lettres.";
            }
            // Sinon, si les mots de passe ne concordent pas :
            elseif (!$this->comparePasswords($_POST["password"], $_POST["confirm"])){
                // On informe également de l'erreur
                $error = "Le mot de passe et sa confirmation sont différents.";
            }
            // Sinon, si l'utilisateur est trop jeune :
            elseif ($this->calculateAge($_POST["date_de_naissance"]) < 13) {
                // On lui informe une nouvelle fois
                $error = "Vous devez être âgé d'au moins 13 ans pour vous inscrire sur notre plateforme.";
            }
            // Sinon, cela signifie que le compte a été créé avec succès, donc :
            else {
                // On ajoute tous les attributs privés de la classe utilisateur avec ce que possède la ligne SQL
                $this->username = $this->db->secureString_ForSQL($_POST["username"]);
                $this->setUserInformation();
                $avatarBLOB = mysqli_real_escape_string($this->conn, $this->avatar);

                // Excepté password pour des questions de sécurité pour le mot de passe
                $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

                // On insert finalement toutes les informations dans la base de données
                $query = "INSERT INTO `utilisateur` VALUES ('$this->email', '$this->username', '$this->lastname', '$this->firstname', '$this->dateOfBirth', '$password', '$avatarBLOB', '$this->organisation', null )";
                $this->conn->query($query);

                if( mysqli_affected_rows($this->conn) == 0 ) {
                    $error = "Erreur lors de l'insertion SQL. Essayez un nom/password sans caractères spéciaux.";
                }
                else {
                    // Si la connexion a été réussie, on l'informe dans la variable ci-dessous :
                    $creationSuccessful = true;
                }
            }
        }
        else {
            $error = "Nom d'utilisateur déjà existant.";
        }

        // On retourne l'array de toutes les informations collectées lors de la création du compte
        return array($creationAttempted, $creationSuccessful, $error);
    }

    /**
     * Méthode privée permettant de calculer l'age d'un utilisateur
     *
     * @param $dateOfBirth
     * @return int
     * @throws Exception
     */
    private function calculateAge($dateOfBirth): int {
        // Il suffit simplement de créer une date possédant la date de naissance de l'utilisateur :
        $dateOfBirth = new DateTime($dateOfBirth);
        // Puis d'une seconde date, mais possédant la date actuelle :
        $now = new DateTime();
        // Il ne reste qu'à calculer l'intervalle entre les deux dates
        $interval = $now->diff($dateOfBirth);
        // Et on renvoie cet interval en années avec ->y
        return $interval->y;
    }

    /**
     * Méthode comparant deux mots de passe
     *
     * @param $password
     * @param $confirmPassword
     * @return bool
     */
    private function comparePasswords($password, $confirmPassword): bool {
        return $password == $confirmPassword;
    }

    /**
     * Méthode permettant d'ajouter les caractéristiques d'un utilisateur depuis la base de données à la classe User
     *
     * @return void
     */
    function setUserInformation() {
        // La méthode récupère seulement les informations de l'utilisateur par rapport à son username
        $query = "SELECT * FROM utilisateur WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Puis stocke tout dans les variables privées de la classe
        $this->email = $this->db->secureString_ForSQL($row["email"]);
        $this->username = $this->db->secureString_ForSQL($row["username"]);
        $this->lastName = $this->db->secureString_ForSQL($row["nom"]);
        $this->firstName = $this->db->secureString_ForSQL($row["prenom"]);
        $this->dateOfBirth = $row["date_de_naissance"];
        $this->organisation = $row["organisation"];
    }

    /**
     * Méthode permettant d'afficher les animaux d'un utilisateur
     *
     * @return false|mysqli_result
     */
    function displayPets() {
        // De la meme manière que la méthode getUserInformation(), cette méthode récupère tout simplement tous les animaux d'un utilisateur
        $query = "SELECT * FROM animal WHERE maitre_username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();

        return $stmt->get_result();
    }

    /**
     * Méthode permettant de récupérer la requete SQL des messages ou réponses d'un utilisateur
     *
     * @param $isMessage
     * @return string
     */
    public function queryMessagesAndAnswers($isMessage): string {
        // Dans le cas où on cherche la liste des messages de l'utilisateur :
        if ($isMessage) {
            // On retourne la requete SQL qui vérifie en fonction de l'auteur du message, et en vérifiant bien que ce message ne possède pas de message parent
            // S'il ne possède pas de message parent, il s'agit forcément d'un message et non d'une réponse
            // On n'oublie pas de ordonner les messages par leur date en décroissant (du plus récent au plus ancien)
            return "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id IS NULL) ORDER BY date DESC";
        } else {
            // Sinon, on souhaite la liste des réponses de messages, ce qui signifie que les messages possèdent un message parent
            // La requete SQL est identique, à part la vérification du message parent qui ne doit pas être nulle
            return "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id is not NULL) ORDER BY date DESC";
        }
    }

    /**
     * Méthode permettant de charger l'avatar d'un utilisateur en le récupérant de la base de données
     *
     * @return string
     */
    public function loadAvatar() : string {
        $sql = "SELECT avatar FROM utilisateur WHERE username = ?";
        return $this->selectSQLAvatar($sql);
    }

    /**
     * Méthode permettant de compter tous les messages d'un utilisateur
     *
     * @return mixed
     */
    public function countAllMessages() {
        // Requete SQL simple qui récupère le nombre de lignes de message où son auteur est l'utilisateur cherché
        $query = "SELECT COUNT(*) FROM message WHERE auteur_username = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        // On revoie ainsi simplement le nombre de messages trouvé
        return $result->fetch_column();
    }
}
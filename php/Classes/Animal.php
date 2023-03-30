<?php

require_once ("Entity.php");
class Animal extends Entity
{
    private $name;
    private $adoption;
    private $masterUsername;
    private $age;
    private $gender;
    private $characteristics;
    private $species;
    private $adopt;

    public function __construct($conn, $db) {
        parent::__construct($conn, $db);
    }

    public function getAdoption() {
        return $this->adopt;
    }

    public function getMasterUsername() {
        return $this->masterUsername;
    }
    public function getCharacteristics() {
        return $this->characteristics;
    }
    public function getSpecies() {
        return $this->species;
    }

    public function getGender() {
        return $this->gender;
    }
    public function getAge() {
        return $this->age;
    }
    public function getName() {
        return $this->name;
    }

    public static function getInstanceById($conn, $db, $username) {
        $animal = new Animal($conn, $db);

        $query = "SELECT * FROM animal WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $animal->username = $row['id'];
            $animal->masterUsername = $row['maitre_username'];
            $animal->characteristics = $row['caracteristiques'];
            $animal->name = $row['nom'];
            $animal->age = $row['age'];
            $animal->gender = $row['sexe'];
            $animal->species = $row['espece'];
            $animal->adopt = $row['adopter'];

            return $animal;
        } else {
            return null; // Aucun utilisateur trouvé avec cet ID
        }
    }

    public function updateAvatar($image) {
        if (isset($image) && is_uploaded_file($image["tmp_name"])) {
            $image_file = $image['tmp_name'];
            $image_data = file_get_contents($image_file);

            $conn = Database::getConnection();
            $query = $conn->prepare("UPDATE animal SET avatar = ? WHERE username = ?");

            $username = $this->getUsername();
            $query->bind_param('ss', $image_data, $username);

            $query->execute();

            $query->close();
        }
    }

    public function setAttributes($id, $name, $masterUsername, $age, $gender, $avatar, $characteristics, $species, $adoption) {
        global $globalUser;
        if (!isset($_POST['adoption'])) {
            $this->adoption = 0;
        } else {
            $this->adoption = $this->db->secureString_ForSQL($_POST['adoption']);
        }

        // Utilisation de la classe Utilisateur pour vérifier l'unicité de l'ID
        if (!$globalUser->verifyUnicity($_POST['id'])) {
            return "Identifiant déjà existant !";
        }

        if (isset($_FILES["avatar_pet"]) && is_uploaded_file($_FILES["avatar_pet"]["tmp_name"])) {
            $image = file_get_contents($_FILES["avatar_pet"]["tmp_name"]);

            $query = "INSERT INTO animal (id, nom, maitre_username, age, sexe, avatar, caracteristiques, espece, adopter) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssisssss", $_POST['id'], $_POST['nom'], $_COOKIE['username'], $_POST['age'], $_POST['gender'], $image, $_POST['bio'], $_POST['species'], $this->adoption);
            $stmt->execute();
            $stmt->close();
            return "Animal ajouté!";
        }

        $avatar = file_get_contents('../images/default_avatar_pet.png');
        $avatarBLOB = mysqli_real_escape_string($this->conn, $avatar);
        $query = "INSERT INTO animal (id, nom, maitre_username, age, sexe, avatar, caracteristiques, espece, adopter) 
                  VALUES ('" . $_POST['id'] . "', '" . $_POST['nom'] . "', '" . $_COOKIE['username'] . "', " . $_POST['age'] . ", '" . $_POST['gender'] . "', '$avatarBLOB', '" . $_POST['bio'] . "', '" . $_POST['species'] . "', '$this->adoption')";
        $this->conn->query($query);

        return "Animal ajouté!";
    }

    function getPetInformation($conn) {
        $query = "SELECT * FROM animal WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $this->id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }

    protected function getTableName() {
        return 'animal';
    }
    public static function exists($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM animal WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function updateProfile($nom, $age, $sexe, $bio, $espece, $adoption) {
        global $conn;
        $query = "UPDATE animal SET nom = '$nom', age = '$age', sexe = '$sexe', caracteristiques = '$bio', espece = '$espece', adopter = '$adoption' WHERE id = '" . $this->id . "'";
        $conn->query($query);
    }

    public function loadAvatar() {
        $sql = "SELECT avatar FROM animal WHERE id = ?";
        return $this->selectSQLAvatar($sql);
    }

    public function countAllMessages($conn) {
        $query = "SELECT COUNT(*) FROM message_animaux WHERE animal_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_Column();
    }

    public function profilMessages($conn) {

        $query = "SELECT message.*
                FROM message
                    JOIN message_animaux
                        ON message.id = message_animaux.message_id
                    JOIN animal
                        ON animal.id = message_animaux.animal_id
                WHERE animal.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                //$this->displayContent($row);
            }
        }
        else {
            echo '<br><h4>Ce profil ne contient aucun message</h4>';
        }
    }
}
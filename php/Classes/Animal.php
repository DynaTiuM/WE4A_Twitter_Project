<?php

class Animal extends Profile
{
    private $id;
    private $name;
    private $owner_username;
    private $age;
    private $gender;
    private $avatar;
    private $characteristics;
    private $species;
    private $adopt;

    public function __construct($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM animal WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $this->id = $row['id'];
        $this->name = $row['nom'];
        $this->owner_username = $row['proprietaire_username'];
        $this->age = $row['age'];
        $this->gender = $row['sexe'];
        $this->avatar = $row['avatar'];
        $this->characteristics = $row['caracteristiques'];
        $this->species = $row['espece'];
        $this->adopt = $row['adopter'];
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

    public function loadAvatar($conn) {
        $sql = "SELECT avatar FROM animal WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["avatar"];
        }

        return "Aucune image trouvée.";
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
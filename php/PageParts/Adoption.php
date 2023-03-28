<?php
class Adoption {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function adoptAnimal($animal_id, $adoptant_username) {
        // Vérifier si l'animal existe
        $stmt = $this->conn->prepare("SELECT * FROM animal WHERE id = ?");
        $stmt->bind_param("s", $animal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0) {
            return "L'animal n'existe pas.";
        }

        // Vérifier si l'animal est déjà adopté
        $stmt = $this->conn->prepare("SELECT * FROM adoption WHERE animal_id = ?");
        $stmt->bind_param("s", $animal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return "L'animal est déjà adopté.";
        }

        // Récupérer le propriétaire de l'animal
        $stmt = $this->conn->prepare("SELECT maitre_username FROM animal WHERE id = ?");
        $stmt->bind_param("s", $animal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $maitre_username = $row['maitre_username'];

        // Insérer la notification dans la table notification
        $date = date("Y-m-d H:i:s");
        $vue = false;
        $stmt = $this->conn->prepare("INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $maitre_username, $date, $vue);
        $stmt->execute();

        // Récupérer l'id de la notification insérée
        $notification_id = $stmt->insert_id;

        // Insérer la notification d'adoption dans la table notification_adoption
        $etat = "en attente";
        $stmt = $this->conn->prepare("INSERT INTO notification_adoption (notification_id, animal_id,  adoptant_username, etat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $notification_id, $animal_id, $adoptant_username, $etat);
        $stmt->execute();

        return "L'adoption a été effectuée avec succès.";
    }
}

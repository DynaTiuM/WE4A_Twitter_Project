<?php

abstract class Profile
{
    protected $username;
    protected $conn;
    protected $db;
    protected int $numberOfMessages;
    protected $profileUser;

    public function __construct($conn, $username, $db) {
        $this->conn = $conn;
        $this->username = $username;
        $this->db = $db;
    }

    public function displayNumMessages() {
        ?>
        <div style = "margin-left: 1vw; font-family: 'Plus Jakarta Sans', sans-serif;">
            <p style = "margin-top: 0; padding-top: 0; font-size: 0.9vw;"><?php echo $this->numberOfMessages?> Messages</p>
        </div>
        <?php
    }

    public static function determineProfileType($conn, $username) {
        $count = 0;
        // Vérifie si le nom d'utilisateur appartient à un utilisateur
        $query = "SELECT COUNT(*) FROM utilisateur WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $count = $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            return 'utilisateur';
        } else {
            return 'animal';
        }
    }


    public function likedMessages() {
        $query = "SELECT message.* FROM message
              JOIN like_message ON message.id = like_message.message_id
              WHERE like_message.utilisateur_username = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
            return $messageIds;
        } else {
            echo '<br><h4>Ce profil n\'a aimé aucun message</h4>';
        }
    }
    public function setNumberOfMessages($number) {
        $this->numberOfMessages = $number;
    }

    public function getUser() {
        return $this->profileUser;
    }

    public function profileMessagesAndAnswers($isMessage) {
        $query = $this->getUser()->queryMessagesAndAnswers($isMessage);
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
            return $messageIds;
        }
        else {
            if ($isMessage) {
                echo '<br><h4>Ce profil ne contient aucun message</h4>';
            } else {
                echo '<br><h4>Ce profil n\'a répondu à aucun message</h4>';
            }
        }
    }


    abstract public function displayProfile(); // Méthode abstraite à implémenter dans les classes filles
    abstract protected function displayButton($loginStatus, $globalUser); // Méthode abstraite à implémenter dans les classes filles
}
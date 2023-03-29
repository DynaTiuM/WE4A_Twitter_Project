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

        $this->profileUser = new User($this->conn, $this->db);
        $this->profileUser->setUsername($username);
    }

    public function displayNumMessages() {
        ?>
        <div style = "margin-left: 1vw; font-family: 'Plus Jakarta Sans', sans-serif;">
            <p style = "margin-top: 0; padding-top: 0; font-size: 0.9vw;"><?php echo $this->numberOfMessages?> Messages</p>
        </div>
        <?php
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

    abstract public function profilMessagesAndAnswers($isMessage);

    abstract public function displayProfile(); // Méthode abstraite à implémenter dans les classes filles
}
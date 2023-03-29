<?php

require_once("../Classes/Profile.php");
class AnimalProfile extends Profile {
    public function displayProfile() {
    }

    public function profilMessagesAndAnswers($isMessage) {
        $query = "SELECT message.*
                FROM message
                    JOIN message_animaux
                        ON message.id = message_animaux.message_id
                    JOIN animal
                        ON animal.id = message_animaux.animal_id
                WHERE animal.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $message = Message::createMessageFromRow($this->conn, $row);
                $message->displayContent($row);
            }
        }
        else {
            echo '<br><h4>Ce profil ne contient aucun message</h4>';
        }
    }

}
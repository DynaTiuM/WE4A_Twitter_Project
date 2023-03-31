<?php

class Notification
{
    private $id;
    private $conn;
    private $db;

    function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    public function displayNotification($table) {
        $notificationType = $table["notif_type"];
        switch ($notificationType) {
            case 'new_follower':
                return $this->displayNewFollowerNotification($table);
            case 'like':
                return $this->displayLikeNotification($table);
            case 'message':
                return $this->displayMessageNotification($table);
            case 'adoption':
                return $this->displayAdoptionNotification($table);
            default:
                return '';
        }
    }

    private function displayNewFollowerNotification($notificationData) {

    }

    private function displayLikeNotification($notificationData) {
    }

    private function displayMessageNotification($notificationData) {
        require_once ("../Classes/Message.php");
        $messageId = $notificationData['id'];
        $message = new Message($this->conn, $this->db);
        $message->loadMessageById($messageId);
        $message->displayContent();
    }

    private function displayAdoptionNotification($notificationData) {
    }

    function numNotifications($username) {
        global $conn;
        $query = "SELECT COUNT(*) FROM notification WHERE utilisateur_username = ? AND vue = ?";
        $stmt = $conn->prepare($query);
        $read = 0;
        $stmt->bind_param("si", $username, $read);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_Column();

    }

    public function getNotifications($username) {
        $query = "SELECT * FROM notification WHERE utilisateur_username = ? ORDER BY vue ASC, date DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifData = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notifId = $row['id'];
                $singleNotifData = $this->getNotificationData($notifId);
                if ($singleNotifData && $singleNotifData->num_rows > 0) {
                    $notifData[] = $singleNotifData->fetch_assoc();
                }
            }
        }
        return !empty($notifData) ? $notifData : null;
    }


    private function getNotificationData($notifId) {
        // Check for message notification
        $queryMessage = "SELECT 'message' AS notif_type, notification.vue, message.*, utilisateur.*,
                 GROUP_CONCAT(animal.nom SEPARATOR ', ') AS animal_names
                 FROM notification_message
                 INNER JOIN message ON notification_message.message_id = message.id
                 LEFT JOIN message_animaux ON message.id = message_animaux.message_id
                 LEFT JOIN animal ON message_animaux.animal_id = animal.id
                 INNER JOIN utilisateur ON message.auteur_username = utilisateur.username
                 INNER JOIN notification ON notification_message.notification_id = notification.id
                 WHERE notification_message.notification_id = ?
                 GROUP BY message.id;";
        $stmtMessage = $this->conn->prepare($queryMessage);
        $stmtMessage->bind_param("i", $notifId);
        $stmtMessage->execute();
        $resultMessage = $stmtMessage->get_result();

        if ($resultMessage->num_rows > 0) {
            return $resultMessage;
        }

        // Check for adoption notification
        // Add your adoption notification query here

        // Check for like notification
        // Add your like notification query here

        // Check for follow notification
        // Add your follow notification query here

        return null;
    }

    public function createNotificationsForFollowers($author_username, $message_id) {
        $followersQuery = "SELECT utilisateur_username FROM suivre WHERE suivi_id_utilisateur = ?;";
        $followersStmt = $this->conn->prepare($followersQuery);
        $followersStmt->bind_param("s", $author_username);
        $followersStmt->execute();
        $followersResult = $followersStmt->get_result();

        if ($followersResult->num_rows > 0) {
            while ($row = $followersResult->fetch_assoc()) {
                $follower_username = $row['utilisateur_username'];
                $this->createNotificationForMessage($follower_username, $message_id);
            }
        }
    }

    private function createNotificationForMessage($username, $message_id) {
        $insertQuery = "INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, NOW(), FALSE);";
        $insertStmt = $this->conn->prepare($insertQuery);
        $insertStmt->bind_param("s", $username);
        $insertStmt->execute();

        $notifId = $this->conn->insert_id;

        $assocQuery = "INSERT INTO notification_message (notification_id, message_id, utilisateur_username) VALUES (?, ?, ?);";
        $assocStmt = $this->conn->prepare($assocQuery);
        $assocStmt->bind_param("iis", $notifId, $message_id, $username);
        $assocStmt->execute();
    }


    public function createFollowNotification($followerUsername, $followedUsername) {
        $query = "INSERT INTO notification (utilisateur_username, date) VALUES (?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $followedUsername);
        $stmt->execute();
        $notificationId = $stmt->insert_id;

        $query = "INSERT INTO notification_suivre (notification_id, suiveur_username) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $notificationId, $followerUsername);
        $stmt->execute();
    }

    public function createLikeNotification($likerUsername, $likedMessageId) {
        $query = "SELECT auteur_username FROM message WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $likedMessageId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $likedUserUsername = $row['auteur_username'];

        $query = "INSERT INTO notification (utilisateur_username, date) VALUES (?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $likedUserUsername);
        $stmt->execute();
        $notificationId = $stmt->insert_id;

        $query = "INSERT INTO notification_like (notification_id, likeur_username, message_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isi", $notificationId, $likerUsername, $likedMessageId);
        $stmt->execute();
    }

    public function markNotificationAsRead($message_id) { /* ... */ }
}
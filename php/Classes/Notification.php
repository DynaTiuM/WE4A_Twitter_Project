<?php

class Notification
{
    private $conn;
    private $db;

    function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    public function displayNotification($table) {
        $notificationType = $table["notif_type"];
        switch ($notificationType) {
            case 'follow':
                $this->displayNewFollowerNotification($table);
                break;
            case 'like':
                $this->displayLikeNotification($table);
                break;
            case 'message':
                $this->displayMessageNotification($table);
                break;
            case 'adoption':
                $this->displayAdoptionNotification($table);
                break;
        }
        return null;
    }

    private function displayLikeNotification($notificationData) {
        $userId = $notificationData['username'];
        $idMessage = $notificationData['id'];

        $followerUser = User::getInstanceById($this->conn, $this->db, $userId);
        $avatar = $followerUser->getAvatarEncoded64();
        ?>
        <form method="post" class="likeRedirectionForm" data-id="<?php echo $idMessage; ?>">
            <input type="hidden" name="notification-id" value="<?php echo $idMessage; ?>">
            <input type="submit" class="invisibleFile">
            <div style="display: flex;" onclick="submitLikeRedirection(event);">
                <label>
                    <img class="image-modification" style="width: 4vw; height: 4vw; margin: 1vw;" src="data:image/jpeg;base64,<?php echo $avatar; ?>" alt="Image de profil">
                </label>
                <p style="margin-top: 2vw; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.2vw"> <?php echo $userId ?> a aimé l'un de vos messages</p>

            </div>
        </form>
        <script>
            function submitLikeRedirection(event) {
                const likeRedirectionForm = event.target.closest('.likeRedirectionForm');
                const id_message = likeRedirectionForm.dataset.id;

                const queryString = `?answer=${id_message}`;

                likeRedirectionForm.action = '../PageParts/explorer.php' + queryString;
                likeRedirectionForm.submit();
            }
        </script>

        <?php
    }

    private function displayMessageNotification($notificationData) {
        require_once("../Classes/Message.php");
        $messageId = $notificationData['id'];
        $message = new Message($this->conn, $this->db);

        $message->loadMessageById($messageId);
        $message->displayContent();

    }

    private function displayNewFollowerNotification($notificationData) {
        $userId = $notificationData['username'];

        $followerUser = User::getInstanceById($this->conn, $this->db, $userId);
        $avatar = $followerUser->getAvatarEncoded64();
        ?>
        <form method="post" id="profileRedirectionForm">
            <input type="hidden" name="notification-id" value="<?php echo $notificationData['id']; ?>">
            <input type="submit" class="invisibleFile">
            <div style="display: flex;" id="profileRedirection" data-username="<?php echo $userId; ?>" onclick="submitProfileRedirectionForm();">
                <label>
                    <img class="image-modification" style="width: 4vw; height: 4vw; margin: 1vw;" src="data:image/jpeg;base64,<?php echo $avatar; ?>" alt="Image de profil">
                </label>
                <p style="margin-top: 2vw; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.2vw"> <?php echo $userId ?> vous suit dorénavant</p>
            </div>
        </form>
        <script>
            function submitProfileRedirectionForm() {
                const profileRedirection = document.getElementById('profileRedirection');
                const username = profileRedirection.dataset.username;

                // Construire la chaîne de requête GET
                const queryString = `?username=${username}`;

                // Soumettre le formulaire avec la chaîne de requête GET
                const profileRedirectionForm = document.getElementById('profileRedirectionForm');
                profileRedirectionForm.action = '../PageParts/profile.php' + queryString;
                profileRedirectionForm.submit();
            }
        </script>
        <?php
    }

    public static function setRead($conn, $notificationId) {
        // Mettre à jour la notification
        $query = "UPDATE notification SET vue = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
    }

    private function displayAdoptionNotification($notificationData) {
    }

    function numNotifications($username) {
        global $conn;
        $query = "SELECT COUNT(*) AS count FROM notification WHERE utilisateur_username = ? AND vue = ?";
        $stmt = $conn->prepare($query);
        $read = 0;
        $stmt->bind_param("si", $username, $read);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['count'];
        }

        return 0;
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
                $read = $row['vue'];
                $singleNotifData = $this->getNotificationData($notifId);
                if ($singleNotifData && $singleNotifData->num_rows > 0) {
                    $notifData[] = [$singleNotifData->fetch_assoc(), $read];
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

        // Check for like notification
        $queryLike = "SELECT 'like' AS notif_type, notification.*, utilisateur_likeur.*, utilisateur_liké.*, message.*
                FROM notification_like
                INNER JOIN like_message ON notification_like.message_id = like_message.message_id
                INNER JOIN utilisateur AS utilisateur_likeur ON like_message.utilisateur_username = utilisateur_likeur.username
                INNER JOIN message ON like_message.message_id = message.id
                INNER JOIN utilisateur AS utilisateur_liké ON message.auteur_username = utilisateur_liké.username
                INNER JOIN notification ON notification_like.notification_id = notification.id
                WHERE notification_like.notification_id  = ?
                GROUP BY like_message.id;";
        $stmtFollow = $this->conn->prepare($queryLike);
        $stmtFollow->bind_param("i", $notifId);
        $stmtFollow->execute();
        $resultFollow = $stmtFollow->get_result();

        if ($resultFollow->num_rows > 0) {
            return $resultFollow;
        }

        // Check for follow notification
        $queryFollow = "SELECT 'follow' AS notif_type, notification.*, utilisateur_suiveur.*, utilisateur_suivi.*,
                GROUP_CONCAT(animal.nom SEPARATOR ', ') AS animal_names
                FROM notification_suivre
                INNER JOIN utilisateur AS utilisateur_suiveur ON notification_suivre.suiveur_username = utilisateur_suiveur.username
                INNER JOIN suivre ON notification_suivre.suivre_id = suivre.id
                INNER JOIN utilisateur AS utilisateur_suivi ON suivre.utilisateur_username = utilisateur_suivi.username
                    /* Ici nous ajoutons une jointure LEFT JOIN  si jamais il n'y a pas de correspondance sur la table animal */
                LEFT JOIN animal ON suivre.suivi_id_animal = animal.id
                INNER JOIN notification ON notification_suivre.notification_id = notification.id
                WHERE notification_suivre.notification_id = ?
                GROUP BY suivre.id;";
        $stmtFollow = $this->conn->prepare($queryFollow);
        $stmtFollow->bind_param("i", $notifId);
        $stmtFollow->execute();
        $resultFollow = $stmtFollow->get_result();

        if ($resultFollow->num_rows > 0) {
            return $resultFollow;
        }

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

    public function createNotificationForLike($username, $message_id) {
        $insertQuery = "INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, NOW(), FALSE);";
        $insertStmt = $this->conn->prepare($insertQuery);
        $insertStmt->bind_param("s", $username);
        $insertStmt->execute();

        $notifId = $this->conn->insert_id;

        $assocQuery = "INSERT INTO notification_like (notification_id, likeur_username, message_id) VALUES (?, ?, ?);";
        $assocStmt = $this->conn->prepare($assocQuery);
        $assocStmt->bind_param("isi", $notifId, $username, $message_id);
        $assocStmt->execute();
    }

    public static function getNotificationMessageByMessageId($conn, $messageId) {

        $stmt = $conn->prepare("SELECT n.id FROM notification n INNER JOIN notification_message nm ON n.id = nm.notification_id WHERE nm.message_id = ?");
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    public static function getNotificationLikeByMessageId($conn, $messageId) {
        $stmt = $conn->prepare("SELECT n.id FROM notification n INNER JOIN notification_like nl ON n.id = nl.notification_id WHERE nl.message_id = ?");
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    public function isAlreadySent($username, $messageId) {
        $stmt = $this->conn->prepare("SELECT id FROM notification INNER JOIN notification_like
                                        ON notification.id = notification_like.notification_id
                                        WHERE notification_like.message_id = ? AND notification_like.likeur_username = ?");
        $stmt->bind_param("is", $messageId, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        return ($result && $result->num_rows > 0);
    }


    public function createNotificationForFollow($followerUsername, $followedUsername, $followId) {
        $query = "INSERT INTO notification (utilisateur_username, date) VALUES (?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $followedUsername);
        $stmt->execute();
        $notificationId = $stmt->insert_id;

        $date = date("d M Y H:i:s");
        $query = "INSERT INTO notification_suivre (notification_id, suiveur_username, suivre_id, date) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isis", $notificationId, $followerUsername, $followId, $date);
        $stmt->execute();
    }

    public function deleteFollowNotifications($followId) {
        $query = "SELECT notification_id FROM notification_suivre WHERE suivre_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $followId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notificationId = $result->fetch_assoc()['notification_id'];

        $query = "DELETE FROM notification_suivre WHERE suivre_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $followId);
        $stmt->execute();

        $query = "DELETE FROM notification WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
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
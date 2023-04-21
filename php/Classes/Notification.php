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
        $notificationType = $table->notif_type;
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
            case 'answer':
                $this->displayAnswerNotification($table);
                break;
            case 'adoption':
                $this->displayAdoptionNotification($table);
                break;
        }
        return null;
    }

    private function displayAdoptionNotification($notificationData) {
        $adoptantPrenom = $notificationData->adoptant_prenom;
        $adoptantUsername = $notificationData->adoptant_username;
        $animalId = $notificationData->animal_id;
        $animalNom = $notificationData->animal_nom;
        $notificationId = $notificationData->notification_id;

        $adoptantAvatar = base64_encode($notificationData->adoptant_avatar);
        $animalAvatar = base64_encode($notificationData->animal_avatar);
        ?>
        <div style="display: flex;">
            <a href="profile.php?username=<?php echo $adoptantUsername; ?>">
                <img class="image-modification" style="width: 4vw; height: 4vw; margin: 1vw;" src="data:image/jpeg;base64,<?php echo $adoptantAvatar; ?>" alt="Image de l'adoptant">
            </a>
            <p style="margin-top: 2vw; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.2vw">
                <?php echo $adoptantPrenom; ?> souhaite adopter <?php echo $animalNom; ?>
            </p>
            <a href="profile.php?username=<?php echo $animalId; ?>">
                <img class="image-modification" style="width: 4vw; height: 4vw; margin: 1vw;" src="data:image/jpeg;base64,<?php echo $animalAvatar; ?>" alt="Image de l'animal">
            </a>
        </div>
        <?php
        if(!$this->isRead($notificationId)) {
            ?>
            <form method="post" action="">
                <input type="hidden" name="adoptant-username" value="<?php echo $adoptantUsername; ?>">
                <input type="hidden" name="animal-id" value="<?php echo $animalId; ?>">
                <input type="hidden" name="notification-id" value="<?php echo $notificationId; ?>">
                <div class = "button-container">
                    <button type="submit" class = "button-follow" name="adoption-status" value="acceptee">Accepter</button>
                    <button type="submit" class = "button-follow" name="adoption-status" value="refusee">Refuser</button>
                </div>
            </form>
            <?php
        }

    }


    private function displayLikeNotification($notificationData) {
        $userId = $notificationData->username;
        $idMessage = $notificationData->id;

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
        $messageId = $notificationData->id;
        $message = new Message($this->conn, $this->db);

        $message->loadMessageById($messageId);
        $message->displayContent();

    }

    private function displayAnswerNotification($notificationData) {
        require_once("../Classes/Message.php");
        $messageId = $notificationData->id;
        $userId = $notificationData->username;
        ?>
        <div style="display: flex; margin-left: 1vw" id="profileRedirection" data-username="<?php echo $userId; ?>">
            <p style="margin-top: 2vw; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.2vw"> <?php echo $userId ?> a répondu à l'un de vos messages</p>
        </div>
        <div style = "margin-left: 1.5vw; margin-top: -0.5vw">
            <?php
            $message = new Message($this->conn, $this->db);

            $message->loadMessageById($messageId);
            $message->displayContent();
            ?>
        </div>
<?php
    }

    private function displayNewFollowerNotification($notificationData) {
        $userId = $notificationData->username;

        $followerUser = User::getInstanceById($this->conn, $this->db, $userId);
        $avatar = $followerUser->getAvatarEncoded64();
        ?>
        <form method="post" id="profileRedirectionForm">
            <input type="hidden" name="notification-id" value="<?php echo $notificationData->id; ?>">
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

    public static function isAnswerNotification($conn, $user, $messageId) : bool {
        $query = "SELECT * FROM notification_reponse
                    INNER JOIN notification ON notification.id = notification_reponse.notification_id
                    WHERE notification_reponse.message_id = ? AND notification.utilisateur_username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $messageId, $user);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public static function setRead($conn, $notificationId) {
        // Mettre à jour la notification
        $query = "UPDATE notification SET vue = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
    }

    private function isRead($notificationId) {
        $query = "SELECT vue FROM notification WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['vue'] == 1;
        }
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
                if ($singleNotifData) {
                    $notifData[] = [$singleNotifData, $read];
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
            return $resultMessage->fetch_object();
        }

        // Check for adoption notification
        $queryAdoption = "SELECT 'adoption' AS notif_type, notification.id AS notification_id,
            adoptant.username AS adoptant_username, adoptant.prenom AS adoptant_prenom, adoptant.avatar AS adoptant_avatar,
            animal.id AS animal_id, animal.nom AS animal_nom, animal.avatar AS animal_avatar,
            organisation.username AS organisation_username, organisation.nom AS organisation_nom, organisation.prenom AS organisation_prenom, organisation.avatar AS organisation_avatar
        FROM notification_adoption
        INNER JOIN utilisateur AS adoptant ON notification_adoption.adoptant_username = adoptant.username
        INNER JOIN animal ON notification_adoption.animal_id = animal.id
        INNER JOIN utilisateur AS organisation ON animal.maitre_username = organisation.username
        INNER JOIN notification ON notification_adoption.notification_id = notification.id
        WHERE notification_adoption.notification_id = ?";
        $stmtFollow = $this->conn->prepare($queryAdoption);
        $stmtFollow->bind_param("i", $notifId);
        $stmtFollow->execute();
        $resultFollow = $stmtFollow->get_result();

        if ($resultFollow->num_rows > 0) {
            return $resultFollow->fetch_object();
        }
        // Check for answer notification
        $queryAnswer = "SELECT 'answer' AS notif_type, notification.*, utilisateur_repondeur.*, message.*
                    FROM notification_reponse
                    INNER JOIN notification ON notification_reponse.notification_id = notification.id
                    INNER JOIN utilisateur AS utilisateur_repondeur ON notification_reponse.repondeur_username = utilisateur_repondeur.username
                    INNER JOIN message ON message.id = notification_reponse.message_id
                    WHERE notification_reponse.notification_id = ?
                    GROUP BY message.id;";
        $stmtFollow = $this->conn->prepare($queryAnswer);
        $stmtFollow->bind_param("i", $notifId);
        $stmtFollow->execute();
        $resultFollow = $stmtFollow->get_result();

        if ($resultFollow->num_rows > 0) {
            return $resultFollow->fetch_object();
        }
        // Check for like notification
        $queryLike = "SELECT 'like' AS notif_type, notification.*, utilisateur_likeur.*, message.*
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
            return $resultFollow->fetch_object();
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
            return $resultFollow->fetch_object();
        }
/*
        // Check for adoption notification
        $queryAdoption = "SELECT 'adoption' AS notif_type, 
            adoptant.username AS adoptant_username, adoptant.nom AS adoptant_nom, adoptant.prenom AS adoptant_prenom, adoptant.avatar AS adoptant_avatar,
            animal.id AS animal_id, animal.nom AS animal_nom, animal.avatar AS animal_avatar,
            organisation.username AS organisation_username, organisation.nom AS organisation_nom, organisation.prenom AS organisation_prenom, organisation.avatar AS organisation_avatar
        FROM notification_adoption
        INNER JOIN utilisateur AS adoptant ON notification_adoption.adoptant_username = adoptant.username
        INNER JOIN animal ON notification_adoption.animal_id = animal.id
        INNER JOIN utilisateur AS organisation ON animal.maitre_username = organisation.username
        INNER JOIN notification ON notification_adoption.notification_id = notification.id
        WHERE notification_adoption.notification_id = ?;
        ";

        $stmtAdoption = $this->conn->prepare($queryAdoption);
        $stmtAdoption->bind_param("i", $notifId);
        $stmtAdoption->execute();
        $resultAdoption = $stmtAdoption->get_result();

        if ($resultAdoption->num_rows > 0) {
            return $resultAdoption->fetch_object();

        }
*/
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

    public function createNotificationForAnswer($username, $message_id) {
        $query = "SELECT parent.auteur_username, answer.parent_message_id FROM message AS parent INNER JOIN message AS answer
                    ON parent.id = answer.parent_message_id
                    WHERE answer.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $author_username = $row['auteur_username'];
        $parent_message_id = $row['parent_message_id'];

        $insertQuery = "INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, NOW(), FALSE);";
        $insertStmt = $this->conn->prepare($insertQuery);
        $insertStmt->bind_param("s", $author_username);
        $insertStmt->execute();

        $notifId = $this->conn->insert_id;

        $assocQuery = "INSERT INTO notification_reponse (notification_id, repondeur_username, message_id, parent_message_id) VALUES (?, ?, ?, ?);";
        $assocStmt = $this->conn->prepare($assocQuery);
        $assocStmt->bind_param("isii", $notifId, $username, $message_id, $parent_message_id);
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

    public static function getNotificationTypeByMessageId($conn, $messageId, $type) {

        $stmt = $conn->prepare("SELECT n.id FROM notification n INNER JOIN notification_$type nt ON n.id = nt.notification_id WHERE nt.message_id = ?");
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

        $query = "INSERT INTO notification_suivre (notification_id, suiveur_username, suivre_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isi", $notificationId, $followerUsername, $followId);
        $stmt->execute();
    }

    public function createNotificationAdoption($adopter_username, $animal_id) {
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
        $read = false;
        $stmt = $this->conn->prepare("INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $maitre_username, $date, $read);
        $stmt->execute();

        // Récupérer l'id de la notification insérée
        $notification_id = $stmt->insert_id;

        // Insérer la notification d'adoption dans la table notification_adoption
        $etat = "en attente";
        $stmt = $this->conn->prepare("INSERT INTO notification_adoption (notification_id, animal_id,  adoptant_username, etat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $notification_id, $animal_id, $adopter_username, $etat);
        $stmt->execute();

        return "L'adoption a été effectuée avec succès.";
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

    public function acceptAdoption($notificationId) : void {
        $query = "UPDATE notification_adoption
              SET etat = 'acceptee'
              WHERE notification_id = ?;";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $query = "UPDATE animal
              SET maitre_username = (
                  SELECT adoptant_username
                  FROM notification_adoption
                  WHERE notification_id = ?
              )
              WHERE id = (
                  SELECT animal_id
                  FROM notification_adoption
                  WHERE notification_id = ?
              );";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $notificationId, $notificationId);
        $stmt->execute();
    }

    public function refuseAdoption($notificationId) : void {
        $query = "UPDATE notification_adoption
              SET etat = 'refusee'
              WHERE notification_id = ?;";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
    }

}
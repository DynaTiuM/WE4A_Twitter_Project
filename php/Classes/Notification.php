<?php

class Notification {
    private $conn;
    private $db;

    /**
     * Constructeur de base de la classe Notification
     *
     * @param $conn
     * @param $db
     */
    function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    /**
     * Méthode permettant d'afficher n'importe quel type de notification
     *
     * @param $table
     * @return null
     */
    public function displayNotification($table) {
        // Il est essentiel de récupérer l'alias notif_type renvoyé par la requete SQL pour déterminer le type de notification à afficher
        $notificationType = $table->notif_type;
        // Selon la valeur de notificationType, on affiche la notification différemment :
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

    /**
     * Méthode permettant d'afficher la notification d'adoption
     *
     * @param $notificationData
     * @return void
     */
    private function displayAdoptionNotification($notificationData) {
        // On récupère toutes les colonnes essentielles que la requete SQL renvoie que l'on souhaite pour l'affichage de la notification :
        $adoptantPrenom = $notificationData->adoptant_prenom;
        $adoptantUsername = $notificationData->adoptant_username;
        $animalId = $notificationData->animal_id;
        $animalNom = $notificationData->animal_nom;
        $notificationId = $notificationData->notification_id;

        $adoptantAvatar = base64_encode($notificationData->adoptant_avatar);
        $animalAvatar = base64_encode($notificationData->animal_avatar);

        // Puis on réalise un affichage classique en HTML de la notification :
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
        // De plus, si la notification n'est pas encore lue, on laisse l'organisation le choix d'accepter ou non la demande d'adoption :
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


    /**
     * Méthode permettant d'afficher les notifications de like
     *
     * @param $notificationData
     * @return void
     */
    private function displayLikeNotification($notificationData) {
        // On récupère les informations que l'on souhaite de la requete SQL
        $userId = $notificationData->username;
        $idMessage = $notificationData->id;

        $followerUser = User::getInstanceById($this->conn, $this->db, $userId);
        $avatar = $followerUser->getAvatarEncoded64();
        // On les affiche :
        ?>
        <form method="post" class="likeRedirectionForm" data-id="<?php echo $idMessage; ?>">
            <input type="hidden" name="notification-id" value="<?php echo $idMessage; ?>">
            <input type="submit" class="invisibleFile">
            <!-- Ici, on ajoute un submitLikeRedirection, car on souhaite que lorsque l'on clique sur le message, le formulaire soit envoyé
            Or, en html, on ne peut pas renvoyer un formulaire avec un url ?answer=
            On peut le réaliser seulement avec une balise <a>. Et si on opte cette méthode, la valeur de l'idMessage ne sera pas envoyée
            via le formulaire, car <a> et <form> sont indépendants. Ainsi un crée une fonction JavaScript : -->
            <div style="display: flex;" onclick="submitLikeRedirection(event);">
                <label>
                    <img class="image-modification" style="width: 4vw; height: 4vw; margin: 1vw;" src="data:image/jpeg;base64,<?php echo $avatar; ?>" alt="Image de profil">
                </label>
                <p style="margin-top: 2vw; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.2vw"> <?php echo $userId ?> a aimé l'un de vos messages</p>
            </div>
        </form>
        <script>
            /**
             * Méthode permettant d'envoyer l'utilisateur vers un lien précis, en récupérant les informations d'un formulaire FORM
             *
             * @param event
             */
            function submitLikeRedirection(event) {
                // La méthode est simple, on vise tout simplement notre formulaire possédant la classe likeRedirectionForm
                const likeRedirectionForm = event.target.closest('.likeRedirectionForm');
                // On peut ainsi récupérer l'id du message, grâce à un dataset, qui est ici nommé data-id :
                const id_message = likeRedirectionForm.dataset.id;
                // On initialise un string permettant d'aller à l'URL de notre destination + l'id du message
                const queryString = `?answer=${id_message}`;
                // On concatène les deux strings, ainsi, le formulaire ne renverra pas seulement à explorer.php mais à explorer.php?answer=id_message
                likeRedirectionForm.action = '../PageParts/explorer.php' + queryString;
                // Finalement, on soumets le formulaire lors du clique du message
                likeRedirectionForm.submit();
            }
        </script>

        <?php
    }

    /**
     * Méthode permattant d'afficher la notification d'un message
     *
     * @param $notificationData
     * @return void
     */
    private function displayMessageNotification($notificationData) {
        // Dans cette méthode, on se contente simplement de réutiliser les méthodes d'affichage d'un message que l'on possède déjà :
        require_once("../Classes/Message.php");
        $messageId = $notificationData->id;
        $message = new Message($this->conn, $this->db);

        // On charge le message en fonction de son id
        $message->loadMessageById($messageId);
        // Et on l'affiche tout simplement
        $message->displayContent();

    }

    /**
     * Méthode permettant d'afficher la notification de réponse à un message
     *
     * @param $notificationData
     * @return void
     */
    private function displayAnswerNotification($notificationData) {
        // Ce méthode se contente simplement d'afficher le message de la réponse à un message de la meme manière que la méthode displayMessageNotification
        // Cependant, il est juste ajouté un petit message avant l'affichage du message, pour indiquer à l'utilisateur qu'il s'agit d'un utilisateur qui a répondu à son message
        require_once("../Classes/Message.php");
        $messageId = $notificationData->id;
        $userId = $notificationData->username;
        ?>
        <div style="display: flex; margin-left: 1vw" id="profileRedirection" data-username="<?php echo $userId; ?>">
            <a href = "../PageParts/explorer.php?answer=<?php echo $messageId?>"><p style="margin-top: 2vw; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.2vw"> <?php echo $userId ?> a répondu à l'un de vos messages</p></a>
        </div>
        <div style = "margin-left: 1.5vw; margin-top: -0.5vw">
            <?php
            // Etant donné que notre méthode d'affichage notification de message existe déjà, on se contente tout simplement de l'utiliser :
            $this->displayMessageNotification($notificationData);
            ?>
        </div>
<?php
    }

    /**
     * Méthode permettant d'afficher une notification de nouveau follower
     *
     * @param $notificationData
     * @return void
     */
    private function displayNewFollowerNotification($notificationData) {
        // On récupère les informations nécessaires à l'affichage de notre notification :
        $userId = $notificationData->username;
        $followerUser = User::getInstanceById($this->conn, $this->db, $userId);
        $avatar = $followerUser->getAvatarEncoded64();
        // On réalise un petit affichage de la notification
        // Il est important de noter que la méthode de redirection de message et récupération de formulaire est identique à celle de la notification de like/
        // Regardez plus en détail les commentaires de cette méthode pour comprendre davantage
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
                // On récupère les informations de l'id profileRedirection
                const profileRedirection = document.getElementById('profileRedirection');
                // On peut ainsi en extraire le username de l'utilisateur qui follow la personne
                const username = profileRedirection.dataset.username;

                // On construit la chaien de requete GET
                const queryString = `?username=${username}`;

                // Puis on souhaite la soumettre dans le formulaire quand l'utilisateur est redirigé :
                const profileRedirectionForm = document.getElementById('profileRedirectionForm');
                // On réalise de la meme méthode que la notification like, la concaténation :
                profileRedirectionForm.action = '../PageParts/profile.php' + queryString;
                // On soumet le formulaire
                profileRedirectionForm.submit();
            }
        </script>
        <?php
    }

    /**
     * Méthode qui vérifie si une notification est une notification de réponse de message
     *
     * @param $conn
     * @param $user
     * @param $messageId
     * @return bool
     */
    public static function isAnswerNotification($conn, $user, $messageId) : bool {
        // On réalise la requete SQL permettant de récupérer une ligne de notification_reponse, que l'on lie à la table notification
        // pour réaliser la condition de vérification par rapport à l'id du message et l'utilisateur
        // Ainsi, si par rapport à un message donné, à l'utilisateur qui regarde ses notifications, il y a une ligne de récupérée, il s'agit nécessairement d'une notification de message
        $query = "SELECT * FROM notification_reponse
                    INNER JOIN notification ON notification.id = notification_reponse.notification_id
                    WHERE notification_reponse.message_id = ? AND notification.utilisateur_username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $messageId, $user);
        $stmt->execute();
        $result = $stmt->get_result();
        // Donc s'il y a plus d'une ligne de récupérée, alors on retourne vrai
        if($result->num_rows > 0) {
            return true;
        }
        // Sinon, on retourne faux
        return false;
    }

    /**
     * Méthode qui met à jour une notification en vue
     *
     * @param $conn
     * @param $notificationId
     * @return void
     */
    public static function setRead($conn, $notificationId) {
        // Cette requête SQL simple permet tout simplement de mettre une notification en vue en fonction de son id
        $query = "UPDATE notification SET vue = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
    }

    /**
     * Méthode qui vérifie si une notification est lue
     *
     * @param $notificationId
     * @return bool|void
     */
    private function isRead($notificationId) {
        // Requete SQL simple permettant de sélectionner la colonne vue de la table notification par rapport à un id
        $query = "SELECT vue FROM notification WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();

        $result = $stmt->get_result();

        // S'il y a au moins une ligne de récupérée
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // On retourne si la notification est vue ou non
            return $row['vue'] == 1;
        }
    }

    /**
     * Méthode permettant de récupérer le nombre de notifications d'un message
     *
     * @param $username
     * @return int|mixed
     */
    function numNotifications($username) {
        // On crée simplement une requete permettant de récupérer le nombre de notifications grâce à la propriété COUNT()
        // Avec une condition où l'utilisateur est l'utilisateur qui navigue sur le site et où la vue est 0 (donc la notification n'est pas encore lue)
        $query = "SELECT COUNT(*) AS count FROM notification WHERE utilisateur_username = ? AND vue = ?";
        $stmt = $this->conn->prepare($query);
        $read = 0;
        $stmt->bind_param("si", $username, $read);
        $stmt->execute();
        $result = $stmt->get_result();

        // S'il y a plus d'une ligne de récupéré :
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // On retourne le nombre de lignes (donc de notifications)
            return $row['count'];
        }

        // Sinon, si aucune ligne n'est récupéré (donc que l'utilisateur a soit lu toutes ses notifications, soit il n'en a pas, on retourne ° :
        return 0;
    }

    /**
     * Méthode permettant de récupérer les notifications d'un utilisateur
     *
     * @param $username
     * @return array|null
     */
    public function getNotifications($username) {
        // Cette requete SQL récupère toutes les notifications d'un utilisateur et le classe par ordre de vue, puis par ordre de date
        // En effet, les notifications lues apparaissent à la fin, puis apparaissent les notifications les plus récentes aux plus anciennes
        $query = "SELECT * FROM notification WHERE utilisateur_username = ? ORDER BY vue ASC, date DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifData = array();

        // S'il y a au moins une notification
        if ($result->num_rows > 0) {
            // Tant que il y a une ligne :
            while ($row = $result->fetch_assoc()) {
                // On récupère l'id de la notification ainsi que son état (lue ou non)
                $notifId = $row['id'];
                $read = $row['vue'];
                // De plus, on appelle une méthode spécifique privée de cette classe, qui permet de récupérer toutes les données nécessaires selon le type de la notification
                // Cela se réalise grâce à l'information simple de la notification de l'id
                $singleNotifData = $this->getNotificationData($notifId);
                // Si une ligne est récupérée :
                if ($singleNotifData) {
                    // On stocke les informations de singleNotifDate et read qui seront utilisées pour l'affichage de la notification
                    $notifData[] = [$singleNotifData, $read];
                }
            }
        }
        return !empty($notifData) ? $notifData : null;
    }


    /**
     * Méthode permettant de récupérer une ligne de notification de la base de données en fonction de son type de notification
     *
     * @param $notifId
     * @return null
     */
    private function getNotificationData($notifId) {
        /* Il s'agit de la méthode la plus complexe du projet.
        /* Elle est décomposée en plusieurs requetes SQL, qui récupèrent toutes les informations nécessaires pour l'affichage d'une notification
        /* Pour ce faire, chaque requete est réalisée au fur et à mesure, et lorsqu'une ligne est trouvée, on la retourne, arretant ainsi la méthode
        /*
        /* Tout d'abord, on cherche s'il s'agit d'une notification de message, par rapport à l'id de la notification
        /* Dans le cas où une ligne est trouvée, on retourne juste toute les informations de la requete
        /* La requete message a besoin de récupérer une colonne nommée notif_type, qui sera utilisée par la suite pour déterminer le type de notification à afficher dans la méthode displayNotification()
        /* Il est par la suite nécessaire de récupérer la colonne vue de notification pour savoir si elle a été lue, de récupérer toutes les informations du message, de l'utilisateur et des animaux qui sont mentionnés
        /* dans le message. Pour regrouper tous les animaux, on les concatène à l'aide du séparateur ',' et on nomme cette concaténation animal_names grâce à un alias
        /* Tous les inner join présent permettent de lier les tables entre-elles grâce à leurs ids communs respectifs
        /* L'important ici est de noter les LEFT JOIN, qui permettent de récupérer des résultats même s'il n'y a aucun animal mentionné. En effet, un message peut ne pas posséder d'animaux !
        /* Au final, la clause importante de vérification est le WHERE avec la notification de message qui doit correspondre à l'id de la notification.
        /* Enfin, on groupe tout par id de message
        */
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
            // Finalement, on retourne le résultat contenant toutes les informations, sous forme d'objet, permettant d'accéder aux colonnes grâce à la notation "->" (cela évite de travailler avec des tableaux)
            return $resultMessage->fetch_object();
        }

        // On réalise le meme mécanisme pour la requete SQL d'adoption.
        // Ici, de nombreux alias sont ajoutés pour pouvoir différencier les colonnes possédants le meme nom (comme la colonne ID de la table notification et la colonne ID de la table animal)
        // Le mécanisme est globalement le même, sauf qu'on étudie par rapport à la table notification_adoption dans ce cas-ci
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
            // De nouveau, on retourne toutes les informations
            return $resultFollow->fetch_object();
        }

        // On réalise la meme opération pour la table notification_reponse, qui est plus simple que les autres
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
        // Encore une fois, on utilise le meme système permettant de récupérer le maximum d'informations sur le message, l'utilisateur qui a liké le message, et les attributs de notification (lue ou non, l'auteur du message etc)
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

        // La récupération d'informations de suivi d'un utilisateur reprendre le meme mécanisme que les requetes précédentes
        $queryFollow = "SELECT 'follow' AS notif_type, notification.*, utilisateur_suiveur.*, utilisateur_suivi.*
                FROM notification_suivre
                INNER JOIN utilisateur AS utilisateur_suiveur ON notification_suivre.suiveur_username = utilisateur_suiveur.username
                INNER JOIN suivre ON notification_suivre.suivre_id = suivre.id
                INNER JOIN utilisateur AS utilisateur_suivi ON suivre.utilisateur_username = utilisateur_suivi.username
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

        // Dans le cas où aucune notification de n'importe quel type n'est trouvée, on retourne tout simplement aucune ligne
        return null;
    }

    /**
     * Méthode permettant de créer une nouvelle notification de suivi
     *
     * @param $author_username
     * @param $message_id
     * @return void
     */
    public function createNotificationsForFollowers($author_username, $message_id) {
        // Cette requete SQL permet de récupérer l'utilisateur que l'utilisateur suit, pour par la suite envoyer une notification à l'utilisateur l'informant qu'il a un nouveau follower
        $query = "SELECT utilisateur_username FROM suivre WHERE suivi_id_utilisateur = ?;";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $author_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($stmt->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // On récupère ainsi simplement le nom de l'utilisateur qui a recu un nouveau follower
                $follower_username = $row['utilisateur_username'];
                // Et on lui crée sa notification
                $this->createNotificationForMessage($follower_username, $message_id);
            }
        }
    }

    /**
     * Méthode permettant de créer une notification de message
     *
     * @param $username
     * @param $message_id
     * @return void
     */
    private function createNotificationForMessage($username, $message_id) {
        // Tout d'abord, il est nécessaire de commencer à créer une notification générale, peu importe son type, en initialisant l'utilisateur qui a créé le message, la date actuelle et la valeur de vue à false (donc non vue)
        $query = "INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, NOW(), FALSE);";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // On récupère l'id de la nouvelle notification insérée
        $notifId = $this->conn->insert_id;

        // On peut donc créer la notification spécifique notification_message grâce à la nouvelle ID de notification crée
        // Et on peut insérer les informations essentielles de notification de message, c'est à dire à quel message il est rattaché et à quel utilisateur
        $query = "INSERT INTO notification_message (notification_id, message_id, utilisateur_username) VALUES (?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iis", $notifId, $message_id, $username);
        $stmt->execute();
    }

    /**
     * Méthode permettant de créer une notification de réponse
     *
     * @param $username
     * @param $message_id
     * @return void
     */
    public function createNotificationForAnswer($username, $message_id) {
        // Il est nécessaire de d'abord créer une requete SQL permettant de récupérer l'auteur d'un message ainsi que le message parent par rapport à l'id d'un message
        $query = "SELECT parent.auteur_username, answer.parent_message_id FROM message AS parent INNER JOIN message AS answer
                    ON parent.id = answer.parent_message_id
                    WHERE answer.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        // On stocke les valeurs des colonnes dans des variables
        $author_username = $row['auteur_username'];
        $parent_message_id = $row['parent_message_id'];

        // De la meme manière que les autres méthodes, on crée une nouvelle notification par rapport à l'auteur du message.
        // En effet, on souhaite envoyer la notification à l'auteur du message, car la réponse est liée au message parent, qui est le message de cet auteur
        $query = "INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, NOW(), FALSE);";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $author_username);
        $stmt->execute();

        $notifId = $this->conn->insert_id;

        // Finalement, on réalise à nouveau le meme procédé en récupérant l'id nouvellement ajouté, et en le spécifiant en notification reponse,
        // Avec comme informations le username de l'utilisateur qui a répondu au message, le message de l'utilisateur qui a répondu etc
        $query = "INSERT INTO notification_reponse (notification_id, repondeur_username, message_id, parent_message_id) VALUES (?, ?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isii", $notifId, $username, $message_id, $parent_message_id);
        $stmt->execute();
    }

    /**
     * Méthode permettant de créer une notification de like
     *
     * @param $username
     * @param $message_id
     * @return void
     */
    public function createNotificationForLike($username, $message_id) {
        // La méthode réalisée est exactement la même que les autres méthodes de création de notification
        $query = "INSERT INTO notification (utilisateur_username, date, vue) VALUES (?, NOW(), FALSE);";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $notifId = $this->conn->insert_id;

        // Ici, on crée une notification de like plus spécifique, en informant le nom de l'utilisateur qui a liké, ainsi que le message qui a été liké
        $query = "INSERT INTO notification_like (notification_id, likeur_username, message_id) VALUES (?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isi", $notifId, $username, $message_id);
        $stmt->execute();
    }

    /**
     * Méthode permettant de créer une notification de suivi
     *
     * @param $followerUsername
     * @param $followedUsername
     * @param $followId
     * @return void
     */
    public function createNotificationForFollow($followerUsername, $followedUsername, $followId) {
        // La méthode réalisée est exactement la même que les autres méthodes de création de notification
        $query = "INSERT INTO notification (utilisateur_username, date) VALUES (?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $followedUsername);
        $stmt->execute();
        $notificationId = $stmt->insert_id;

        // Il suffit simplement d'insérer une notification de suivi avec comme informations le username du suiveur, et l'id de suivi
        $query = "INSERT INTO notification_suivre (notification_id, suiveur_username, suivre_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isi", $notificationId, $followerUsername, $followId);
        $stmt->execute();
    }

    /**
     * Méthode permettant de créer une notification d'adoption
     *
     * @param $adopter_username
     * @param $animal_id
     * @return string
     */
    public function createNotificationAdoption($adopter_username, $animal_id) {
        // Lorsqu'un utilisateur clique sur le bouton d'adoption, il faut d'abord vérifier si l'animal existe bien :
        $stmt = $this->conn->prepare("SELECT * FROM animal WHERE id = ?");
        $stmt->bind_param("s", $animal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0) {
            // Si aucune ligne n'est trouvée, on informe l'utilisateur que l'animal n'existe pas
            return "L'animal n'existe pas.";
        }

        // Ensuite, il faut vérifier si l'animal est déjà adopté :
        $stmt = $this->conn->prepare("SELECT * FROM adoption WHERE animal_id = ?");
        $stmt->bind_param("s", $animal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return "L'animal est déjà adopté.";
        }

        // Par la suite, il faut récupérer le propriétaire de l'animal avec cette requete SQL simple :
        $stmt = $this->conn->prepare("SELECT maitre_username FROM animal WHERE id = ?");
        $stmt->bind_param("s", $animal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        // On stocke le propriétaire dans cette variable :
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


    /**
     * Méthode permettant de récupérer le type d'une notification en fonction de l'id du message
     *
     * @param $conn
     * @param $messageId
     * @param $type
     * @return mixed|null
     */
    public static function getNotificationTypeByMessageId($conn, $messageId, $type) {
        // Dans le cas où nous connaissons seulement l'id du message et son type, on peut avoir besoin de récupérer une notification spécifique
        // Pour ce faire, il suffit simplement de réaliser une concaténation de notification et $type et pour réaliser notification_type
        // Etant donné que les noms de table sont notification_message, notification_like, notification_reponse etc...
        // Donc si on cherche une notification d'un message d'id avec un type par exemple reponse, on cherchera dans la table notification_reponse avec l'id du message
        // Ca nous renverra ainsi toutes les informations de cette notification
        $stmt = $conn->prepare("SELECT n.id FROM notification n INNER JOIN notification_$type nt ON n.id = nt.notification_id WHERE nt.message_id = ?");
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Ici, on retourne la ligne entière pour récupérer les informations de notification
            return $row;
        } else {
            // Si rien n'est trouvé, on ne retourne rien
            return null;
        }
    }

    /**
     * Méthode permettant de déterminer si une notification a déjà été envoyée
     *
     * @param $username
     * @param $messageId
     * @return bool
     */
    public function isAlreadySent($username, $messageId) {
        // Dans le cadre d'un like, si un utilisateur like un message, puis ne le like plus, puis le relike, il ne faut pas envoyer deux fois la notification, seulement 1 fois.
        // Pour ce faire, on regarde tout simplement si le message a déjà été liké par l'utilisateur, et donc on regarde si la notification a déjà été envoyée
        // On regarde tout simplement par rapport à l'ID du message, et le likeur du message dans la clause WHERE
        $stmt = $this->conn->prepare("SELECT id FROM notification INNER JOIN notification_like
                                        ON notification.id = notification_like.notification_id
                                        WHERE notification_like.message_id = ? AND notification_like.likeur_username = ?");
        $stmt->bind_param("is", $messageId, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // On retourne le résultat (vrai ou faux)
        return ($result && $result->num_rows > 0);
    }

    /**
     * Méthode permettant de supprimer une notification de suivi
     *
     * @param $followId
     * @return void
     */
    public function deleteFollowNotifications($followId) {
        // Dans le cas où l'on souhaite que l'utilisateur abandonne son suivi d'un autre utilisateur, il est nécessaire de supprimer la notification de suivi
        // Ainsi, on récupère l'id de la notification de la table notification_suivre où l'id de la colonne suivre est égale à l'id de suivi
        $query = "SELECT notification_id FROM notification_suivre WHERE suivre_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $followId);
        $stmt->execute();
        $result = $stmt->get_result();
        // On stocke l'id de la notification :
        $notificationId = $result->fetch_assoc()['notification_id'];

        // Puis on supprime successivement la ligne dans la table notification_suivre en relation
        $query = "DELETE FROM notification_suivre WHERE suivre_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $followId);
        $stmt->execute();

        // Et enfin la notification générale que l'on supprime par rapport à l'id de la notification_suivi
        $query = "DELETE FROM notification WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
    }

    /**
     * Méthode permettant d'accepter une notification
     *
     * @param $notificationId
     * @return void
     */
    public function acceptAdoption($notificationId) : void {
        // Il ya plusieurs étapes à réaliser lors de l'acceptation de l'adoption :
        // 1) Mettre à jour la notification en "acceptée"
        // 2) Mettre à jour le maitre de l'animal au nouveau propriétaire
        // 3) Ajouter l'adoption dans la table Adoption
        // 4) Mettre l'animal en adoption = false, car il ne recherche plus de propriétaire

        // 1) On met à jour la notification en acceptée :
        $query = "UPDATE notification_adoption
              SET etat = 'acceptee'
              WHERE notification_id = ?;";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();

        // 2) On met à jour le nouveau maitre de l'animal, grâce à une requete composée qui évite de réaliser plusieurs requetes SQL séparées
        // Donc on met à jour le nouveau maitre en sélectionnant l'username de l'adoptant dans la table notification
        // De meme, la condition s'effectue sur l'id de l'animal qui se trouve dans la ligne notification_adoption avec sa notification_id correspondante
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

        // 3) Avant d'ajouter l'adoption, on récupère les informations de l'animal grâce à la l'id de la notification et l'animal qui se trouve dans la notification_adoption
        $query = "SELECT * FROM animal INNER JOIN notification_adoption ON animal.id = animal_id WHERE notification_adoption.notification_id = ? ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // On récupère ainsi l'id de l'animal ainsi que le username de son nouveau maitre (l'adoptant)
        $animal_id = $row['animal_id'];
        $adoptant_username = $row['adoptant_username'];

        //Enfin, on insert la nouvelle adoption avec les informations de l'animal et l'adoptant + la date actuelle d'adoption
        $query = "INSERT INTO adoption (animal_id, adoptant_username, date_adoption) VALUES ( ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $animal_id, $adoptant_username);
        $stmt->execute();

        // 4) Il est nécessaire de ne pas oublier de mettre l'adoption de l'animal à false dans la base de données
        $query = "UPDATE animal SET adopter = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $animal_id);
        $stmt->execute();
    }

    /**
     * Méthode permettant de refuser une adoption
     *
     * @param $notificationId
     * @return void
     */
    public function denyAdoption($notificationId) : void {
        // Cette requete simple permet tout simplement de changer l'etat en refusée dans la colonne de la notification d'adoption
        $query = "UPDATE notification_adoption SET etat = 'refusee' WHERE notification_id = ?;";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
    }

}
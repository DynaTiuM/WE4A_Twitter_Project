<?php

class Message {
    private $id;
    private $authorUsername;
    private $content;
    private $date;
    private $category;
    private $parentMessageId;
    private $location;
    private $image;
    private $firstName;
    private $lastName;
    private $organization;
    private $avatar;

    private $conn;
    private $db;

    /**
     * Constructeur prenant en paramètres une instance de mysqli() et une instance de la base de données
     *
     * @param mysqli $conn Instance de la classe mysqli
     * @param Database $db Instance de la classe Database
     *
     * @return void
     */
    public function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    public function setAuthorUsername($username) {
        $this->authorUsername = $username;
    }
    public function setContent($content) {
        $this->content = $content;
    }
    public function setDate($date) {
        $this->date = $this->dateConverted($date);
    }
    public function setLocation($location) {
        $this->location = $location;
    }

    /**
     * Méthode permettant d'afficher tous les messages
     *
     * @param $conn
     * @param $db
     * @param $messageIds
     * @return void
     */
    public static function displayMessages($conn, $db, $messageIds) {
        // Pour chaque message, en fonction de leur ID :
        foreach ($messageIds as $id) {
            // On crée un nouveau message
            $message = new Message($conn, $db);
            // On charge d'abord le message par rapport à son ID
            $message->loadMessageById($id);
            // Puis on l'affiche
            $message->displayContent();
        }
    }

    /**
     * Méthode permettant d'afficher les messages de la catégorie Abonnements
     *
     * @param $loginStatus
     * @return void
     */
    public function subMessages($loginStatus) {
        ?>
        <div class = "hub-messages">
            <?php
        // On récupère tous les Ids des messages de la catégorie abonnements, avec un level de 0, c'est à dire qu'il n'y a pas de messages pères aux messages
        // Il ne s'agit donc pas de réponses, mais seulement des messages postés, ne répondants à aucun autre message
        $messageIds = Message::mainMessagesQuery($this->conn, $this->db, $loginStatus, 'subs', null);
        // S'il y a au moins 1 message, on les affiche grâce à la méthode displayMessages
        if($messageIds)
            Message::displayMessages($this->conn, $this->db, $messageIds);
        else
            // Sinon on affiche s'implement qu'aucune réponse n'est disponible
            echo '<h4><br>Aucun message disponible</h4>';
        ?>

        </div>
        <?php
    }

    /**
     * Méthode permettant d'afficher les messages de la catégorie Explorer
     *
     * @param $loginStatus
     * @return void
     */
    public function explorerMessages($loginStatus) {
        // On commence par afficher les catégories de messages disponible dans la section explorer :
        ?>
        <div class = "hub-messages">
            <div class = "center">
                <div style ="display: inline-flex; margin-bottom: 0">
                    <a href = "explorer.php?category=sauvetage"><p class = "rescue" style = "font-size: 1.3vw">Sauvetages</p></a>
                    <a href = "explorer.php?category=evenement"><p class = "event" style = "font-size: 1.3vw">Événements</p></a>
                    <a href = "explorer.php?category=conseil"><p class = "advice" style = "font-size: 1.3vw">Conseils</p></a>
                </div>
            </div>

            <?php

            //On commencer par vérifier si l'utilisateru se trouve dans une page réponse
            if(isset($_GET['answer'])) {
                // Si answer dans l'url est vide, on considère que l'on ne se trouve pas dans une réponse
                // Ainsi, il est nécessaire de vérifier que $_GET['answer'] est bien différent d'un texte vide
                if ($_GET['answer'] != '') {
                    // Si on se trouve dans une réponse, il faut donc charger le message réponse
                    $message = new Message($this->conn, $this->db);
                    // On le charge grâce à son id positionné dans l'url du site
                    $message->loadMessageById($_GET['answer']);
                    if($message->id != null) {

                        // Il est également nécessaire de chercher le message parent, s'il existe, pour pouvoir éventuellement remonter dans les réponses
                        $parent_message_id = $message->getParentMessageId();

                        // Si le message parent existe :
                        if ($parent_message_id) {
                            ?>
                            <div>
                                <?php
                                // On effectue exactement la meme méthode d'affichage pour le message parent
                                $parent_message = new Message($this->conn, $this->db);
                                $parent_message->loadMessageById($parent_message_id);
                                // On affiche d'abord le message parent,
                                $parent_message->displayContent();
                                ?>
                                <span class="container-parent-message"></span>
                            </div>
                            <?php
                        }

                        // Puis on affiche ensuite le message fils (celui actuellement que l'on visionne)
                        // Afin de voir le message parent au dessus de la réponse apportée
                        $message->displayContent();
                    }
                    else {
                        echo "<h4><br>Ce message n'existe pas !</h4>";
                        return;
                    }
                }
                // Si $_POST['reply_to'] n'est pas set, c'est à dire si l'utilisateur n'a pas cliqué sur l'icon pour répondre à un commentaire
                // Et si isset($_POST['new-message'] n'est pas set également, c'est à dire que l'utilisateur n'a pas cliqué sur le bouton de nouveau message positionné sur la navigation bar,
                // Et qu'enfin l'utilisateur est connecté, on affiche le formulaire de nouveau message au dessous de la réponse
                if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");

                // Enfin, après notre espace d'écriture, il est important d'ajouter également les autres réponses à cette réponse actuelle :
                // On récupère tous les ids des messages qui sont la réponse à la réponse actuelle
                $messageIds = Message::mainMessagesQuery($this->conn, $this->db, $loginStatus, 'explorer', $_GET['answer']);
                if($messageIds)
                    // On affiche les messages
                    Message::displayMessages($this->conn, $this->db, $messageIds);
                else
                    // Sinon, il n'y a pas de réponses à la réponse
                    echo '<h4><br>Aucune réponse disponible</h4>';
            }
            // Si l'utilisateur ne se trouve pas dans une réponse, c'est qu'il peut se trouver dans une catégorie de message :
            elseif (isset($_GET['category'])) {
                // On effectue exactement les mêmes opérations, mais cette fois-ci en récupérant les ids de message d'une meme catégorie
                $messageIds = Message::getMessagesByCategory($this->conn, $this->db, $_GET['category']);
                if($messageIds)
                    Message::displayMessages($this->conn, $this->db, $messageIds);
                else
                    echo '<h4><br>Aucun message disponible dans cette catégorie</h4>';

            }
            // Finalement, si l'utilisateur se trouve dans la section explorer, ni dans une réponse de message, ni dans une catégorie
            // C'est qu'il est forcément sur la page explorer de base
            else {
                // De même, on effectue la meme vérification que vu précédemment ou afficher ou non la section de réponse à message
                if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");
                // On récupère à nouveau tous les ids de messages de la catégorie explorer, avec un level de null car on souhaite seulement les messages, et non les réponses de messages
                $messageIds = Message::mainMessagesQuery($this->conn, $this->db, $loginStatus, 'explorer', null);
                if($messageIds)
                    Message::displayMessages($this->conn, $this->db, $messageIds);
                else
                    echo '<h4><br>Aucun message disponible</h4>';
            }

            ?>
        </div>
        <?php
    }

    /**
     * Méthode permettant de créer un message à partir d'une ligne de message dans la base de données
     *
     * @param $conn
     * @param $row
     * @return Message
     */
    public static function createMessageFromRow($conn, $row) {
        global $globalDb;
        // On crée une nouvelle instance de message
        $message = new Message($conn, $globalDb);
        // Et on ajoute ses attributs en fonction de ce qu'il y a comme informations dans la ligne de la base de données
        $message->setId($row['id']);
        $message->setAuthorUsername($row['auteur_username']);
        $message->setContent($row['contenu']);
        $message->setDate($row['date']);
        $message->setLocation($row['localisation']);
        // Il est important de ne pas oublier de mettre à jour les informations du message avant de l'afficher
        $message->setInformationMessage();
        $message->setParentMessageId($row['id']);

        return $message;
    }

    /**
     * Méthode permettant de charger un message par rapport à son ID
     *
     * @param $id
     * @return void
     */
    public function loadMessageById($id) : void {
        // On prépare la requete SQL permettant de récupérer un message en fonction d'un id spécifique
        $query = "SELECT * FROM message WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        // On exécute la requête
        $stmt->execute();
        $result = $stmt->get_result();

        // Au final, s'il y a un résultat :
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            // On met à jour toutes les informations des attributs de la classe en fonction de la ligne récupérée dans la requête
            $this->id = $data['id'];
            $this->authorUsername = $data['auteur_username'];
            $this->parentMessageId = $data['parent_message_id'];
            $this->content = $data['contenu'];
            $this->image = $data['image'];
            $this->category = $data['categorie'];
            $this->location = $data['localisation'];
            // Ici, on converti la date pour obtenir la date du message différemment sur le site :
            // En effet, sur un message, ce n'est pas la date de publication qui apparait, mais il y a combien de temps que le message a été envoyé
            $this->date = $this->dateConverted($data['date']);
            $this->setInformationMessage();

        }
    }

    /**
     *
     * Méthode permettant de convertir une date en une date "il y a tant de temps"
     *
     * @param $date
     * @return string
     */
    private function dateConverted($date) {
        $timestamp = strtotime($date);

        $diff = date_diff(new DateTime("@$timestamp"), new DateTime());

        $days = $diff->d;
        $hours = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;

        if ($days > 0) {
            $diff = $days."j";
        } elseif ($hours > 0) {
            $diff = $hours."h";
        } elseif ($minutes > 0) {
            $diff = $minutes."m";
        } else {
            $diff = $seconds."s";
        }

        return $diff;
    }


    /**
     * Méthode permettant d'ajouter les informations essentielles concernant l'auteur d'un message
     *
     * @return array
     */
    private function setInformationMessage() {
        // On commence par créer une instance d'un utilisateur qui correspondra à l'auteur du message
        $user = User::getInstanceById($this->conn, $this->db, $this->authorUsername);

        // Ainsi, on peut récupérer toutes les informations nécessaires de l'auteur
        $this->avatar = $user->loadAvatar();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->organization = $user->isOrganization();
    }

    /**
     * Méthode permettant d'afficher un message en fonction de son ID
     *
     * @param $parent
     * @return void
     */
    public function displayContentById($parent = false)
    {
        $id = $this->db->secureString_ForSQL($this->id);

        // On effectue la requete SQL pour récupérer les informations du message dans la base de données
        $query = "SELECT * FROM message WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // On réutilise une méthode que l'on possède déjà pour créer notre message
            $message = Message::createMessageFromRow($this->conn, $result->fetch_assoc());
            // Enfin, on l'affiche
            $message->displayContent();
        }
    }

    /**
     * Méthode permettant d'afficher n'importe quel type de message
     *
     * @param $parent
     * @return void
     */
    public function displayContent($parent = false) {
        // On importe le script JavaScript permettant d'intéragir avec la modification ou suppression de message
        ?>
        <script src="../js/optionsMessage.js"></script>
        <?php

        // On récupère l'instance de notre utilisateur global (celui qui navigue sur le site)
        $user = User::getInstance($this->conn, $this->db);
        // On vérifie s'il est bien connecté
        $loginStatus = $user->isLoggedIn();
        require_once ('../Classes/Notification.php');

        // Au final, on vérifie si l'utilisateur global qui navigue sur le site est le meme que celui de l'auteur du message
        $isAuthor = $user->getUsername() === $this->authorUsername;

        // Cette partie de code permet de gérer les notifications
        // En effet, lorsqu'un utilisateur clique sur une notification de message, cette dernière le renvoie sur le contenu du message
        // (Donc dans cette partie du code)
        // Puis, il est nécessaire de vérifier ainsi s'il est venu jusqu'ici grâce à la notification de message

        // Donc, s'il s'agit d'un message d'un utilisateur que l'utilisateur suit :
        if(isset($_GET['answer']) && $user->isFollowing($this->authorUsername)) {
            // On récupère la notification en question
            $notification = Notification::getNotificationTypeByMessageId($this->conn, $_GET['answer'], 'message');
            $notificationId = $notification['id'];
            // Puis on la met en lue
            Notification::setRead($this->conn, $notificationId);
        }
        // De même, s'il s'agit d'un message qui est la réponse d'un message de l'utilisateur :
        if(isset($_GET['answer']) && Notification::isAnswerNotification($this->conn, $user->getUsername(), $this->id)) {
            $notification = Notification::getNotificationTypeByMessageId($this->conn, $_GET['answer'], 'reponse');
            if($notification) {
                $notificationId = $notification['id'];
                // On la met également en lue
                Notification::setRead($this->conn, $notificationId);
            }
        }
        // Il ne reste plus qu'à réaliser du HTML/PHP simple pour afficher le message :
        ?>
        <div class="message" id="message-container-<?php echo $this->id ?>">
        <a href="profile.php?username=<?php echo $this->authorUsername; ?>">
            <img class="avatar-message" src="data:image/jpeg;base64,<?php echo base64_encode($this->avatar); ?>">
        </a>
        <div>
        <div class="tweet-header">
            <?php
            // S'il s'agit d'une organisation, on ajoute un petit logo doré à coté du nom du compte
            if ($this->organization) {
                echo "<a href='./profile.php?username=$this->authorUsername'><h2 class='name-profile'>" . $this->firstName . " " . $this->lastName . "<img title=\"Ce compte est certifié car il s'agit d'une organisation\" src='../images/organisation.png' style='margin-left: 0.8vw; width:1.4vw; height: 1.4vw;'></h2></a>";
            }
            // Sinon on n'affiche rien
            else {
                echo "<a href='./profile.php?username=$this->authorUsername'><h2 class='name-profile'>" . $this->firstName . " " . $this->lastName . "</h2></a>";
            }
            echo '<h2 class="tweet-information">' . ' @' . $this->authorUsername . ' · ' . $this->date . '</h2>';
            ?>
        </div>
        <div class="tweet-content">
        <?php

        // Selon le type de message, on ajoute une pastille sur le message avec son type
        switch ($this->category) {
            case null:
                break;
            case 'evenement':
                echo '<a href="./explorer.php?category=evenement"><span class="event" style="padding: 0.5vw; margin-left: 0; margin-top: 1vw">ÉVÉNEMENT</span></a>';
                break;
            case 'sauvetage':
                echo '<a href="./explorer.php?category=sauvetage"><span class="rescue" style="padding: 0.5vw; margin-left: 0; margin-top: 1vw">SAUVETAGE</span></a>';
                break;
            case 'conseil':
                echo '<a href="./explorer.php?category=conseil"><span class="advice" style="padding: 0.5vw; margin-left: 0; margin-top: 1vw">CONSEIL</span></a>';
                break;
        }

        // Si le message possède une localisation :
        if ($this->location != null) {
            echo '<div>
                      <img style="width: 1vw; float: left;" src="../images/localisation.png" alt="Localisation">
                      <p class="localisation-message" style="margin-left: 1vw;">' . $this->location . '</p>
                  </div>';
        } ?>

            <div style = "display: flex; ">
                <a class="display-answer" href="./explorer.php?answer=<?php echo $this->id ?>">
                    <div>
                        <p id="message-<?php echo $this->id ?>"><?php echo stripcslashes($this->content) ?></p>
                    </div>
                    <?php
                    if ($this->image != null) { ?>
                        <img class="message-image" src="data:image/png;base64,<?php echo base64_encode($this->image); ?>">
                    <?php } ?>
                </a>
                <?php

                // Et donc comme vu précédemment, si l'utilisateur qui parcours le message est l'auteur du message, il y a possibilité de modifier le message
                if ($isAuthor) {
                    // On inclus le formulaire de modification de message :
                    include("../PageParts/updateMessageForm.php");
                    ?>
                    <div class="message-options">
                        <button class="options-button" id="options-button-<?php echo $this->id ?>" onclick="displayModification(<?php echo $this->id ?>)">✎</button>
                        <div class="options-dropdown" id="options-dropdown-<?php echo $this->id ?>">
                            <button class="options-dropdown-item" onclick="editMessage(<?php echo $this->id ?>)">Modifier</button>
                            <button class="options-dropdown-item" onclick="deleteMessage(<?php echo $this->id ?>)">Supprimer</button>
                        </div>
                    </div>
                    <?php } ?>
        </div>
        </div>
            <!-- Ajout de HTML pour les boutons comment, like et pour l'affichage des animaux qui sont mentionnés sur un message : -->

            <div style="display: flex;">
                    <div>
                        <form method="post" action="">
                            <input type="hidden" name="reply_to" value="<?php echo $this->id ?>">
                            <button type="submit" class="comment" <?php if (!$loginStatus) { ?> disabled<?php } ?>>
                                <label style="display: flex;">
                                    <img style="width: 1.5vw; padding: 0.6vw;" src="../images/comment.png" alt="Commenter">
                                    <?php if ($this->isCommented()) { ?>
                                        <span style="margin-top: 1vw; margin-left: -0.3vw; font-size: 1vw"><?php echo $this->numComments() ?></span>
                                    <?php } ?>
                                </label>
                            </button>
                        </form>
                    </div>

                    <form method="post" action="">
                        <input type="hidden" name="like" value="<?php echo $this->id ?>">
                        <button type="submit" class="comment" <?php if (!$loginStatus) { ?> disabled<?php } ?>>
                            <label style="display: flex;">
                                <?php
                                if ($this->isMessageLikedByUser($user->getUsername())) { ?>
                                    <img style="width: 1.5vw; padding: 0.6vw;" src="../images/liked.png" alt="Aimer">
                                <?php } else { ?>
                                    <img style="width: 1.5vw; padding: 0.6vw;" src="../images/like.png" alt="Ne plus aimer">
                                <?php }
                                if ($this->numLike() > 0) { ?>
                                    <span style="margin-top: 1vw; margin-left: -0.3vw; font-size: 1vw"><?php echo $this->numLike() ?></span>
                                <?php } ?>
                            </label>
                        </button>
                    </form>
                    <div id="pets">
                        <div style="display: flex; margin-left: 1vw; margin-top: 0.2vw;">
                            <?php
                            $result = $this->findPets();
                            while ($row = $result->fetch_assoc()) { ?>
                                <label>
                                    <a href="./profile.php?username=<?php echo $row['id']; ?>"><img style="margin-left: 0.3vw" class="pet-image-message" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Animal : <?php echo $row['nom'] ?>"></a>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                </div>
        </div>
        </div>
        <?php
    }

    /**
     * Méthode qui permet de récupérer les messages d'une catégorie spécifique dans la partie Explorer
     *
     * @param $conn
     * @param $db
     * @param $category
     * @return array
     */
    public static function getMessagesByCategory($conn, $db, $category)
    {
        $category = $db->secureString_ForSQL($category);

        // On effectue la requete SQL simple :
        $query = "SELECT * FROM message WHERE categorie = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        // On initialise un tableau pour stocker les ids des messages :
        $messageIds = [];
        if ($result->num_rows > 0) {
            // Et tant qu'il y a encore une ligne, on stocke l'id dans le tableau :
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
        }

        // On fini par retourner le tableau d'ids de messages
        return $messageIds;
    }

    /**
     * Méthode permettant de trier les messages à afficher selon :
     * - la localisation (explorer ou subscriptions)
     * - le niveau (message ou réponse)
     * - le status (utilisateur connecté ou non)
     *
     * @param $conn
     * @param $db
     * @param $loginStatus
     * @param $search
     * @param $level
     * @return array
     */
    public static function mainMessagesQuery($conn, $db, $loginStatus, $search, $level) : array {
        // Si le niveau est null, il s'agit d'un message simple (et non pas une réponse à un message)
        if ($level == null) {
            // On indique donc que level prend cette valeur, afin de réaliser notre requete SQL par la suite
            $level_ = 'IS NULL';
        } else {
            // Sinon, on donne la valeur de level dans la requete SQL, en concaténant avec le signe = devant
            // Car sinon, si le = n'est pas positionné ici, il risque d'être écrit WHERE level = IS NULL si le niveau est null, ce qui est faux au niveau SQL
            $level_ = "= ".$db->secureString_ForSQL($level);
        }

        // S'il y a un hashtag dans l'URL :
        if (isset($_GET['tag'])) {
            $tag = $db->secureString_ForSQL($_GET['tag']);

            // On prépare la requete SQL qui permet de récupérer tous les messages possédant cet hashtag
            // Pour ce faire, on join la table message avec cette de hashtag par l'id du message
            // On fini par joindre aussi avec la table utilisateur grâce à l'auteur du message, afin d'obtenir les informations de chaque auteur des messages avec l'hashtag en question
            // On fini la clause par une condition simple de hashtag
            // On n'oublie pas de trier par date décroissante pour obtenir les plus récents en premier
            $stmt = $conn->prepare("SELECT DISTINCT message.*, utilisateur.*
                                    FROM message
                                        JOIN hashtag ON message.id = hashtag.message_id
                                        JOIN utilisateur ON message.auteur_username = utilisateur.username
                                    WHERE hashtag.tag = ?
                                    ORDER BY message.date DESC");
            $stmt->bind_param("s", $tag);
        }
        // Sinon, s'il n'y a pas de hashtag, on regarde si on se trouve :
        else {
            // Dans la partie Explorer :
            if ($search != 'subs') {
                // Si c'est le cas, on sélectionne tous les messages où le niveau correspond à celui indiqué dans le paramètre level
                $stmt = $conn->prepare("SELECT message.*, utilisateur.*
                                        FROM message
                                            JOIN utilisateur ON message.auteur_username = utilisateur.username
                                        WHERE message.parent_message_id {$level_}
                                        ORDER BY message.date DESC");
            }
            // Ou dans la partie Abonnements :
            else {
                // Il est important de vérifier si l'utilisateur est bien connecté pour afficher les messages
                if ($loginStatus) {
                    // De plus, étant donné que l'on peut être abonné à un animal :
                    // Il est nécessaire ici d'ajouter un DISTINCT pour ne pas avoir des doublons de messages
                    // En effet, si on suit un animal ainsi que son propriétaire, il risque d'y apparaitre donc deux fois le meme message !
                    // Car pour rappel, lorsqu'on suit un animal, les messages où il est mentionné apparaissent dans l'onglet abonnements (il s'agit donc du message du propriétaire!)

                    // Dans cette requete SQL, il y a également des LEFT JOIN, qui sont utilisés dans le cas où il n'y a aucun animal de mentionné
                    $stmt = $conn->prepare("SELECT DISTINCT message.*
                            FROM message
                            LEFT JOIN message_animaux ON message.id = message_animaux.message_id
                            LEFT JOIN animal ON message_animaux.animal_id = animal.id
                            LEFT JOIN suivre ON suivre.suivi_id_utilisateur = message.auteur_username OR (suivre.suivi_type = 'animal' AND suivre.suivi_id_animal = animal.id)
                            WHERE suivre.utilisateur_username = ?
                            ORDER BY message.date DESC");
                    $stmt->bind_param("s", $_SESSION['username']);
                }
            }
        }

        // Finalement, on execute la requete SQL retenue
        $stmt->execute();
        $result = $stmt->get_result();

        // On initialise un tableau d'ids de messages
        $messageIds = [];
        if ($result->num_rows > 0) {
            // Et tant qu'il y a une ligne de retenue, on stocke l'id du message dans le tableau
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
        }

        // Le tableau renvoyé contient ainsi donc tous les ids de messages à afficher sur la page actuelle
        return $messageIds;
    }

    /**
     * Méthode d'envoi d'un message
     *
     * @param $conn
     * @param $db
     * @param $reply_id
     * @return void
     */
    public static function sendMessage($conn, $db, $reply_id = null) : void {

        // Vérification basique : Si le contenu est vide, on n'envoie pas le message
        if (!isset($_POST["content"])) {
            return;
        }

        // Cette vérification signifie que si l'URL possède un id de réponse à un message, et que ce dernier n'est pas vide :
        if (isset($_GET['answer']) && !empty($_GET['answer'])) {
            // On a joute une information pour laquelle le message répondra à un message déjà existant (il deviendra donc son message père)
            $reply_id = $_GET['answer'];
        }

        // Si reply_id est vide, on l'initialise à null
        if (empty($reply_id)) {
            $reply_id = null;
        }

        $content = $db->secureString_ForSQL($_POST["content"]);

        $username = $_SESSION["username"];

        require_once("Image.php");
        // On crée une nouvelle instanciation d'image en ajoutant en paramètres de constructeur l'image téléchargée
        $image = new Image($_FILES["image"]);

        // On vérifie que l'image GD crée n'est pas nulle, si c'est le cas, ça signifie qu'aucune image n'a été ajoutée dans le message
        if ($image->getGD() !== null) {
            // Sinon, on formate l'image pour réduire sa taille pour ne pas qu'elle prenne trop de place sur la base de données
            $image->formatImage();
        }

        // Au final, on récupère l'image formatée que l'on insèrera dans la base de données
        $formatedImage = $image->getFormatedImage();

        // De même, si la localisation est ajoutée, on la stocke dans une variable localisation
        // Sinon, on la met à null, grâce à la notation ?? qui permet d'effectuer une opération raccourcie équivalente à celle-ci :
        // $localisation = isset($_POST['localisation']) ? $_POST['localisation'] : null;
        $localisation = $_POST['localisation'] ?? null;

        // Si une catégorie de message est ajouté, on l'ajoute également grâce à $_POST['category']
        // Egalement, on considère que si la catégorie est classique, on ajoute simplement null dans la base de données pour des questions de simplicité
        $category = (isset($_POST['category']) && $_POST['category'] && $_POST['category'] != 'classique') ? $_POST['category'] : null;

        // Ainsi, on prépare la requete, puis on l'exécute
        $stmt = $conn->prepare("INSERT INTO message (auteur_username, parent_message_id, date, contenu, localisation, image, categorie) VALUES (?, ?, NOW(), ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $reply_id, $content, $localisation, $formatedImage, $category);
        $stmt->execute();

        // Par la suite, il est important de récupérer l'id du message nouvellement ajouté dans la base de données :
        $message_id = $stmt->insert_id;

        // Et nous pouvons ainsi créer une notification,
        require_once ("../Classes/Notification.php");
        $notification = new Notification($conn, $db);

        if($reply_id != null) {
            // Notification qui sera crée sois lorsqu'un utilisateur répond à un message, et informera donc l'utilisateur du message origingal
            $notification->createNotificationForAnswer($username, $message_id);
        }
        else {
            // Sois qui sera crée lorsqu'un utilisateur postera un message, et donc les abonnés de cet utilisateur en seront informés
            $notification->createNotificationsForFollowers($username, $message_id);
        }

        // De plus, il est important d'ajouter dans la base de données message_animaux, les messages auxquels les animaux sont mentionnés
        if (!empty($_POST['animaux'])) {
            // Pour chaque animal qui est mentionné dans le message :
            foreach ($_POST['animaux'] as $animal_id) {
                // On ajoute le message avec l'animal qui y est mentionné
                $stmt = $conn->prepare("INSERT INTO message_animaux (message_id, animal_id) VALUES (?, ?)");
                $stmt->bind_param("is", $message_id, $animal_id);
                $stmt->execute();
            }
        }

        // Finalement, il ne reste plus qu'à insérer les hashtags dans la base de données, dans le cas où un message contient des hashtags
        // Tout d'abord, il est important de remplacer le symbole \&#039 qui correspond au guillement simple par un espace, pour éviter d'avoir des bugs d'hashtag
        // En effet, un hashtag n'est pas censé posséder de '
        $content = str_replace("\&#039", " ", $content);

        // On utilise la fonction php preg_match_all qui permet de récupérer un motif d'une chaine de caractères et les stocker dans une autre variable
        // Ici, on cherche un motif d'hashtags composés de lettres, chiffres ou underscores
        // Voici l'explication du pattern :
        // # : recherche le caractère # qui correspond à celui de l'hashtag de message
        // ( : ouvre le groupe de capture pour récupérer le contenu du hashtag
        // \p{L} correspond à n'importe quelle lettre de l'alphabet
        // 0-9 correspond aux chiffres 0 à 9
        // _ correspond à l'underscore
        // Les crochets [] permettent de délimiter le pattern à ces symboles (lettres, chiffres, underscore) et le + indique qu'il
        // faut au minimum 1 caractère de ce groupe de capture pour qu'il soit considéré comme un pattern
        // ) : ferme le groupe de capture (le u indique le unicode)
        preg_match_all('/#([\p{L}0-9_]+)/u', $content, $matches);

        // On stocke donc les hashtags récupérés dans le groupe de capture dans un variable hashtags
        $hashtags = $matches[1];

        // Et pour chaque hashtag, on l'insere dans la base de données avec son message associé
        foreach ($hashtags as $hashtag) {
            $stmt = $conn->prepare("INSERT INTO hashtag (tag, message_id) VALUES (?, ?)");
            $stmt->bind_param("si", $hashtag, $message_id);
            $stmt->execute();
        }

        // Finalement, si un message, ou une réponse est envoyée, on redirige l'utilisateur directement vers la réponse adéquate ou le message adéquate
        $redirectUrl = isset($_GET['answer']) || isset($_POST['submit']) ? "explorer.php?answer=$reply_id" : "explorer.php";
        header("Location: $redirectUrl");
        exit();
    }


    /**
     * Méthode permettant de désigner le message parent d'un message, dans le cas où il s'agit d'un message réponse
     *
     * @param $id_son
     * @return void
     */
    public function setParentMessageId($id_son) : void {
        $id_son = $this->db->secureString_ForSQL($id_son);

        // On prépare une requete SQL simple permettant de sélectionner l'id du message parent en fonction de l'id du message
        $stmt = $this->conn->prepare("SELECT parent_message_id FROM message WHERE id = ?");
        $stmt->bind_param("i", $id_son);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Il suffit simplement de stocker ainsi cette valeur dans la variable privée de la classe message
            $this->parentMessageId = $row['parent_message_id'];
        }
        $stmt->close();
    }

    /**
     * Méthode permettant de modifier un message
     *
     * @return string
     */
    public function modifyMessage() {
        // On prépare la requete SQL permettant de mettre à jour la colonne contenu en fonction de l'id du message
        $stmt = $this->conn->prepare("UPDATE message SET contenu = ? WHERE id = ?");
        $stmt->bind_param("si", $this->content, $this->id);
        // On execute simplement la requete avec le nouveau contenu du message
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $stmt->close();
            // Dans le cas où une ligne a été impactée, cela signifie que le message a bien été mis à jour
            return "Message mis à jour.";
        }
        $stmt->close();

        // Sinon on indique à l'utilisateur qu'il y a eu une erreur lors de la mise à jour du message
        return "Erreur lors de la mise à jour du message";
    }

    /**
     * Méthode permettant de supprimer un message
     *
     * @return string
     */
    public function deleteMessage(): string {
        // On prépare la requete SQL permettant de supprimer un message en fonction de son id
        $stmt = $this->conn->prepare("DELETE FROM message WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $affected_rows = $stmt->affected_rows;
        if ($affected_rows > 0) {
            $stmt->close();
            // Si une ligne a été impactée par cette suppression, c'est que le message a bien été supprimé
            return "Message supprimé.";
        }
        $stmt->close();
        // Sinon, on indique qu'il y a eu une erreur lors de la suppression du message
        return "Erreur lors de la suppression du message";
    }

    // Getter
    public function getParentMessageId() {
        return $this->parentMessageId;
    }

    /**
     * Méthode permettant de vérifier si un message a été liké par un utilisateur cible
     *
     * @param $username
     * @return bool
     */
    public function isMessageLikedByUser($username) {
        // Requete SQL simple permettant de retourner une ligne dans la table like_message par rapport à un message et à un utilisateur
        $stmt = $this->conn->prepare("SELECT * FROM like_message WHERE message_id = ? AND utilisateur_username = ?");
        $stmt->bind_param("is", $this->id, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        // Si une ligne est retournée, ça retourne vrai, sinon faux
        return ($result->num_rows > 0);
    }

    /**
     * Méthode permettant de savoir si un message est commenté
     *
     * @return bool
     */
    public function isCommented() {
        // Dans ce cas-ci, il est nécessaire de regarder du coté des réponses
        // En effet, si un message dans la table message possède un message parent qui possède l'ID du message où l'on veut s'avoir s'il possède des réponses, il a nécessairement au moins 1 réponse
        $query = "SELECT * FROM message WHERE parent_message_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        // On retourne en fonction du nombre de lignes retrouvées
        // S'il y a au moins un message qui possède un message père possédant cet ID, c'est que le message possède des réponses
        if ($result->num_rows > 0) {
            return true;
        }
        // Sinon le message ne possède pas de réponses
        return false;
    }

    /**
     * Méthode permettant de connaitre les nombre de commentaires d'un message
     *
     * @return void
     */
    public function numComments() {
        // Cette requete SQL utilise le meme procédé que isCommented(), seulement en récupérant le nombre de lignes que la requete retournera grâce à COUNT()
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM message WHERE parent_message_id = ?");
        $stmt->bind_param("s", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        // S'il y a au moins une ligne, on retourne
        if ($result && $result->num_rows > 0) {
            // Cette méthode permet de récupérer le nombre de commentaires
            return $result->fetch_column();
        }
    }

    /**
     * Méthode permettant de récupérer le nombre de likes d'un message
     *
     * @return void
     */
    public function numLike() {
        // Le procédé est identique que la méthode numComments, seulement, elle s'effectue avec la table like_message
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM like_message WHERE message_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_column();
        }
    }

    /**
     * Méthode permettant de récupérer tous les animaux mentionnés dans un message
     *
     * @return false|mysqli_result
     */
    public function findPets() {
        // Afin de récupérer les caractéristiques de chaques animaux, il est nécessaire de lier la table message_animaux
        // avec la table animal, grâce à l'id de l'animal
        // Pour la condition de vérification est au niveau de l'ID du message
        $query = "SELECT animal.* FROM animal JOIN message_animaux ON animal.id = message_animaux.animal_id WHERE message_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        // Finalement, on renvoie tous les animaux qui correspondent à cet id de message
        return $stmt->get_result();
    }
}
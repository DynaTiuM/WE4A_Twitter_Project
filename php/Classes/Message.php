<?php

class Message
{
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


    public function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

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
    public function setCategory($category) {
        $this->category = $category;
    }
    public function setImage($image) {
        $this->image = $image;
    }
    public function setLocation($location) {
        $this->location = $location;
    }

    public static function displayMessages($conn, $db, $messageIds) {
        foreach ($messageIds as $id) {
            $message = new Message($conn, $db);
            $message->loadMessageById($id);
            $message->displayContent();
        }
    }

    public static function createMessageFromRow($conn, $row) {
        global $globalDb;
        $message = new Message($conn, $globalDb);
        $message->setId($row['id']);
        $message->setAuthorUsername($row['auteur_username']);
        $message->setContent($row['contenu']);
        $message->setDate($row['date']);
        $message->setLocation($row['localisation']);
        // Mettre à jour les informations du message avant de l'afficher
        $message->setInformationMessage();
        $message->setParentMessageId($row['id']);

        return $message;
    }

    public function loadMessageById($id) {
        // Récupérer les données du message en utilisant l'ID du message et stockage dans les attributs de la classe
        $query = "SELECT * FROM message WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $this->id = $data['id'];
            $this->authorUsername = $data['auteur_username'];
            $this->parentMessageId = $data['parent_message_id'];
            $this->content = $data['contenu'];
            $this->image = $data['image'];
            $this->category = $data['categorie'];
            $this->location = $data['localisation'];
            $this->date = $this->dateConverted($data['date']);
            $this->setInformationMessage();
        }
    }

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


    private function setInformationMessage() {
        global $globalDb;
        $user = new User($this->conn, $globalDb);
        $user->setUsername($this->authorUsername);

        // Appelez la méthode loadAvatar() sur l'objet Utilisateur
        $this->avatar = $user->loadAvatar();

        $stmt = $this->conn->prepare("SELECT nom, prenom, organisation FROM utilisateur JOIN message ON utilisateur.username = message.auteur_username WHERE auteur_username = ?");
        $stmt->bind_param("s", $this->authorUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result) {
            $row = $result->fetch_assoc();
            $this->firstName = $row['prenom'];
            $this->lastName = $row['nom'];
            $this->organization = $row['organisation'];
        }

        return array($this->avatar, $this->image, $this->location, $this->firstName, $this->lastName, $this->organization);
    }

    private function determineAvatarProvider() {
        // code to determine whether the author of the message is a user or an animal
        $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE username = ?");
        $stmt->bind_param("s", $this->authorUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }

    public static function getAllMessageIds($conn) {
        $query = "SELECT id FROM message";
        $result = $conn->query($query);

        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
        }
        return $messageIds;
    }

    public function displayContentById($parent = false)
    {
        $id = $this->db->secureString_ForSQL($this->id);

        $query = "SELECT * FROM message WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = Message::createMessageFromRow($this->conn, $result->fetch_assoc());
            $message->displayContent();
        }
    }


    public function displayContent($parent = false) {
        $user = new User($this->conn, $this->db);
        $loginStatus = $user->isLoggedIn();
        require_once ('../Classes/Notification.php');

        if(isset($_GET['answer']) && $user->isFollowing($this->authorUsername)) {
            $notification = Notification::getNotificationTypeByMessageId($this->conn, $_GET['answer'], 'message');
            $notificationId = $notification['id'];
            Notification::setRead($this->conn, $notificationId);
        }
        if(isset($_GET['answer']) && Notification::isAnswerNotification($this->conn, $user->getUsername(), $this->id)) {
            $notification = Notification::getNotificationTypeByMessageId($this->conn, $_GET['answer'], 'reponse');
            if($notification) {
                $notificationId = $notification['id'];
                Notification::setRead($this->conn, $notificationId);
            }
        }

        ?>
        <div class="message">
        <a href="profile.php?username=<?php echo $this->authorUsername; ?>">
            <img class="avatar-message" src="data:image/jpeg;base64,<?php echo base64_encode($this->avatar); ?>">
        </a>
        <div>
        <div class="tweet-header">
            <?php
            if ($this->organization) {
                echo "<a href='./profile.php?username=$this->authorUsername'><h2 class='name-profile'>" . $this->firstName . " " . $this->lastName . "<img title=\"Ce compte est certifié car il s'agit d'une organisation\" src='../images/organisation.png' style='margin-left: 0.8vw; width:1.4vw; height: 1.4vw;'></h2></a>";
            } else {
                echo "<a href='./profile.php?username=$this->authorUsername'><h2 class='name-profile'>" . $this->firstName . " " . $this->lastName . "</h2></a>";
            }
            echo '<h2 class="tweet-information">' . ' @' . $this->authorUsername . ' · ' . $this->date . '</h2>';
            ?>
        </div>
        <div class="tweet-content">
        <?php

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
        if ($this->location != null) {
            echo '<div>
                      <img style="width: 1vw; float: left;" src="../images/localisation.png" alt="Localisation">
                      <p class="localisation-message" style="margin-left: 1vw;">' . $this->location . '</p>
                  </div>';
        } ?>

    <a class="display-answer" href="./explorer.php?answer=<?php echo $this->id ?>">
        <label>
            <p><?php echo stripcslashes($this->content) ?></p>
        </label>
        <?php
        if ($this->image != null) { ?>
            <img class="message-image" src="data:image/png;base64,<?php echo base64_encode($this->image); ?>">
        <?php } ?>
    </a>
        </div>
            <?php if (!$parent) { ?>
                <div style="display: flex;">
                    <?php if (!isset($_POST['reply_to'])) { ?>
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
                    <?php } ?>
                    <form method="post" action="">
                        <input type="hidden" name="like" value="<?php echo $this->id ?>">
                        <button type="submit" class="comment" <?php if (!$loginStatus) { ?> disabled<?php } ?>>
                            <label style="display: flex;">
                                <?php
                                global $globalUser;
                                if ($this->isMessageLikedByUser($globalUser->getUsername())) { ?>
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
            <?php } ?>
        </div>
        </div>
        <?php
    }

    public static function displayMessagesByCategory($conn, $db, $category)
    {
        $category = $db->secureString_ForSQL($category);

        $query = "SELECT * FROM message WHERE categorie = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
        }

        return $messageIds;
    }


    public static function mainMessagesQuery($conn, $db, $loginStatus, $search, $level) {
        if ($level == null) {
            $level_ = 'IS NULL';
        } else {
            $level_ = "= " . $db->secureString_ForSQL($level);
        }
        if (isset($_GET['tag'])) {
            $tag = $db->secureString_ForSQL($_GET['tag']);
            $stmt = $conn->prepare("SELECT DISTINCT message.*, utilisateur.*
                                        FROM message
                                            JOIN hashtag ON message.id = hashtag.message_id
                                            JOIN utilisateur ON message.auteur_username = utilisateur.username
                                        WHERE hashtag.tag = ?
                                        ORDER BY message.date DESC");
            $stmt->bind_param("s", $tag);
        } else {
            if ($search != 'subs') {
                $stmt = $conn->prepare("SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                        FROM message
                        JOIN utilisateur ON message.auteur_username = utilisateur.username
                        WHERE message.parent_message_id {$level_}
                        ORDER BY message.date DESC");
            } else {
                if ($loginStatus) {
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

        $stmt->execute();
        $result = $stmt->get_result();

        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
        }
        return $messageIds;
    }

    public static function sendMessage($conn, $db, $reply_id = null) {
        if (!isset($_POST["content"])) {
            return;
        }

        if (isset($_GET['answer']) && !empty($_GET['answer'])) {
            $reply_id = $_GET['answer'];
        }

        if (empty($reply_id)) {
            $reply_id = null;
        }

        $content = $db->secureString_ForSQL($_POST["content"]);

        $username = $_SESSION["username"];

        require_once("Image.php");
        $image = new Image($_FILES["image"]);
        if ($image->getGD() !== null) {
            $image->formatImage();
        }
        $formatedImage = $image->getFormatedImage();

        $localisation = $_POST['localisation'] ?? null;

        $category = (isset($_POST['category']) && $_POST['category'] && $_POST['category'] != 'classique') ? $_POST['category'] : null;

        $stmt = $conn->prepare("INSERT INTO message (auteur_username, parent_message_id, date, contenu, localisation, image, categorie) VALUES (?, ?, NOW(), ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $reply_id, $content, $localisation, $formatedImage, $category);
        $stmt->execute();

        $message_id = $stmt->insert_id;

        require_once ("../Classes/Notification.php");
        $notification = new Notification($conn, $db);
        $notification->createNotificationsForFollowers($username, $message_id);

        if($reply_id != null) {
            $notification->createNotificationForAnswer($username, $message_id);
        }

        if (!empty($_POST['animaux'])) {
            foreach ($_POST['animaux'] as $animal_id) {
                $stmt = $conn->prepare("INSERT INTO message_animaux (message_id, animal_id) VALUES (?, ?)");
                $stmt->bind_param("is", $message_id, $animal_id);
                $stmt->execute();
            }
        }

        $content = str_replace("\&#039", " ", $content);
        preg_match_all('/#([\p{L}0-9_]+)/u', $content, $matches);
        $hashtags = $matches[1];

        foreach ($hashtags as $hashtag) {
            $stmt = $conn->prepare("INSERT INTO hashtag (tag, message_id) VALUES (?, ?)");
            $stmt->bind_param("si", $hashtag, $message_id);
            $stmt->execute();
        }

        $redirectUrl = isset($_GET['answer']) || isset($_POST['submit']) ? "explorer.php?answer=$reply_id" : "explorer.php";
        header("Location: $redirectUrl");
        ob_flush();
        exit();
    }

    public function setParentMessageId($id_son) {
        $id_son = $this->db->secureString_ForSQL($id_son);

        $stmt = $this->conn->prepare("SELECT parent_message_id FROM message WHERE id = ?");
        $stmt->bind_param("i", $id_son);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->parentMessageId = $row['parent_message_id'];
        }
        $stmt->close();
    }

    public function getParentMessageId() {
        return $this->parentMessageId;
    }

    public function isMessageLikedByUser($username) {
        $stmt = $this->conn->prepare("SELECT * FROM like_message WHERE message_id = ? AND utilisateur_username = ?");
        $stmt->bind_param("is", $this->id, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return ($result->num_rows > 0);
    }

    public function isCommented()
    {
        $query = "SELECT * FROM message WHERE parent_message_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public function numComments()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM message WHERE parent_message_id = ?");
        $stmt->bind_param("s", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_Column();
        }
    }

    public function numLike()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM like_message WHERE message_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_Column();
        }
    }

    function findPets() {
        global $conn;

        $query = "SELECT animal.* FROM animal JOIN message_animaux ON animal.id = message_animaux.animal_id WHERE message_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        return $stmt->get_result();
    }


    public static function countAllMessages($conn, $username, $type) {
        if ($type == "utilisateur") {
            $query = "SELECT COUNT(*) FROM message WHERE auteur_username = ?";
        } else {
            $query = "SELECT COUNT(*) FROM message_animaux WHERE animal_id = ?";
        }
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_Column();
    }
}
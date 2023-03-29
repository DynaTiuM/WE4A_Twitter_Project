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

    public function loadMessageById($id) {
        // Récupérez les données du message en utilisant l'ID du message et stockez-les dans les attributs de la classe
        $query = "SELECT * FROM message WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $this->id = $data['id'];
            $this->authorUsername = $data['auteur_username'];
            $this->content = $data['contenu'];
            $this->image = $data['image'];
            $this->category = $data['categorie'];
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
            include("../messageForm.php");
            $this->displayContent($result->fetch_assoc(), $parent);
        }
    }

    private function displayContent($parent = false) {
        $user = new User($this->conn, $this->db);
        $loginStatus = $user->isLoggedIn();

        if(isset($_GET['answer']) && ($user->isFollowing($this->authorUsername))) {
            //$this->markNotificationAsRead();
        }

        // Ici commence la partie HTML
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
                      <img style="width: 1vw; float: left;" src="./images/localisation.png" alt="Localisation">
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
                                if ($this->isLiked()) { ?>
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

        if ($result->num_rows > 0) {
            include("messageForm.php");

            while ($row = $result->fetch_assoc()) {
                $message = new Message($conn, $db);
                $message->setId($row['id']);
                $message->setContent($row['contenu']);
                $message->setDate($row['date']);
                $message->setAuthorUsername($row['auteur_username']);
                $message->setCategory($row['categorie']);
                $message->setImage($row['image']);
                $message->setLocation($row['localisation']);

                // Mettez à jour les informations du message avant de l'afficher
                $message->setInformationMessage();

                // Affichez le message
                $message->displayContent();
            }
        }
    }


    public static function mainMessagesQuery($conn, $db, $loginStatus, $search, $level)
    {
        if ($level == null) {
            $level_ = 'IS NULL';
        } else {
            $level_ = "= " . $conn->securizeString_ForSQL($level);
        }

        if (isset($_GET['tag'])) {
            $tag = $db->secureString_ForSQL($_GET['tag']);
            $stmt = $conn->prepare("SELECT DISTINCT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                FROM message
                    JOIN hashtag ON message.id = hashtag.message_id
                    JOIN utilisateur ON message.auteur_username = utilisateur.username
                WHERE message.contenu like ? OR hashtag.tag = ?
                ORDER BY message.date DESC");
            $like_tag = "%$tag%";
            $stmt->bind_param("ss", $like_tag, $tag);
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
                    $stmt->bind_param("s", $_COOKIE['username']);
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

    public function sendMessage($reply_id = null) {
        if (!isset($_POST["content"])) {
            return;
        }

        if (isset($_GET['answer']) && !empty($_GET['answer'])) {
            $reply_id = $_GET['answer'];
        }

        if (empty($reply_id)) {
            $reply_id = null;
        }

        $content = $this->db->secureString_ForSQL($_POST["content"]);
        $username = $_COOKIE["username"];

        require_once("Image.php");
        $image = new Image($_FILES["image"]);
        if ($image->getGD() !== null) {
            $image->formatImage();
        }
        $formatedImage = $image->getFormatedImage();

        $localisation = $_POST['localisation'] ?? null;

        $category = (isset($_POST['category']) && $_POST['category'] && $_POST['category'] != 'classique') ? $_POST['category'] : null;

        $stmt = $this->conn->prepare("INSERT INTO message (auteur_username, parent_message_id, date, contenu, localisation, image, categorie) VALUES (?, ?, NOW(), ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $reply_id, $content, $localisation, $formatedImage, $category);
        $stmt->execute();

        $message_id = $stmt->insert_id;

        if (!empty($_POST['animaux'])) {
            foreach ($_POST['animaux'] as $animal_id) {
                $stmt = $this->conn->prepare("INSERT INTO message_animaux (message_id, animal_id) VALUES (?, ?)");
                $stmt->bind_param("is", $message_id, $animal_id);
                $stmt->execute();
            }
        }

        $content = str_replace("\&#039", " ", $content);
        preg_match_all('/#([\p{L}0-9_]+)/u', $content, $matches);
        $hashtags = $matches[1];

        foreach ($hashtags as $hashtag) {
            $stmt = $this->conn->prepare("INSERT INTO hashtag (tag, message_id) VALUES (?, ?)");
            $stmt->bind_param("si", $hashtag, $message_id);
            $stmt->execute();
        }

        $redirectUrl = isset($_GET['answer']) || isset($_POST['submit']) ? "explorer.php?answer=$reply_id" : "explorer.php";
        header("Location: $redirectUrl");
        ob_flush();
        exit();
    }

    public function likeMessage()
    {
        $id_message = $this->id;
        $id_user = $this->conn->securizeString_ForSQL($_COOKIE['username']);
        $date = date('Y-m-d H:i:s');

        if (!$this->isLiked($this->conn)) {
            $stmt = $this->conn->prepare("INSERT INTO like_message (message_id, utilisateur_username, date) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $id_message, $id_user, $date);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $this->conn->prepare("DELETE FROM like_message WHERE message_id = ? AND utilisateur_username = ?");
            $stmt->bind_param("ss", $id_message, $id_user);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function setParentMessageId($id_son) {
        $id_son = $this->conn->securizeString_ForSQL($id_son);

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

    public function isLiked()
    {
        $query = "SELECT * FROM like_message WHERE message_id = ? AND utilisateur_username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $this->id, $this->authorUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return true;
        }
        return false;
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

    public function numComments($conn, $id_message)
    {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM message WHERE parent_message_id = ?");
        $stmt->bind_param("s", $id_message);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_Column();
        }
    }

    public function findLikedMessages($username) {
        $query = "SELECT message_id FROM like_message WHERE utilisateur_username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($result->fetch_assoc()) {
                $query = "SELECT * FROM message WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("s", $this->id);
                $stmt->execute();
                $result2 = $stmt->get_result();

                if($result2){
                    $row2 = $result2->fetch_assoc();
                    $this->displayContent($row2);
                }
            }
        }
        else {
            echo '<br><h4>Ce profil n\'a aimé aucun message</h4>';
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

    public function profilMessagesAndAnswers($username, $isMessage) {
        if ($isMessage) {
            $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id IS NULL) ORDER BY date DESC";
        } else {
            $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id is not NULL) ORDER BY date DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $this->displayContent($row);
            }
        }
        else {
            if ($isMessage) {
                echo '<br><h4>Ce profil ne contient aucun message</h4>';
            } else {
                echo '<br><h4>Ce profil n\'a répondu à aucun message</h4>';
            }
        }
    }

    public function countAllMessages($username, $type) {
        if ($type == "user") {
            $query = "SELECT COUNT(*) FROM message WHERE auteur_username = ?";
        } else {
            $query = "SELECT COUNT(*) FROM message_animaux WHERE animal_id = ?";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_Column();
    }
}
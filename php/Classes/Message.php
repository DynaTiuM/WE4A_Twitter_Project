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

    private static $instance;

    public function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    public static function getInstance($conn, $db) {
        if (self::$instance === null) {
            self::$instance = new Message($conn, $db);
        }

        return self::$instance;
    }

    public function getInformationMessage() {
        $timestamp = strtotime($this->date);

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

        $avatarProvider = $this->determineAvatarProvider();
        $this->avatar = $avatarProvider->loadAvatar();

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

        return array($this->id, $this->content, $diff, $this->avatar, $this->image, $this->location, $this->authorUsername, $this->firstName, $this->lastName, $this->category, $this->organization);
    }

    private function determineAvatarProvider() {
        // code to determine whether the author of the message is a user or an animal
        $stmt = $this->conn->prepare("SELECT type FROM utilisateur WHERE username = ? UNION SELECT type FROM animal WHERE id = ?");
        $stmt->bind_param("ss", $this->authorUsername, $this->authorUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $authorType = $row['type'];
            if ($authorType == 'user') {
                $authorIsUser = true;
            } else {
                $authorIsUser = false;
            }
        } else {
            $authorIsUser = false;
        }

        if ($authorIsUser) {
            return new User($this->authorUsername);
        } else {
            return new Animal($this->authorUsername);
        }
    }

    public function displayContentById($parent = false)
    {
        $id = $this->conn->securizeString_ForSQL($this->id);

        $query = "SELECT * FROM message WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            include("messageForm.php");
            displayContent($result->fetch_assoc(), $parent);
        }
    }
    public function displayContentByCategory($category)
    {
        $category = $this->db->securizeString_ForSQL($category);

        $query = "SELECT * FROM message WHERE categorie = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            include("messageForm.php");
            displayContent($result->fetch_assoc());
        }
    }

    public function mainMessagesQuery($loginStatus, $search, $level)
    {
        if ($level == null) {
            $level_ = 'IS NULL';
        } else {
            $level_ = "= " . $this->conn->securizeString_ForSQL($level);
        }

        if (isset($_GET['tag'])) {
            $tag = $this->conn->securizeString_ForSQL($_GET['tag']);
            $stmt = $this->conn->prepare("SELECT DISTINCT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                FROM message
                    JOIN hashtag ON message.id = hashtag.message_id
                    JOIN utilisateur ON message.auteur_username = utilisateur.username
                WHERE message.contenu like ? OR hashtag.tag = ?
                ORDER BY message.date DESC");
            $like_tag = "%$tag%";
            $stmt->bind_param("ss", $like_tag, $tag);
        } else {
            if ($search != 'subs') {
                $stmt = $this->conn->prepare("SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                        FROM message
                        JOIN utilisateur ON message.auteur_username = utilisateur.username
                        WHERE message.parent_message_id {$level_}
                        ORDER BY message.date DESC");

            } else {
                if ($loginStatus) {
                    $stmt = $this->conn->prepare("SELECT DISTINCT message.*
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

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                displayContent($row);
            }
        } else {
            if ($level == null) echo '<h4>Aucun contenu disponible</h4>';
            else echo '<h4>Aucune réponse disponible</h4>';
        }
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

    public function isLiked($conn, $id_message, $username)
    {
        $id_user = $this->conn->securizeString_ForSQL($username);

        $query = "SELECT * FROM like_message WHERE message_id = ? AND utilisateur_username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $id_message, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public function isCommented($conn, $id_message)
    {
        $query = "SELECT * FROM message WHERE parent_message_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_message);
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
                    //$this->displayContent($row2);
                }
            }
        }
        else {
            echo '<br><h4>Ce profil n\'a aimé aucun message</h4>';
        }
    }

    public function numLike($conn)
    {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM like_message WHERE message_id = ?");
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
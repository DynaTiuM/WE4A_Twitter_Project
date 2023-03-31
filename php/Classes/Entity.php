<?php

class Entity {

    protected $username;

    protected $conn;
    protected $db;
    protected $avatar;

    public function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    function numFollowers($type) {
        $query = "SELECT COUNT(*) FROM suivre WHERE suivi_id_$type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_column();
    }

    public function setUsername($username) {
        $this->username = $username;
    }
    public function getUsername() {
        return $this->username;
    }

    protected function selectSQLAvatar($sql) {
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["avatar"];
        }

        return "Aucune image trouvée.";
    }

    public function getAvatarEncoded64() {
        return base64_encode($this->loadAvatar());
    }

    public function getAvatar() {
        return $this->loadAvatar();
    }

    public function follow_unfollow($to_follow, $type) {
        if ($type != 'utilisateur') $type = 'animal';

        $stmt = $this->conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
        $stmt->bind_param("ss", $this->username, $to_follow);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            require_once ("../Classes/Notification.php");
            $followId = $result->fetch_assoc()['id'];
            $notification = new Notification($this->conn, $this->db);
            $notification->deleteFollowNotifications($followId);

            $stmt = $this->conn->prepare("DELETE FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
            $stmt->bind_param("ss", $this->username, $to_follow);
            $stmt->execute();
            return null;
        }

        $stmt = $this->conn->prepare("INSERT INTO suivre (utilisateur_username, suivi_type, suivi_id_$type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->username, $type, $to_follow);
        $stmt->execute();
        return $stmt->insert_id;
    }


    public function checkFollow($to_follow, $type): bool {
        $stmt = $this->conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_type = ? AND suivi_id_$type = ?");
        $stmt->bind_param("sss", $this->username, $type, $to_follow);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyUnicity($parameter) {
        $query = "(SELECT username FROM utilisateur WHERE username = ?) UNION (SELECT id FROM animal WHERE id = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $parameter, $parameter);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            return false;
        }
        return true;
    }
}

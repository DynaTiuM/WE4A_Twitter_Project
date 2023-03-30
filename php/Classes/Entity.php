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

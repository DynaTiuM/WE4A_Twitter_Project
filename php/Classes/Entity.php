<?php

class Entity {

    protected $username;

    protected $conn;
    protected $db;

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
}

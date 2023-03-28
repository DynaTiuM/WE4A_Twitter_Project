<?php

class Database
{
    private $conn;

    public function __construct() {
        $servername = "localhost";
        $username = "root2";
        $password = "root";
        $dbname = "we4a_project";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function secureString_ForSQL($string): string {
        $string = trim($string);
        $string = stripcslashes($string);
        $string = addslashes($string);
        return htmlspecialchars($string);
    }

    public function getConnection() {
        return $this->conn;
    }

    public function close() {
        $this->conn->close();
    }
}
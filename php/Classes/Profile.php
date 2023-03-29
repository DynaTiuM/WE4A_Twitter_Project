<?php

abstract class Profile
{
    protected $username;
    protected $conn;
    protected $db;
    protected int $numberOfMessages;
    protected $profileUser;

    public function __construct($conn, $username, $db) {
        $this->conn = $conn;
        $this->username = $username;
        $this->db = $db;

        $this->profileUser = new User($this->conn, $this->db);
        $this->profileUser->setUsername($username);
    }

    public function displayNumMessages() {
        ?>
        <div style = "margin-left: 1vw; font-family: 'Plus Jakarta Sans', sans-serif;">
            <p style = "margin-top: 0; padding-top: 0; font-size: 0.9vw;"><?php echo $this->numberOfMessages?> Messages</p>
        </div>
        <?php
    }

    public function setNumberOfMessages($number) {
        $this->numberOfMessages = $number;
    }

    public function getUser() {
        return $this->profileUser;
    }

    abstract public function displayProfile(); // Méthode abstraite à implémenter dans les classes filles
}
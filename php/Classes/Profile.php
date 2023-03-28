<?php

abstract class Profile
{
    protected $username;
    protected $conn;
    protected int $numberOfMessages;

    public function __construct($conn, $username) {
        $this->conn = $conn;
        $this->username = $username;
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

    abstract public function displayProfile(); // Méthode abstraite à implémenter dans les classes filles
}
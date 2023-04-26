<?php
require_once("init.php");
require_once("../Classes/Message.php");
require_once("functions.php");
global $conn;
global $globalDb;

// Dans le cas où l'id du message est POST :
if (isset($_POST["messageId"])) {
    // On stocke la valeur de l'id dans uen variable
    $messageId = intval($_POST["messageId"]);

    // On crée un nouveau message
    $message = new Message($conn, $globalDb);
    $message->setId($messageId);
    // Et on delete finalement le message
    $message->deleteMessage();
}
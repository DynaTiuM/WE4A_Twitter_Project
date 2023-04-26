<?php
require_once("init.php");
require_once("../Classes/Message.php");
require_once("functions.php");
global $conn;
global $globalDb;

// Si l'id du message est set et que le nouveau contenu du message est set également par méthode POST :
if (isset($_POST["messageId"]) && isset($_POST["newContent"])) {
    // On récupère les valeurs dans des variables :
    $messageId = intval($_POST["messageId"]);
    $newContent = $_POST["newContent"];

    // On crée un nouveau message
    $message = new Message($conn, $globalDb);
    // On ajoute ainsi les attributs du message
    $message->setId($messageId);
    $message->setContent($newContent);
    // Puis on modifie le message
    $result = $message->modifyMessage();
}

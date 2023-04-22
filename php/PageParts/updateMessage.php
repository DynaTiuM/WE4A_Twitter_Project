<?php
require_once("init.php");
require_once("../Classes/Message.php");
require_once("functions.php");
global $conn;
global $globalDb;

if (isset($_POST["messageId"]) && isset($_POST["newContent"])) {
    $messageId = intval($_POST["messageId"]);
    $newContent = $_POST["newContent"];

    $message = new Message($conn, $globalDb);
    $message->setId($messageId);
    $message->setContent($newContent);
    $result = $message->modifyMessage();

    displayPopUp("Succès", "Message mis à jour.");
    ?>
    <script>
        // Ouverture automatique de la fenêtre
        window.onload = function() {
            openWindow('pop-up');
        }
    </script>
    <?php
} else {
    displayPopUp("Erreur", "Erreur lors de la mise à jour du message.");
}
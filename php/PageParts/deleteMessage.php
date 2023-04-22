<?php
require_once("init.php");
require_once("../Classes/Message.php");
require_once("functions.php");
global $conn;
global $globalDb;

if (isset($_POST["messageId"])) {
    error_log("POST values are set");
    $messageId = intval($_POST["messageId"]);

    $message = new Message($conn, $globalDb);
    $message->setId($messageId);
    $message->deleteMessage();

    displayPopUp("Succès", "Message Supprimé.");
    ?>
    <script>
        // Ouverture automatique de la fenêtre erreur-connexion
        window.onload = function() {
            openWindow('pop-up');
        }
    </script>
    <?php
} else {
    displayPopUp("Erreur", "Erreur lors de la suppression du message.");
}
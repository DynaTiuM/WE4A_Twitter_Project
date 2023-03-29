<?php

function popUpNewMessage($forced = false) {
    if (isset($_POST['reply_to']) && !empty($_POST['reply_to']) || $forced) {
        // Afficher ici la section des messages avec la réponse au message sélectionné
        ?>
        <script>
            // Ouverture automatique de la fenêtre erreur-connexion
            window.onload = function() {
                openWindow('new-message');
            }
        </script>
        <?php
    }
}

function displayNewMessageForm($conn, $db, $messageId = null) {
    echo '<div id="new-message" class="window-background">
        <div class="window-content">';

    if (isset($messageId)) {
        echo '<span class="close" onclick="closeWindowToNewDestination(\'new-message\', location.href)">&times;</span>';
        echo '<h2 class="window-title">Nouveau commentaire</h2>';
        $message = new Message($conn, $db);
        $message->setId($messageId);
        $message->displayContentById($messageId);
    } else {
        echo '<span class="close" onclick="closeWindowToNewDestination(\'new-message\', location.href)">&times;</span>';
        echo '<h2 class="window-title">Nouveau message</h2>';
    }

    require_once("./newMessageForm.php");
    echo '</div></div>';
}
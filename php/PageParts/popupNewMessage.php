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
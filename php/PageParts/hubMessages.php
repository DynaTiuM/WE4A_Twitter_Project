<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("./functions.php");

/**
 * Méthode permettant d'afficher le contenu des pages abonnement ou explorer
 *
 * @param $type
 * @return void
 */
function displayContainer($type) {
    require_once("init.php");

    // On récupère les instances globales :
    global $globalDb;
    global $globalUser;
    global $globalMessage;
    global $conn;

    // On vérifie le statut de connexion de l'utilisateur
    $loginStatus = $globalUser->isLoggedIn();

    // Si un message a été liké et que l'utilisateur est connecté, alors on utilise la méthode likeMessage qui permet à un utilisateur de liker un message
    if(isset($_POST['like']) && $loginStatus) $globalUser->likeMessage($_POST['like']);

    // Si une notification a été cliquée dans la section notification :
    if(isset($_POST['notification-id'])) {
        require_once ('../Classes/Notification.php');
        // On récupère la notification par rapport à l'id du message
        $notification = Notification::getNotificationTypeByMessageId($conn, $_POST['notification-id'], 'like');
        $notificationId = $notification['id'];
        // Et cette notification est par la suite mise en vue
        Notification::setRead($conn, $notificationId);
    }

    // Enfin, si un message a été posté grâce au bouton d'envoi du formulaire de message :
    if(isset($_POST["submit"])) {
        // On envoie le message dans la base de données
        Message::sendMessage($conn, $globalDb, $_POST["submit"]);
    }

    ?>

    <!DOCTYPE html>
    <html lang = "fr">
    <head>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "../css/stylesheet.css">
        <link rel = "stylesheet" href = "../css/newMessage.css">
        <link rel = "stylesheet" href = "../css/message.css">
        <link rel="shortcut icon" href="../favicon.ico">
    </head>
    <body>
    <div class = "Container">
        <?php
        // Il est important de bien afficher la partie navigation
        include ("./navigation.php");

        // Et encore une fois, si l'utilisateur a cliqué sur le bouton pour répondre à un commentaire :
        if(isset($_POST['reply_to'])) {
            // On fait apparaitre la pop-up pour répondre à ce commentaire
            popUpNewMessage();
            displayNewMessageForm($conn, $globalDb, $_POST['reply_to']);
        }
        ?>

        <div class = "MainContainer">
            <?php

            // Si la type d'affichage est les abonnements :
            if($type == 'subs') {
                // On affiche la section abonnement
                ?>
                <div class = "h1-container">
                    <h1>Abonnements</h1>
                </div>
                <div class = "spacing"></div>
                <?php
                // Ainsi, si l'utilisateur est connecté :
                if ($loginStatus) {
                    // On affiche tous les messages d'abonnement
                    $globalMessage->subMessages($loginStatus);
                }
                // Sinon :
                else {
                    // On indique à l'utilisateur qu'il doit se connecter
                    echo '<h4>Connectez-vous pour accéder au contenu</h4>';
                }
            }
            // Sinon, s'il s'agit de la section explorer :
            else {
                ?>
                <div class = "h1-container">
                    <h1>Explorer</h1>
                </div>
                <div class = "spacing"></div>
                <?php
                // On affiche les messages de l'explorer
                $globalMessage->explorerMessages($loginStatus);
            }
            ?>
        </div>

        <?php
        // On fini par afficher les tendances
        include("./trends.php");
        ?>
    </div>

    </body>

    </html>

    <?php
}

?>

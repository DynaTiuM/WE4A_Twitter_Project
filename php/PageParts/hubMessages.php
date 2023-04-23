<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("./functions.php");

function displayContainer($type) {
    require_once("init.php");

    global $globalDb;
    global $globalUser;
    global $globalMessage;
    global $conn;
    $loginStatus = $globalUser->isLoggedIn();

    if(isset($_POST['like']) && $loginStatus) $globalUser->likeMessage($_POST['like']);

    if(isset($_POST['notification-id'])) {
        require_once ('../Classes/Notification.php');
        $notification = Notification::getNotificationTypeByMessageId($conn, $_POST['notification-id'], 'like');
        $notificationId = $notification['id'];
        Notification::setRead($conn, $notificationId);
    }

    if(isset($_POST["submit"])) {
        include("./sendingMessage.php");
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
        include ("./navigation.php");
        if(isset($_POST['reply_to'])) {
            popUpNewMessage();
            displayNewMessageForm($conn, $globalDb, $_POST['reply_to']);
        }
        ?>

        <div class = "MainContainer">
            <?php
            if($type == 'subs') {
                ?>
                <div class = "h1-container">
                    <h1>Abonnements</h1>
                </div>
                <div class = "spacing"></div>
                <?php
                if ($loginStatus) {
                    $globalMessage->subMessages($loginStatus);
                }
                else {
                    echo '<h4>Connectez-vous pour accéder au contenu</h4>';
                }
            }
            else {
                ?>
                <div class = "h1-container">
                    <h1>Explorer</h1>
                </div>
                <div class = "spacing"></div>
                <?php
                $globalMessage->explorerMessages($loginStatus);
            }
            ?>
        </div>

        <?php
        include("./trends.php");
        ?>
    </div>

    </body>

    </html>

    <?php
}

?>

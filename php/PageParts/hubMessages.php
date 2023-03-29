<?php

function displayContainer($type) {
    $globalDb = Database::getInstance();
    $conn = $globalDb->getConnection();
    $globalUser = User::getInstance($conn, $globalDb);
    $globalMessage = new Message($conn, $globalDb);
    $loginStatus = $globalUser->isLoggedIn();


    //if(isset($_POST['like']) && $loginStatus) likeMessage($_POST['like']);

    if(isset($_POST["submit"])) {
        include("./sendingMessage.php");
        $globalMessage->sendMessage($_POST["submit"]);
    }

    ?>

    <!DOCTYPE html>
    <html lang = "fr">
    <head>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "../css/stylesheet.css">
        <link rel = "stylesheet" href = "../css/newMessage.css">
        <link rel="shortcut icon" href="../favicon.ico">
    </head>
    <body>
    <div class = "Container">
        <?php
        include ("./navigation.php");
        include("./popupnewMessage.php");
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
                    //popUpNewMessage();
                    mainMessages($loginStatus);
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
                //popUpNewMessage();
                explorerMessages($loginStatus);
            }
            ?>
        </div>

        <?php
        include("./trends.php");

        include("./popupNewMessageForm.php");
        ?>
    </div>

    </body>

    </html>

    <?php
}

?>

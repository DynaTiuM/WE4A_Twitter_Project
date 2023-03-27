<?php

function displayContainer($type) {
    require_once("./PageParts/databaseFunctions.php");
    ConnectDatabase();
    $loginStatus = CheckLogin();

    if(isset($_POST['like']) && $loginStatus[0]) likeMessage($_POST['like']);

    if(isset($_POST["submit"])) {
        include("./PageParts/sendingMessage.php");
        sendMessage($_POST["submit"]);
    }

    ?>

    <!DOCTYPE html>
    <html lang = "fr">
    <head>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "./css/stylesheet.css">
        <link rel="shortcut icon" href="./favicon.ico">
    </head>
    <body>
    <div class = "Container">
        <?php
        include ("./PageParts/navigation.php");
        include("./PageParts/popupnewMessage.php");
        ?>

        <div class = "MainContainer">
            <?php
            if($type == 'subs') {
                ?>
                <h1>Abonnements</h1>
                <?php
                if ($loginStatus) {

                    popUpNewMessage();
                    mainMessages($loginStatus);
                }
                else {
                    echo '<h4>Connectez-vous pour accéder au contenu</h4>';
                }
            }
            else {
                ?>
                <h1>Explorer</h1>
                <?php
                popUpNewMessage();
                explorerMessages($loginStatus);
            }
            ?>
        </div>

        <?php
        include("./PageParts/trends.php");

        include("./PageParts/popupNewMessageForm.php");
        ?>
    </div>

    </body>

    </html>

    <?php
}

?>

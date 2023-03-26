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

function mainMessages($loginStatus) {
    ?>
    <div class = "hub-messages">
        <?php
        include("./PageParts/messageForm.php");
        if(isset($_GET['answer']))
            mainMessagesQuery($loginStatus, 'subs', $_GET['answer']);
        else
            mainMessagesQuery($loginStatus, 'subs', null);
        ?>

    </div>
<?php
}

function explorerMessages($loginStatus) {
    ?>
    <div class = "hub-messages">
        <div class = "center">
            <div style ="display: inline-flex; margin-bottom: 0">
                <a href = "./explorer.php?category=sauvetage"><p class = "rescue" style = "font-size: 1.3vw">Sauvetage</p></a>
                <a href = "./explorer.php?category=evenement"><p class = "event" style = "font-size: 1.3vw">Événements</p></a>
                <a href = "./explorer.php?category=conseil"><p class = "advice" style = "font-size: 1.3vw">Conseils</p></a>
            </div>
        </div>

        <?php
        include("./PageParts/messageForm.php");

        if(isset($_GET['answer'])) {
            $parent_message_id = getParentMessageId($_GET['answer']);
            if($parent_message_id) {
                ?>
                <div class ="parent-message">
                    <?php displayContentbyId($parent_message_id);?>
                    <span class = "container-parent-message"></span>
                </div>
            <?php
            }
            displayContentById($_GET['answer']);
            include("./PageParts/adressSearch.php");
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./PageParts/newMessageForm.php");
            mainMessagesQuery($loginStatus, 'explorer', $_GET['answer']);
        }
        elseif(isset($_GET['category'])) {
            displayContentByCategory($_GET['category']);
        }
        else {
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./PageParts/newMessageForm.php");
            mainMessagesQuery($loginStatus, 'explorer', null);
        }

        ?>
    </div>
    <?php
}

?>

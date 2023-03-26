<?php

function displayContainer($type) {
    require_once("./PageParts/databaseFunctions.php");
    ConnectDatabase();
    $loginStatus = CheckLogin();

    if(isset($_POST['like']) && $loginStatus[0]) likeMessage($_POST['like']);

    if(isset($_POST["submit"])) {
        include("./PageParts/sendingMessage.php");

        sendMessage();
    }
    if(isset($_POST['animaux'])) {
        if(!empty($_POST['animaux'])) {
            foreach($_POST['animaux'] as $animal_id){
                echo $animal_id;
            }
        }
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
        <?php include ("PageParts/navigation.php");?>

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

        include("./PageParts/popupNewMessage.php");
        ?>
    </div>

    </body>

    </html>

    <?php
}

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
    } // Afficher la section des messages par défaut
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
        <?php
        include("./PageParts/messageForm.php");

        if(isset($_GET['answer'])) {
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

<?php
require_once("./PageParts/databaseFunctions.php");
ConnectDatabase();
$loginStatus = CheckLogin();

global $image;
if($loginStatus[0]) {
    $image = loadAvatar($_COOKIE['username']);
}

if(isset($_POST['like'])){
    likeMessage($_POST['like']);
}

if(isset($_POST["submit"])) {
    include("./PageParts/sendingMessage.php");
    if($_GET['reply_to']) sendMessage('answer');

    else sendMessage('message');
}
?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Twitturtle</title>
    <link rel="shortcut icon" href="./favicon.ico">
</head>

<body>
<div class = "Container">
    <?php include ("PageParts/navigation.php");?>

    <div class = "MainContainer">
        <h1>Accueil</h1>

        <?php
        if (isset($_GET['reply_to']) && !empty($_GET['reply_to'])) {
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
            ?>
        <div class = "hub-messages">
            <?php
            include("./PageParts/messageForm.php");
            if(isset($_GET['answer'])) {
                displayContentById($_GET['answer']);
                include("./PageParts/adressSearch.php");
                include("./PageParts/newMessageForm.php");

                mainMessages($loginStatus, 'explorer', $_GET['answer']);
            }
            else
                mainMessages($loginStatus, 'explorer', null);

            ?>
        </div>
    </div>

    <?php
    include("./PageParts/trends.php");

    include("./PageParts/popupNewMessage.php");
    ?>
</div>

</body>

</html>


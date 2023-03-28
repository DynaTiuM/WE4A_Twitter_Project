<?php
ob_start();

require_once("./databaseFunctions.php");

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");
require_once("../Classes/UserProfile.php");
require_once("../Classes/AnimalProfile.php");

global $globalDb;
global $globalUser;
global $globalMessage;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = User::getInstance($conn, $globalDb);
$globalMessage = Message::getInstance($conn, $globalDb);
?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <link rel = "stylesheet" href = "../css/profile.css">
    <link rel = "stylesheet" href = "../css/newMessage.css">
    <title>Profil</title>
    <link rel="shortcut icon" href="../favicon.ico">

    <?php
    include("./windows.php");
    include("./popupNewMessage.php");
    popUpNewMessage();
    ?>

</head>

<body>
<div class = "Container">
    <?php include("./navigation.php") ?>
    <div class = "MainContainer">
        <div class = "h1-container">
            <h1 style = "margin-bottom: 0.2vw">Profil</h1>
            <?php

            $username =  $_GET["username"];

            $type = determinePetOrUser($globalDb->getConnection(), $username);

            if ($type == 'user') {
                $profile = new UserProfile($conn, $username);
            } else {
                $profile = new AnimalProfile($conn, $username);
            }

            $profile->setNumberOfMessages($globalMessage->countAllMessages($username, $type));

            $profile->displayNumMessages();
            $profile->displayProfile();
            ?>
        </div>
        <div class = "spacing"></div>

        <div class = "profile">
            <?php
            if(isset($_POST['follow'])) {
                follow_unfollow($username, $type);
            }

            if($type == 'user') {
                include("./userProfileForm.php");
                $result = displayUserProfile($conn, $username);
            }
            else {
                include("./petProfileForm.php");
                displayPetProfile($conn, $username);
            }
            ?>
        </div>

        <div id="message-like-section">
            <button id="message-button" class="message-section" disabled>Messages</button>
            <?php if($type == 'user') {?>
            <button id="answer-button" class="answer-section">Réponses</button>
            <button id="like-button" class="like-section" >J'aime</button>
                <?php
            }?>
            <div id="message-content">
                <?php
                include("./messageForm.php");
                profilMessages();
                ?>
            </div>
            <?php if($type == 'user') {?>
                <div id="answer-content" style="display:none;">
                    <?php profilAnswers();?>
                </div>
                <div id="like-content" style="display:none;">
                <?php findLikedMessages();?>
                </div>
                <?php
                }?>
        </div>
    </div>

    <div id="modification-profile" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('modification-profile')">&times;</span>
            <h2 class = "window-title">Modification du profil</h2>
            <?php include("./PageParts/profileModificationForm.php"); ?>
        </div>
    </div>
    <div id="modification-pet-profile" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('modification-pet-profile')">&times;</span>
            <h2 class = "window-title">Modification du profil de l'animal</h2>
            <?php include("./PageParts/petProfileModificationForm.php"); ?>
        </div>
    </div>

    <div id="add-pet" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('add-pet')">&times;</span>
            <h2 class = "window-title">Ajout d'un animal</h2>
            <?php include("./PageParts/addPetForm.php"); ?>
        </div>
    </div>
    <?php include("./PageParts/trends.php") ?>
</div>

</body>

<script>
    // Récupération des boutons
    const messageBtn = document.getElementById("message-button");
    const answerBtn = document.getElementById("answer-button");
    const likeBtn = document.getElementById("like-button");

    // Récupération des contenus
    const messageContent = document.getElementById("message-content");
    const answerContent = document.getElementById("answer-content");
    const likeContent = document.getElementById("like-content");

    // Fonction qui désactive les boutons et affiche le contenu correspondant
    function switchContent(btn, content) {
        // On désactive tous les boutons
        messageBtn.disabled = false;
        answerBtn.disabled = false;
        likeBtn.disabled = false;

        // On cache tous les contenus
        messageContent.style.display = "none";
        answerContent.style.display = "none";
        likeContent.style.display = "none";

        // On active le bouton cliqué et affiche le contenu correspondant
        btn.disabled = true;
        content.style.display = "block";
    }
    messageBtn.addEventListener("click", function() {
        switchContent(this, messageContent);
    });

    answerBtn.addEventListener("click", function() {
        switchContent(this, answerContent);
    });

    likeBtn.addEventListener("click", function() {
        switchContent(this, likeContent);
    });
</script>
</html>



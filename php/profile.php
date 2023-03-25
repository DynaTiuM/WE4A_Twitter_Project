
<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <link rel = "stylesheet" href = "./css/profile.css">
    <link rel = "stylesheet" href = "./css/newMessage.css">
    <title>Profil</title>
    <link rel="shortcut icon" href="./favicon.ico">

    <?php include("./PageParts/windows.php"); ?>

</head>

<body>
<div class = "Container">
    <?php include ("./PageParts/navigation.php")?>
    <div class = "MainContainer">
        <h1>Profil</h1>
        <div class = "profile">
            <img class = "profile-picture" src="data:image/jpeg;base64,<?php echo base64_encode(loadAvatar($_GET['username'])); ?>"  alt="Photo de profil">

            <?php
            global $conn;
            $username =  $_GET["username"];
            $result = determinePetOrUser($conn, $username);
            if($result == 'user') {
                include("./PageParts/userProfileForm.php");
                $result = displayUserProfile($conn, $username);
            }
            else {
                include("./PageParts/petProfileForm.php");
                displayPetProfile($conn, $username);
            }

            ?>
        </div>

        <div id="message-like-section">
            <button id="message-button" class="message-section" disabled>Messages</button>
            <?php if($result == 'user') {?>
            <button id="answer-button" class="answer-section">Réponses</button>
            <button id="like-button" class="like-section" >J'aime</button>
                <?php
            }?>
            <div id="message-content">
                <?php include("./PageParts/messageForm.php");
                profilMessages();
                ?>
            </div>
            <?php if($result == 'user') {?>
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
    <?php include ("./PageParts/trends.php")?>
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


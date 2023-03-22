<?php
require_once('./PageParts/databaseFunctions.php');

ConnectDatabase();
$loginStatus = CheckLogin();

if(isset($_POST['modification-profile'])) {
    motificationProfile();
}

if(isset($_POST['add-pet'])) {
    addPet();
}

if(isset($_POST['follow'])) {
    follow($_GET['username']);
}

?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <link rel = "stylesheet" href = "./css/profil.css">
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

            $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."'";
            $result = $conn->query($query);

            if($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $prenom = $row["prenom"];
                $nom = $row["nom"];
                echo "<h3 class = 'name-profile'>" . $prenom . " " . $nom . "</h3>";

                if($_COOKIE['username'] == $username) {?>
                    <button class = "button-modify-profile" onclick="openWindow('modification-profile')">Editer le profil</button>
                    <form action="" method="post">
                        <input type="submit" name="delete_cookies" value="Déconnexion">
                    </form>
                    <?php
                    if(isset($_POST['delete_cookies'])) {
                        DestroyLoginCookie();
                    }
                }
                elseif (!checkFollow($username)) { ?>
                    <form action="" method="post" class = "button-follow">
                        <button type = "submit" name="follow" class = "button-modify-profile">Suivre</button>
                    </form>
                <?php }
                else { ?>
                    <button type = "submit" name="follow" class = "button-following">Suivi</button>
                <?php }

                echo "<h4>" ."@" . $username . "</h4>";
                if($row["bio"] != ("Bio" && null)) {
                    echo'<div class = "bio"><p>' . $row["bio"].'</p></div>';
                }
            }

            ?>

            <button class = "add-pet"  onclick="openWindow('add-pet')">Ajouter un animal</button>
        </div>


        <div id="message-like-section">
            <button id="message-button" class="message-section" disabled>Tweet</button>
            <button id="answer-button" class="answer-section">Réponses</button>
            <button id="like-button" class="like-section" >J'aime</button>
            <div id="message-content">
                <?php include("./PageParts/messageForm.php");
                profilMessages();
                ?>
            </div>
            <div id="answer-content" style="display:none;">
                <?php profilAnswers();?>
            </div>
            <div id="like-content" style="display:none;">
                <?php findLikedMessages();?>
            </div>
        </div>


    </div>
    <div id="modification-profile" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('modification-profile')">&times;</span>
            <h2 class = "window-title">Modification du profil</h2>
            <?php include("./PageParts/profilModificationForm.php"); ?>
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


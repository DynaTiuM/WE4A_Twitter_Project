<?php
include("./PageParts/databaseFunctions.php");
ConnectDatabase();
$loginStatus = CheckLogin();

include("./PageParts/sendingMessage.php");
?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Twitturtle</title>
    <link rel="shortcut icon" href="./favicon.ico">
    <script src="https://maps.googleapis.com/maps/api/js?key=KEY&callback=initMap"></script>

<body>
<div class = "Container">
    <?php include ("PageParts/navigation.php");?>

    <div class = "MainContainer">
        <h1>Accueil</h1>
        <?php if ($loginStatus[0]) {?>
        <div class = "NewMessage">
            <form action = "" method = "post">
                <a href = "profil.php"><img class = "AvatarMessage" src = "./images/titan.png"></a>
                <textarea name = "content" class = "message-content" placeholder="Quoi de neuf ?" rows="1" maxlength="240"></textarea>
                <span class = "Border" style="width: 80%;"></span>
                <div class = "ButtonPosition">
                    <button class = "Tweeter" type = "submit">Envoyer</button>
                </div>
                <select>
                    <option value ="option1"></option>
                    <option value ="option2">PET 1</option>
                    <option value ="option3">PET 2</option>
                </select>
                <p>HASHTAG</p>
                <button onclick="openModal()">Choisir ma localisation</button>
            </form>


            <div id="modal">
                <div id="modal-content">
                    <span onclick="closeModal()" style="float:right">&times;</span>
                    <h3>Choisir une localisation sur la carte</h3>
                    <div id="map" style="height: 400px"></div>
                    <form>
                        <label for="latitude">Latitude :</label>
                        <input type="text" id="latitude" name="latitude"><br>

                        <label for="longitude">Longitude :</label>
                        <input type="text" id="longitude" name="longitude"><br>
                    </form>
                </div>
            </div>

        </div>
            <div class = "hub-messages">
                <?php

                include("./PageParts/Messages.php");
                mainMessages($loginStatus);
                ?>
            </div>
        <?php
        }
        else {
            echo '<h2>Connectez-vous pour accéder au contenu</h2>';
        }
        ?>
    </div>

    <?php
    include("./PageParts/trends.php");
    ?>
</div>


</body>

</html>


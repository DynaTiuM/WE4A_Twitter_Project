<?php
include("./PageParts/databaseFunctions.php");
ConnectDatabase();
$loginStatus = CheckLogin();
?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Twitturtle</title>
    <link rel="shortcut icon" href="./favicon.ico">
    <script src="https://maps.googleapis.com/maps/api/js?key=KEY&callback=initMap"></script>

   <!-- <script>
        function initMap() {
            if (document.getElementById('map') != null) {
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: 48.8534, lng: 2.3488}, // Coordonnées de Paris
                    zoom: 13
                });

                var marker = new google.maps.Marker({
                    position: map.getCenter(),
                    map: map,
                    draggable: true
                });

                google.maps.event.addListener(marker, 'dragend', function() {
                    document.getElementById('latitude').value = marker.getPosition().lat();
                    document.getElementById('longitude').value = marker.getPosition().lng();
                });

                google.maps.event.addListener(map, 'click', function(event) {
                    marker.setPosition(event.latLng);
                    document.getElementById('latitude').value = event.latLng.lat();
                    document.getElementById('longitude').value = event.latLng.lng();
                });
            }
        }

        function openModal() {
            document.getElementById('modal').style.display = 'block';
            initMap();
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script> -->
</head>

<body>
<div class = "Container">
    <?php include ("PageParts/navigation.php");?>

    <div class = "MainContainer">
        <h1>Accueil</h1>
        <?php if ($loginStatus[0]) {?>
        <div class = "NewMessage">
            <a href = "profil.php"><img class = "AvatarMessage" src = "./images/titan.png"></a>
            <textarea class = "Content" placeholder="Quoi de neuf ?" rows="1" maxlength="240"></textarea>
            <span class = "Border" style="width: 80%;"></span>
            <div class = "ButtonPosition">
                <a><button class = "Tweeter">Envoyer</button></a>
            </div>
            <select>
                <option value ="option1"></option>
                <option value ="option2">PET 1</option>
                <option value ="option3">PET 2</option>
            </select>
            <p>HASHTAG</p>
            <button onclick="openModal()">Choisir ma localisation</button>

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


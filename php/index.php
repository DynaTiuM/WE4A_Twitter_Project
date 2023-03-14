<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Twitturtle</title>
    <link rel="shortcut icon" href="./favicon.ico">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATs7fPlZoEbjUiYV4dpOwg2rz4dOmtPuQ&callback=initMap"></script>

    <script>
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
    </script>

</head>

<body>
<div class = "Container">
    <div class = "Navigation">
        <a href = "index.php"><img src = "./images/logo_site.png" alt = "Logo" style = "width:5vw; height: 5vw; padding: 1.2vw; padding-bottom: 0;"></a>
        <ul>
            <li class = "NavigationButton"><a href = "index.php">Accueil</a></li>
            <li class = "NavigationButton">Explorer</li>
            <li class = "NavigationButton">Notifications</li>
            <li class = "NavigationButton">Messages</li>
            <li class = "NavigationButton"><a href = "profil.php">Profil</a></li>
            <li class = "NavigationButton"><a href = "connect.php">Se connecter</a></li>
        </ul>
        <div class = "center">
            <li class = "NavigationTweeter">Message</li>
        </div>
    </div>

    <div class = "MainContainer">
        <h1>Accueil</h1>
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
        <div class = "TimeLine">
            <p>TIMELINE</p>
        </div>
    </div>

    <div class = "Trends">
        <h2>Tendances</h2>
        <p>1ER</p>
        <p>2EME</p>
        <p>3EME</p>
    </div>
</div>


</body>

</html>


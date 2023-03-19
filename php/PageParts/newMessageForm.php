<!DOCTYPE html>

<html lang ="fr">
<head>
    <meta charset = "utf-8">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUlRIAK-8v1vAkmyRDp-pu7C4KGjshby0&libraries=places"></script>

    <link rel = "stylesheet" href = "./css/stylesheet.css">

    <?php include("adressSearch.php"); ?>
</head>

<body>

<div class = "NewMessage">
    <form action="" method="post" enctype="multipart/form-data">
        <a href="profil.php?username=<?php echo $_COOKIE['username']; ?>">
            <img class = "AvatarMessage"  src="data:image/jpeg;base64,<?php echo base64_encode($image); ?> " />
        </a>
        <label>
            <textarea name = "content" class = "message-content" placeholder="Quoi de neuf ?" rows="1" maxlength="240" required></textarea>
        </label>
        <span class = "Border" style="width: 80%;"></span>
        <div class = "ButtonPosition">
            <button class = "Tweeter" type = "submit">Envoyer</button>
        </div>


        <div class = "icons">
            <label for ="image">
                <img src="./images/image.png" class = "icon">
            </label>

        <input type="file" id = "image" name = "image" class = "invisibleFile">

            <label for ="localisation">
                <img onclick="showMap()" src="./images/localisation.png" class ="icon">
            </label>

        </form>
        <!-- Fenêtre flottante pour la carte -->
        <div id="map-container" class="localisation-window">
            <div class="localisation-content">
                <h2 class = "window-title">Localisation</h2><div id="map"></div>

                <input type="text" class = "answer" id="search" placeholder="Rechercher une adresse" name = "localisation">
                <a href = '#' class = "form-button" onclick="closeWindow('map-container')">OK</a>

            </div>
        </div>
    </div>
</div>


</body>

</html>

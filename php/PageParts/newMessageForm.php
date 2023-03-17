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

        <label>
            <input type="file" name = "image">
        </label>

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
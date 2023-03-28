<?php
$filename = basename($_SERVER['SCRIPT_FILENAME']);

global $globalDb, $globalUser;
$conn = $globalDb->getConnection();
$loginStatus = $globalUser->isLoggedIn();

$info = $globalUser->getUserInformation($_COOKIE['username']);
$avatar = $info['avatar'];
include("adressSearch.php");


?>

<!DOCTYPE html>

<html lang ="fr">
<head>
    <meta charset = "utf-8">
    <script src="https://maps.googleapis.com/maps/api/js?key=KEY&libraries=places"></script>

    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <link rel = "stylesheet" href = "./css/newMessage.css">
</head>

<body>

<div class = "new-message">

    <form action="" method="post" enctype="multipart/form-data">
        <a href="profile.php?username=<?php echo $_COOKIE['username']; ?>">
            <img class = "avatar-new-message"  src="data:image/jpeg;base64,<?php echo base64_encode($avatar); ?> " />
        </a>
        <label>
            <textarea name = "content" class = "message-content" placeholder="Nouveau message" rows="2" maxlength="240" required></textarea>
        </label>
        <span class = "Border" style="width: 80%;"></span>
        <div class = "ButtonPosition">
            <button class = "Tweeter" type = "submit" name = "submit" value = "<?php if(isset($_POST['reply_to'])) echo $_POST['reply_to']?>">Envoyer</button>
        </div>

        <div class = "icons">
            <label for ="image">
                <img src="./images/image.png" class = "icon" alt = "Image">
            </label>

            <input type="file" id = "image" name = "image" class = "invisibleFile">

            <label>
                <img onclick="showMap()" src="./images/localisation.png" class ="icon" alt = "Localisation">
            </label>

            <label>
                <img onclick="openWindow('display-pet')" src="./images/pet.png" class ="icon" alt = "Animaux">
            </label>

            <?php if(!isset($_POST['reply_to']) && !isset($_GET['answer'])) {
            ?>
            <label>
                <img onclick="openWindow('display-type', 'block')" src="./images/select.png" class ="icon" alt = "Type">
            </label>
            <?php
            }
            ?>
    </form>

    <!-- Fenêtre flottante pour la localisation -->
    <div id="map-container" class="localisation-window">
        <div class="localisation-content">
            <span style ="font-size: 1.3vw;" class="close" onclick="closeMap()">OK</span>
            <h2 class = "window-title">Localisation</h2><div id="map"></div>
            <input type="text" class = "answer" id="search" placeholder="Rechercher une adresse" name = "localisation">
        </div>
    </div>

    <div id="display-pet">
        <div class="pets-content">
            <span style ="font-size: 1.3vw;" class="close" onclick="closeWindow('display-pet')">OK</span>
            <h2 class = "window-title">Sélectionner animaux</h2>

            <?php
            $result = displayPets($_COOKIE['username']);
            if($result->num_rows == 0) {
                echo '<h4>Vous n\'avez ajouté aucun animal</h4>';
            }
            else {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="image-container">
                        <label for="<?php echo $row['id']?>">
                            <img class="pet-image" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Bouton parcourir">
                        </label>
                        <label class = "pet-name"><?php echo $row['nom']?></label>  <input class ="checkbox" type="checkbox" id="<?php echo $row['id']?>" name="animaux[]" value="<?php echo $row['id']?>">
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <div id="display-type">
        <div class = "type-content" style = "display: flex">
            <div class="classical">
                <input class = "check-radio" style = "border: none" type="radio" id="classical" name="category" value="classique">
                <label for="classical">Classique</label>
            </div>
            <div class="event">
                <input class = "check-radio" style = "border: none" type="radio" id="event" name="category" value="evenement">
                <label for="event">Événement</label><br>
            </div>
            <div class="rescue">
                <input class = "check-radio" style = "border: none" type="radio" id="rescue" name="category" value="sauvetage">
                <label for="rescue">Sauvetage</label><br>
            </div>
            <div class="advice">
                <input class = "check-radio" style = "border: none" type="radio" id="advice" name="category" value="conseil">
                <label for="advice">Conseil</label><br>
            </div>
        </div>
        <div class = "center">
            <span style ="font-size: 1.3vw; margin-right: 3vw; margin-bottom: 2vw" class="close" onclick="closeWindow('display-type')">OK</span>
        </div>

    </div>

</div>
</div>

</body>
</html>
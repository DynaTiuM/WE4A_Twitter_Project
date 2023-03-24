<?php
require_once('databaseFunctions.php');

$information = getUserInformation($_GET['username']);
$prenom = $information['prenom'];
$avatar = loadAvatar($_GET['username']);
$nom = $information['nom'];
$date = $information['date_de_naissance'];
$password = $information['mot_de_passe'];
$bio = $information['bio'];

?>

<!DOCTYPE html>

<html lang = "fr">
<form  action="" method="post" enctype="multipart/form-data">

    <div class="image-container">
        <label for="avatar">
            <img class="image-modification" src="data:image/jpeg;base64,<?php echo base64_encode($avatar); ?>" alt="Bouton parcourir">
            <div class="overlay"></div>
        </label>
    </div>

    <input id="avatar" class = "invisibleFile" type="file" name = "avatar">
    <div>
        <input name = "prenom" class = "answer" value="<?php echo $prenom; ?>">
    </div>
    <div>
        <input name = "nom" class = "answer" value="<?php echo $nom; ?>">
    </div>
    <div>
        <input type ="date" name = "date" class = "answer" value="<?php echo $date; ?>">
    </div>
    <div>
        <input name = "bio" class = "answer" value="<?php echo $bio; ?>" placeholder="Bio">
    </div>
    <div>
        <input class = "answer" type="password" id="password" name="password" value="<?php echo $password; ?>">
    </div>
    <div>
        <input class = "answer" type="password" id="confirm" name="confirm" value="<?php echo $password; ?>">
    </div>
    <br>

    <button class = "form-button" type="submit" name = "modification-profile">Modifier le profil</button>

</form>
</html>
<?php
require_once('databaseFunctions.php');

$information = getPetInformation($_GET['username']);
$nom = $information['nom'];
$avatar = loadAvatar($_GET['username']);
$age = $information['age'];
$sexe = $information['sexe'];
$bio = $information['caracteristiques'];
$espece = $information['espece'];

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
        <input name = "nom" class = "answer" value="<?php echo $nom; ?>">
    </div>
    <div>
        <input type = "number" name = "age" class = "answer" value="<?php echo $age; ?>">
    </div>
    <div>
        <label for="gender"></label>
        <label>
            <input type="radio" name="sexe" value="masculin" required <?php if($sexe == 'masculin') {?> checked <?php }?>>
            Masculin
        </label>
        <label>
            <input type="radio" name="sexe" value="feminin" <?php if($sexe == 'féminin') {?> checked <?php }?>>
            Féminin
        </label>
    </div>
    <div>
        <input name = "bio" class = "answer" value="<?php echo $bio; ?>" placeholder="Bio">
    </div>
    <div>
        <input name = "espece" class = "answer" value="<?php echo $espece; ?>" placeholder="Bio">
    </div>
    <br>

    <button class = "form-button" type="submit" name = "modification-pet-profile">Modifier le profil</button>
</form>
</html>
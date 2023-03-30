<?php
// Assurez-vous d'avoir une instance de UserProfile dans votre code.

global $profile;
?>

<!DOCTYPE html>
<html lang="fr">
<form action="" method="post" enctype="multipart/form-data">
    <div class="image-container">
        <label for="avatar">
            <img class="image-modification" src="data:image/jpeg;base64,<?php echo $profile->getUser()->getAvatarEncoded64(); ?>" alt="Bouton parcourir">
            <div class="overlay"></div>
        </label>
    </div>

    <input id="avatar" class="invisibleFile" type="file" name="avatar">
    <div>
        <input name="prenom" class="answer" value="<?php echo $profile->getUser()->getFirstName(); ?>">
    </div>
    <div>
        <input name="nom" class="answer" value="<?php echo $profile->getUser()->getLastName();; ?>">
    </div>
    <div>
        <input type="date" name="date" class="answer" value="<?php echo $profile->getUser()->getDateOfBirth(); ?>">
    </div>
    <div>
        <input name="bio" class="answer" value="<?php echo $profile->getUser()->getBio(); ?>" placeholder="Bio">
    </div>
    <div>
        <input class="answer" type="password" id="password" name="password" placeholder="Nouveau mot de passe">
    </div>
    <div>
        <input class="answer" type="password" id="confirm" name="confirm" placeholder="Confirmer le nouveau mot de passe">
    </div>
    <br>

    <button class="form-button" type="submit" name="modification-profile">Modifier le profil</button>
</form>
</html>

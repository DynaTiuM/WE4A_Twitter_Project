<?php

global $profile;
global $globalUser;
global $globalDb;

// Comme la majorité des fichiers php, on récupère les instances à la base de données
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();

$userId = $_GET['username'];
// Et ici on récupère l'id d'un animal, car nous nous situons sur le profil d'un animal
$profile = Animal::getInstanceById($conn, $globalDb, $userId);

?>

<!DOCTYPE html>

<html lang = "fr">
<body>
<form  action="" method="post" enctype="multipart/form-data">
    <div class="image-container">
        <label for="avatar">
            <img class="image-modification" src="data:image/jpeg;base64,<?php echo $profile->getAvatarEncoded64(); ?>" alt="Bouton parcourir">
            <div class="overlay"></div>
        </label>
    </div>

    <input id="avatar" class = "invisibleFile" type="file" name = "avatar">

    <div>
        <input name = "nom" class = "answer" value="<?php echo $profile->getName() ?>">
    </div>
    <div>
        <input type = "number" min = "0" max = "100" name = "age" class = "answer" value="<?php echo $profile->getAge(); ?>">
    </div>
    <div>
        <label for="gender"></label>
        <label>
            <input type="radio" name="sexe" value="masculin" class = "check-radio" required <?php if($profile->getGender() == 'masculin') {?> checked <?php }?>>
            Masculin
        </label>
        <label>
            <input type="radio" name="sexe" value="feminin" class = "check-radio" <?php if($profile->getGender() == 'féminin') {?> checked <?php }?>>
            Féminin
        </label>
    </div>
    <div>
        <input name = "bio" class = "answer" value="<?php echo $profile->getCharacteristics(); ?>" placeholder="Bio">
    </div>
    <div>
        <input name = "espece" class = "answer" value="<?php echo $profile->getSpecies(); ?>" placeholder="Espece">
    </div>
    <?php

    $userId = $_SESSION['username'];
    $globalUser = User::getInstanceById($conn, $globalDb, $userId);

    // Dans le cas où l'utilisateur qui modifie le profil de l'animal est une organisation, alors il peut changer le status de l'animal
    // C'est à dire, s'il est à adopter ou non
    if($globalUser->isOrganization()) { ?>
        <div class="adoption-center">
            <div class="adoption-container">
                <p>Est à la recherche d'un propriétaire</p>
                <div>
                    <label>
                        <input type="radio" class="check-radio" name="adoption" value="1" required <?php if ($profile->getAdoption() == 1) { ?>checked<?php } ?>>
                        Oui
                    </label>
                    <label>
                        <input type="radio" class="check-radio" name="adoption" value="0" <?php if ($profile->getAdoption() == 0) { ?>checked<?php } ?>>
                        Non
                    </label>
                </div>
            </div>
        </div>
        <?php
    }?>
    <br>

    <button class = "form-button" type="submit" name = "modification-pet-profile">Modifier le profil</button>
</form>
</body>
</html>
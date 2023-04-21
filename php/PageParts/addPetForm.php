<!DOCTYPE html>

<html lang = "fr">
<form action ="" method="post" enctype="multipart/form-data">

    <div class="image-container">
        <label for="avatar_pet">
            <img class="image-modification" src="../images/default_avatar_pet.png" alt="Bouton parcourir">
            <div class="overlay"></div>
        </label>
    </div>

    <input id="avatar_pet" class = "invisibleFile" type="file" name = "avatar_pet">
    <div>
        <input name = "id" class = "answer" placeholder="Identifiant de l'animal*" required>
    </div>
    <div>
        <input name = "nom" class = "answer" placeholder="Nom de l'animal" required>
    </div>
    <div>
        <input type ="number" min = "0" max = "100" name = "age" class = "answer" placeholder="Age">
    </div>
    <div>
        <input type ="text" name = "species" class = "answer" placeholder="Espèce">
    </div>
    <?php
    global $globalUser;
    global $globalDb;
    $globalDb = Database::getInstance();
    $conn = $globalDb->getConnection();

    $userId = $_SESSION['username'];
    $globalUser = User::getInstanceById($conn, $globalDb, $userId);

    if($globalUser->isOrganization()) {?>
    <div class="adoption-center">
        <div class="adoption-container">
            <p>Cet animal est à la recherche d'un propriétaire</p>
            <div>
                <label>
                    <input type="radio" class="check-radio" name="adoption" value="1" required>
                    Oui
                </label>
                <label>
                    <input type="radio" class="check-radio" name="adoption" value="0">
                    Non
                </label>
            </div>

        </div>
    </div>
<?php
    } ?>
    <div>
        <input name = "bio" class = "answer" placeholder="Bio">
    </div>
    <div>
        <label>
            <input type="radio" name="gender" value="masculin" required>
            Masculin
        </label>
        <label>
            <input type="radio" name="gender" value="feminin">
            Féminin
        </label>
    </div>
    <br>
    <div class="formbutton">
        <button class = "form-button" type="submit" name = "add-pet">Ajouter l'animal</button>
    </div>
    <br>
    <span>* L'identifiant de l'animal correspond au nom que vous utiliserez pour l'identifier dans un message</span>
</form>
</html>
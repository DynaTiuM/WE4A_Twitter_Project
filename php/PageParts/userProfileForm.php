<?php

function displayUserProfile($conn, $username) {
    $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."'";
    $result = $conn->query($query);

    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $prenom = $row["prenom"];
        $nom = $row["nom"];
        echo "<h3 class = 'name-profile'>" . $prenom . " " . $nom . "</h3>";

        if($_COOKIE['username'] == $username) {?>
            <button class = "button-modify-profile" onclick="openWindow('modification-profile')">Editer le profil</button>
            <form action="" method="post">
                <input type="submit" name="delete_cookies" value="Déconnexion">
            </form>

            <button class = "add-pet"  onclick="openWindow('add-pet')">Ajouter un animal</button>
            <?php
            if(isset($_POST['delete_cookies'])) {
                DestroyLoginCookie();
            }
        }
        elseif (!checkFollow($username)) { ?>
            <form action="" method="post" class = "button-follow">
                <button type = "submit" name="follow" class = "button-modify-profile">Suivre</button>
            </form>
        <?php }
        else { ?>
            <button type = "submit" name="follow" class = "button-following">Suivi</button>
        <?php }

        echo "<h4>" ."@" . $username . "</h4>";
        if($row["bio"] != ("Bio" && null)) {
            echo'<div class = "bio"><p>' . $row["bio"].'</p></div>';
        }
        return true;
    }
    return false;
}

function displayPetProfile() {

}
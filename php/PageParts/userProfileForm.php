<?php

function displayUserProfile($conn, $username) {


    if (isset($_POST['modification-profile'])) {
        motificationProfile('utilisateur');
    }

    if (isset($_POST['add-pet'])) {
        addPet();
    }

    if (isset($_POST['follow'])) {
        follow($_GET['username']);
    }


    $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."'";
    $result = $conn->query($query);

    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $prenom = $row["prenom"];
        $nom = $row["nom"];
        echo "<h3 class = 'name-profile'>" . $prenom . " " . $nom . "</h3>";
        echo "<h4>" ."@" . $username . "</h4>";
        if($row["bio"] != ("Bio" && null)) {
            echo'<div class = "bio"><p>' . $row["bio"].'</p></div>';
        }

        if($_COOKIE['username'] == $username) {?>
            <button class = "button-modify-profile" onclick="openWindow('modification-profile')">Editer le profil</button>
            <form action="" method="post">
                <input type="submit" name="delete_cookies" value="Déconnexion">
            </form>


            <button class = "add-pet" onclick="openWindow('add-pet')">Ajouter un animal</button>
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

        ?>

        <div style = "margin-top: 1vw; display: inline-block">
            <?php
            $result = displayPets($username);
            if($result->num_rows > 0) echo'<h3>Animaux</h3> <br>';
            while ($row = $result->fetch_assoc()) {
                ?>
                <a href="./profile.php?username=<?php echo $row['id']; ?>"><img style = "border-radius: 50%; width: 4vw; height: 4vw; margin-left: 1vw;" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Bouton parcourir"></a>
                <?php
            }
            ?>
        </div>
<?php
        return true;
    }
    return false;
}

?>
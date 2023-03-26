<?php
require_once('./PageParts/databaseFunctions.php');


if (!function_exists('displayPetProfile')) {
    function displayPetProfile($conn, $username) {

        ConnectDatabase();
        $loginStatus = isLogged();

        if (isset($_POST['modification-pet-profile'])) {
            motificationProfile('animal');
        }

        $query = "SELECT * FROM animal WHERE id = '$username'";
        $result = $conn->query($query);

        if($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nom = $row["nom"];
            ?>
            <img class = "profile-picture-pet" src="data:image/jpeg;base64,<?php echo base64_encode(loadAvatar($username)); ?>"  alt="Photo de profil">
            <?php
            echo "<h3 class = 'name-profile'>" . $nom . "</h3>";

            if($loginStatus) {
                if($_COOKIE['username'] == $row['maitre_username']) {?>
                    <button class = "button-modify-profile" onclick="openWindow('modification-pet-profile')">Editer le profil</button>
                    <?php
                }
                elseif (!checkFollow($username, 'animal')) { ?>
                    <form action="" method="post" class = "button-follow">
                        <button type = "submit" name="follow" class = "button-modify-profile">Suivre</button>
                    </form>
                <?php }
                else { ?>
                    <button type = "submit" name="follow" class = "button-following">Suivi</button>
                <?php }
            }


            echo "<h4>" ."@" . $username . "</h4>";
            if($row["caracteristiques"] != ("Bio" && null)) {
                echo'<div class = "bio"><p>' . $row["caracteristiques"].'</p></div>';
            }
        }
    }
}
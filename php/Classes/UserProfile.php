<?php

require_once("../Classes/Profile.php");
class UserProfile extends Profile
{

    public function __construct($conn, $username, $db)
    {
        parent::__construct($conn, $username, $db);
    }

    public function displayProfile() {
        if (isset($_POST['modification-profile'])) {
            motificationProfile('utilisateur');
        }

        global $globalUser;
        $loginStatus = $globalUser->isLoggedIn();

        $row = $this->profileUser->getUserInformation();

        if (isset($_POST['add-pet'])) {
            addPet();
        }
        global $loginStatus;
        /* DUPLICATED!!!! */
        if(isset($_POST['like']) && $loginStatus) likeMessage($_POST['like']);

        if(isset($_POST["submit"])) {
            include("./PageParts/sendingMessage.php");
            sendMessage($_POST["submit"]);
        }

        ?>
            <img <?php if($row['organisation'] == 1) { ?> class = "profile-picture-organisation" <?php } else { ?> class = "profile-picture" <?php } ?> src="data:image/jpeg;base64,<?php echo base64_encode($this->profileUser->loadAvatar()); ?>"  alt="Photo de profil">
            <?php
            if ($row['organisation']) {
                echo "<h3 class = 'name-profile'>" . $row['prenom'] . " " . $row['nom'] . "<img title=\"Ce compte est certifié car il s'agit d'une organisation\" src = '../images/organisation.png' style = 'margin-left: 0.8vw; width:1.4vw; height: 1.4vw;'></h3>";
            } else {
                echo "<h3 class = 'name-profile'>" . $row['prenom'] . " " . $row['nom'] . "</h3>";
            }

            echo "<h4>" . "@" . $this->getUsername() . "</h4>";
            if($row["bio"] != ("Bio" && null)) {
                echo'<div class = "bio"><p>' . $row["bio"].'</p></div>';
            }

            if($loginStatus) {
                if($this->profileUser->getUsername() == $this->username) {?>
                    <button class = "button-modify-profile" onclick="openWindow('modification-profile')">Editer le profil</button>

                    <button class = "add-pet" onclick="openWindow('add-pet')">Ajouter un animal</button>
                    <?php
                }
                elseif (!$this->profileUser->checkFollow($this->username, 'utilisateur')) { ?>
                    <form action="" method="post" class = "button-follow">
                        <button type = "submit" name="follow" class = "button-modify-profile">Suivre</button>
                    </form>
                <?php }
                else { ?>
                    <form action="" method="post" class = "button-follow">
                        <button type = "submit" name="follow" class = "button-following">Suivi</button>
                    </form>
                <?php }
            }
            ?>
            <div style = "display: flex; padding-top: 1.4vw">
                <h4 style = "color: #3a3a3a"><?php echo $globalUser->numFollowing()." abonnements" ?></h4>
                <!-- <h4 style = "color: #3a3a3a"><?php echo $globalUser->numFollowers("utilisateur")." abonnés" ?></h4> -->
            </div>
            <div style = "margin-top: 1vw; display: inline-block">
                <?php
                /*$result = displayPets($this->username);
                if($result->num_rows > 0) echo'<h3>Animaux</h3> <br>';
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <a href="./profile.php?username=<?php echo $row['id']; ?>"><img style = "border-radius: 50%; width: 4vw; height: 4vw; margin-left: 1vw;" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Bouton parcourir"></a>
                    <?php
                }*/
                ?>
            </div>
            <?php
    }

    public function getUsername()
    {
        return $this->username;
    }
}
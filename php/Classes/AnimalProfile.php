<?php

require_once("../Classes/Profile.php");
class AnimalProfile extends Profile {

    public function __construct($conn, $username, $db)
    {
        parent::__construct($conn, $username, $db);
        $this->profileUser = Animal::getInstanceById($this->conn, $this->db, $this->username);
    }
    public function displayProfile() {
        $userId = $_SESSION['username'];
        $globalUser = User::getInstanceById($this->conn, $this->db, $userId);
        $loginStatus = $globalUser->isLoggedIn();

        $masterUsername = $this->profileUser->getMasterUsername(); // Remplacez cette ligne par la méthode appropriée pour obtenir le username du maître
        $masterUser = User::getInstanceById($this->conn, $this->db, $masterUsername);

        if (isset($_POST['modification-pet-profile'])) {
            $this->getUser()->updateProfile('animal');
        }

        if(isset($_POST['adopt'])) {
            require_once("./PageParts/Adoption.php");
            $adoption = new Adoption($this->conn);

            // Appeler la méthode adopter() avec les paramètres appropriés
            $resultat = $adoption->adoptAnimal($this->username, $_COOKIE['username']);
        }

        ?>
        <div style = "display : flex">
            <img class = "profile-picture-pet" src="data:image/jpeg;base64,<?php echo base64_encode($this->profileUser->loadAvatar()); ?>"  alt="Photo de profil">
            <div style = "text-align: center">
                <h4 style = "margin-bottom: 0.4vw">Maître</h4>
                <a href = "./profile.php?username=<?php echo $masterUser->getUsername();?>"><img class = "profile-picture" style ="width:4.5vw; height: 4.5vw" src="data:image/jpeg;base64,<?php echo base64_encode($masterUser->loadAvatar()); ?>"  alt="Photo de profil maitre"></a>
            </div>
        </div>
        <?php
        echo "<h3 class = 'name-profile'>" . $this->profileUser->getName() . "</h3>";
        echo "<h4>" ."@" . $this->profileUser->getUsername() . "</h4>";
        if($this->profileUser->getCharacteristics() != ("Bio" && null)) {
            echo'<div class = "bio"><p>' . $this->profileUser->getCharacteristics() .'</p></div>';
        }
        ?>
        <div style = "display: flex">
            <h4 style = "color: #3a3a3a"><?php echo numFollowers($this->profileUser->getUsername(), "animal")." abonnés" ?></h4>
        </div>
        <?php
        $this->displayButton($loginStatus, $globalUser);
    }

    protected function displayButton($loginStatus, $globalUser)
    {
        if(!$loginStatus)
            return;

        $masterUsername = $this->profileUser->getMasterUsername();
        if ($globalUser->getUsername() == $masterUsername) {
            ?>
            <button class="button-modify-profile" onclick="openWindow('modification-pet-profile')">Editer le profil</button>
            <?php
        } else {
            if (!$globalUser->checkFollow($this->getUser()->getUsername(), 'animal')) { ?>
                <form action="" method="post" class="button-follow">
                    <button type="submit" name="follow" class="button-modify-profile">Suivre</button>
                </form>
            <?php } else { ?>
                <form action="" method="post" class="button-follow">
                    <button type="submit" name="follow" class="button-following">Suivi</button>
                </form>
            <?php }

            if($this->profileUser->getAdoption() == 1) {?>
                <div style = "align-self: flex-end;">
                    <form action = "./profile.php?username=<?php echo $this->profileUser->getUsername();?>" method = "post">
                        <input type = "submit" class = "add-pet" name = "adopt" value = "Adopter">
                    </form>
                </div>
                <?php
            }
        }
    }

    protected function queryMessagesAndAnswers($isMessage = true) : string {
        return "SELECT message.*
                FROM message
                    JOIN message_animaux
                        ON message.id = message_animaux.message_id
                    JOIN animal
                        ON animal.id = message_animaux.animal_id
                WHERE animal.id = ?";
    }


    public function displayBoxes() {
        ?>
        <div id="message-content">
            <?php
            $messageIds = $this->profileMessagesAndAnswers(true);
            if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
            ?>
        </div>
    <?php
    }
}
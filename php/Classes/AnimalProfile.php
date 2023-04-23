<?php

require_once("../Classes/Image.php");
require_once("../Classes/Animal.php");
require_once("../Classes/Profile.php");

// Classe dédiée à l'affichage du profil d'un animal
// Une classe AnimalProfile hérite de la classe Profile, car il s'agit d'un profil classique ayant quelques particularités différentes
class AnimalProfile extends Profile {

    /**
     * Constructeur prenant en paramètres une instance de mysqli() et une instance de la base de données, ainsi que le username d'un animal
     *
     * @param mysqli $conn Instance de la classe mysqli
     * @param Database $db Instance de la classe Database
     * @param string $username Username de l'animal
     *
     * @return void
     */
    public function __construct($conn, $username, $db)
    {
        parent::__construct($conn, $username, $db);
        // La méthode getInstanceById permet ainsi ici de lier le AnimalProfile à un animal spécifique correspondant à son username dans la base de données
        $this->profileUser = Animal::getInstanceById($this->conn, $this->db, $this->username);

    }

    /**
     * Méthode permettant d'afficher le profil d'un animal
     *
     * @return void
     *
     */
    public function displayProfile() : void {

        // On vérifie tout d'abord si le profil a été mis à jour
        $this->checkModificationProfile();

        // On récupère le username de l'utilisateur actuellement connecté grâce à notre session
        $userId = $_SESSION['username'] ?? null;
        // On récupère l'instance de notre utilisateur en fonction de son username
        $globalUser = User::getInstanceById($this->conn, $this->db, $userId);
        // Dans le cas où globalUser est null, cela signifie que l'utilisateur n'est pas connecté
        if(!$globalUser) $loginStatus = false;
        // Sinon, dans le cas où une session est active, on vérifie si l'utilisateur est bien connecté par l'intermédiaire de la méthode isLoggedIn()
        else $loginStatus = $globalUser->isLoggedIn();

        // On récupère le username du maitre de l'animal du profil
        $masterUsername = $this->profileUser->getMasterUsername();
        // On effectue la meme opération pour récupérer l'utilisateur maître
        $masterUser = User::getInstanceById($this->conn, $this->db, $masterUsername);

        if(isset($_POST['adopt'])) {
            $notification = new Notification($this->conn, $this->db);
            $notification->createNotificationAdoption($globalUser->getUsername(), $_GET['username']);
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
            <h4 style = "color: #3a3a3a"><?php echo $this->getUser()->numFollowers("animal")." abonnés" ?></h4>
        </div>
        <?php
        $this->displayButton($loginStatus, $globalUser);
    }

    /**
     *
     * Méthode permettant de vérifier si le profil a été mis à jour
     *
     * @return void
     */
    private function checkModificationProfile() : void {
        // Pour vérifier si le profil a été modifié, on regarde si le formulaire de modification de profil a été envoyé
        if (isset($_POST['modification-pet-profile'])) {

            // Si c'est le cas, on récupère toutes les informations nécessaires en les sécurisant
            $name = $this->db->secureString_ForSQL($_POST['nom']);
            $age = $this->db->secureString_ForSQL($_POST['age']);
            $gender = $this->db->secureString_ForSQL($_POST['sexe']);
            $bio = $this->db->secureString_ForSQL($_POST['bio']);
            $species = $this->db->secureString_ForSQL($_POST['espece']);
            $adoption = $this->db->secureString_ForSQL($_POST['adoption']) ?? null;
            $avatar = $_FILES['avatar'];

            // Enfin, on appelle la fonction updateProfile de la classe Animal
            // Dans le cas où adoption n'est pas null, c'est à dire que l'utilisateur a mis à jour les paramètres d'adoption,
            //on ajoute l'information dans la méthode updateProfile
            if ($adoption !== null) {
                $status = $this->getUser()->updateProfile($name, $age, $avatar, $gender, $bio, $species, $adoption);
            }
            // Sinon, on ne le met pas, et adoption sera automatiquement passé en "null" dans la méthode.
            else {
                $status =  $this->getUser()->updateProfile($name, $age, $avatar, $gender, $bio, $species);
            }

            // On affiche enfin à l'utilisateur le status de modification du profil.
            displayPopUp("Modification du profil", $status);
            ?>
            <script>
                window.onload = function() {
                    openWindow('pop-up');
                }
            </script>
            <?php
        }
    }

    protected function displayButton($loginStatus, $globalUser)
    {
        if(!$loginStatus)
            return;

        $masterUsername = $this->profileUser->getMasterUsername();
        if ($globalUser->getUsername() == $masterUsername) {
            ?>
            <button class="button-modify-profile" onclick = "openWindow('pop-up-profile')">Editer le profil</button>

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
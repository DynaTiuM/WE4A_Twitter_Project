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
    public function __construct($conn, $username, $db) {
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

        // Dans le cas où l'utilisateur actuel a appuyé sur le bouton de formulaire d'adoption,
        if(isset($_POST['adopt'])) {
            // Il est nécessaire de créer une nouvelle notification pour informer l'organisation que l'utilisateur souhaite adopter l'animal
            $notification = new Notification($this->conn, $this->db);
            // On crée ainsi une notification d'adoption par rapport au username de l'utilisateur qui souhaite adopter, et le username de l'organisation du profil
            $notification->createNotificationAdoption($globalUser->getUsername(), $_GET['username']);
        }

        // Quelques lignes de HTML/PHP permettant de mettre en ordre le profil de l'animal :
        ?>
        <div style = "display : flex">
            <img class = "profile-picture-pet" src="data:image/jpeg;base64,<?php echo $this->profileUser->getAvatarEncoded64(); ?>"  alt="Photo de profil">
            <div style = "text-align: center">
                <h4 style = "margin-bottom: 0.4vw">Maître</h4>
                <a href = "./profile.php?username=<?php echo $masterUser->getUsername();?>"><img class = "profile-picture" style ="width:4.5vw; height: 4.5vw" src="data:image/jpeg;base64,<?php echo $masterUser->getAvatarEncoded64(); ?>"  alt="Photo de profil maitre"></a>
            </div>
        </div>
        <?php
        // Getters classiques pour récupérer les attributs de l'animal du profil correspondant
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

        // Il est enfin important d'afficher les boutons en fonction de l'utilisateur qui parcours la page de profil de l'animal
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

    /**
     * Méthode permettant d'afficher les boutons du profil en fonction de l'utilisateur qui le visionne
     *
     * @param $loginStatus
     * @param $globalUser
     * @return void
     */
    protected function displayButton($loginStatus, $globalUser) : void {

        // Tout d'abord, si l'utilisateur n'est pas connecté, il est nécessaire de n'afficher aucun bouton
        if(!$loginStatus)
            return;

        // On récupère à nouveau le nom du maitre de l'animal du profil
        $masterUsername = $this->profileUser->getMasterUsername();

        // Si l'utilisateur qui visionne actuellement le profil est égal à l'utilisateur maitre du chien, cela signifie
        // Qu'il est le maitre du chien
        if ($globalUser->getUsername() == $masterUsername) {
            // Ainsi, il faut ajouter le bouton d'éditage du profil de l'animal
            ?>
            <button class="button-modify-profile" onclick = "openWindow('pop-up-profile')">Editer le profil</button>

            <?php
        }
        // Sinon, cela signifie qu'il s'agit d'un utilisateur étranger
        else {
            // On vérifier si l'utilisateur n'est pas abonné à l'animal
            if (!$globalUser->checkFollow($this->getUser()->getUsername(), 'animal')) {
                // Si c'est le cas, on afficher le bouton "Suivre"
                ?>
                <form action="" method="post" class="button-follow">
                    <button type="submit" name="follow" class="button-modify-profile">Suivre</button>
                </form>
            <?php }
            // Sinon, cela signifie que l'utilisateur est abonné, on affiche le bouton "Suivi"
            else { ?>
                <form action="" method="post" class="button-follow">
                    <button type="submit" name="follow" class="button-following">Suivi</button>
                </form>
            <?php }

            // Enfin, si l'animal est à adopter et qu'il s'agit d'un utilisateur extérieur,
            if($this->profileUser->getAdoption() == 1) {
                // On affiche un bouton permettant d'envoyer une demande d'adoption de l'animal
                ?>
                <div style = "align-self: flex-end;">
                    <form action = "./profile.php?username=<?php echo $this->profileUser->getUsername();?>" method = "post">
                        <input type = "submit" class = "add-pet" name = "adopt" value = "Adopter">
                    </form>
                </div>
                <?php
            }
        }
    }

    /**
     *
     * Méthode affichant sur le profil de l'animal les messages où il est mentionné
     *
     * @return void
     */
    public function displayBoxes() {
        // Dans le cas du profil animal, il y a seulement une section Message :
        ?>
        <div id="message-content">
            <?php
            /* On stocke les IDs des messages correspondant aux messages où l'animal est mentionné dans une variable
            /* Ces IDs sont récupérés grâce à la méthode profileMessagesAndAnswers, appartenant à la classe Profile.php
            /* En effet, cette méthod est également utilisée pour le profil des utilisateurs, elle est donc commune aux 2 types de profil.
            /* Ici on met en paramètres true car on souhaite seulement les messages, et non les réponses
            */
            $messageIds = $this->profileMessagesAndAnswers(true);

            // S'il y a des messages, on les affiche grâce à la classe Message
            if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
            ?>
        </div>
    <?php
    }
}
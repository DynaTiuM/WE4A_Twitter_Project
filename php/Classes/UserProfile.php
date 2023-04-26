<?php
require_once("../Classes/Profile.php");
require_once("../Classes/Image.php");
require_once("../Classes/Animal.php");


/**
 * Classe qui hérite de la classe Profile, avec des spécificités propres au profil d'un utilisateur
 */
class UserProfile extends Profile {

    /**
     * Constructeur de base de la classe UserProfile
     *
     * @param $conn
     * @param $username
     * @param $db
     */
    public function __construct($conn, $username, $db)
    {
        parent::__construct($conn, $username, $db);
        $this->profileUser = User::getInstanceById($this->conn, $this->db, $this->username);
    }

    /**
     * Méthode permettant d'afficher le profil utilisateur
     *
     * @return mixed|void
     */
    public function displayProfile() {
        // Nous créons une instance de la classe User qui correspondra à l'utilisateur qui visite actuellement le profil
        $userId = $_SESSION['username'] ?? null;
        $globalUser = User::getInstanceById($this->conn, $this->db, $userId);
        // Si aucun utilisateur n'est trouvé par rapport au username de l'utilisateur qui visite le profil, cela signifie que la personne qui visite ce profil n'est pas connectée
        if(!$globalUser) $loginStatus = false;
        // Sinon, on effectue la vérification de connexion si une instance est trouvée
        else $loginStatus = $globalUser->isLoggedIn();

        // Si le formulaire d'ajout d'un animal est posté :
        if (isset($_POST['add-pet'])) {
            // On récupère les données du formulaire
            $id = $_POST['id'];
            $name = $_POST['nom'];
            $age = $_POST['age'];
            $species = $_POST['species'];
            $bio = $_POST['bio'];
            $gender = $_POST['gender'];
            // Si le critère d'adoption est renseigné, on l'ajoute aussi
            if(isset($_POST['adoption'])) {
                $adoption = $_POST['adoption'];
            }
            else {
                $adoption = null;
            }

            // Si un avatar est ajouté à l'animal :
            if(isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"]['tmp_name'])) {
                // On crée une nouvelle instance de la classe Image
                $image = new Image($_FILES["avatar"]);
                // Si cette image GD crée n'est pas nulle :
                if ($image->getGD() !== null) {
                    // On formate l'image à sa nouvelle taille
                    $image->formatImage();
                    // Et enfin on récupère l'image formatée
                    $formatedImage = $image->getFormatedImage();
                }
            }
            else {
                // Sinon, il sera tout simplement envoyé null dans la colonne d'avatar de l'animal
                $formatedImage = null;
            }

            // Finalement, on crée une instance de la classe Animal, pour créer notre nouvel animal
            $animal = new Animal($this->conn, $this->db);

            // Et on ajoute les attributs de l'animal grâce à la récupération du formulaire
            $result = $animal->setAttributes($id, $name, $globalUser->getUsername(), $age, $gender, $formatedImage, $bio, $species, $adoption);

            // S'il y a un résultat, on affiche une pop-up, avec la réponse à la création du profil de l'animal (qu'elle soit bonne ou mauvaise)
            if ($result) {
                displayPopUp("Ajout animal", $result);
                ?>
                <script>
                    window.onload = function() {
                        openWindow('pop-up');
                    }
                </script>
                <?php
            }
        }

        global $loginStatus;

        // Par ailleurs, on effectue également une vérification dans le profil :
        // Si l'utilisateur a cliqué sur le formulaire de like d'un message et qu'il est connecté, alors on like le message
        if(isset($_POST['like']) && $loginStatus) $globalUser->likeMessage($_POST['like']);

        verificationPostSubmit($this->conn, $this->db);

        // Il ne reste plus qu'à réaliser du PHP/HTML simple pour l'affichage des différentes partie du profil utilisateur :
        ?>
            <img <?php if($this->profileUser->isOrganization()) { ?> class = "profile-picture-organisation" <?php } else { ?> class = "profile-picture" <?php } ?> src="data:image/jpeg;base64,<?php echo base64_encode($this->profileUser->loadAvatar()); ?>"  alt="Photo de profil">
            <?php
        // Si le profil est une organisation, on affiche un petit logo en or à droite du nom de l'utilisateur
            if ($this->profileUser->isOrganization()) {
                echo "<h3 class = 'name-profile'>" . $this->profileUser->getFirstName() . " " . $this->profileUser->getLastName() . "<img title=\"Ce compte est certifié car il s'agit d'une organisation\" src = '../images/organisation.png' style = 'margin-left: 0.8vw; width:1.4vw; height: 1.4vw;'></h3>";
            } else {
                echo "<h3 class = 'name-profile'>" . $this->profileUser->getFirstName() . " " . $this->profileUser->getLastName() . "</h3>";
            }

            echo "<h4>" . "@" . $this->getUsername() . "</h4>";
            if($this->profileUser->getBio() != ("Bio" && null)) {
                echo'<div class = "bio"><p>' . $this->profileUser->getBio().'</p></div>';
            }
        ?>
        <div style = "display: flex; padding-top: 1.4vw">
            <h4 style = "color: #3a3a3a"><?php echo $this->getUser()->numFollowing()." abonnements" ?></h4>
            <h4 style = "color: #3a3a3a"><?php echo $this->getUser()->numFollowers("utilisateur")." abonnés" ?></h4>
        </div>
        <?php
        $this->displayButton($loginStatus, $globalUser);
        ?>

        <div style = "margin-top: 1vw; display: inline-block">
            <?php
            $result = $this->getUser()->displayPets();
            if($result->num_rows > 0) echo'<h3>Animaux</h3> <br>';
            while ($row = $result->fetch_assoc()) {
                ?>
                <a href="./profile.php?username=<?php echo $row['id']; ?>"><img style = "border-radius: 50%; width: 4vw; height: 4vw; margin-left: 1vw; object-fit: cover" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Bouton parcourir"></a>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * Méthode permettant d'afficher les boutons du profil utilisateur en fonction de l'utilisateur qui consulte le profil
     *
     * @param $loginStatus
     * @param $globalUser
     * @return mixed|void
     */
    protected function displayButton($loginStatus, $globalUser) {

        // Si l'utilisateur qui consulte le profil n'est pas connecté, on n'affiche aucun bouton, on sort de la méthode
        if(!$loginStatus)
            return;

        // Si l'utilisateur qui consulte le profil est l'utilisateur du profil :
        if ($globalUser->getUsername() == $this->getUser()->getUsername()) {
            // On ajoute des boutons qui lui permet de configurer son profil
            ?>
            <button class="button-modify-profile" onclick="openWindow('pop-up-profile')">Editer le profil</button>
            <button class="add-pet" onclick="openWindow('add-pet')">Ajouter un animal</button>
            <?php
        } else {
            // Sinon, il s'agit d'un utilisateur connecté. On vérifie s'il suit l'utilisateur du profil :
            if (!$globalUser->checkFollow($this->getUser()->getUsername(), 'utilisateur')) {
                // S'il ne le suit pas, on affiche le bouton suivre, sinon on affiche le bouton suivi
                ?>
                <form action="" method="post" class="button-follow">
                    <button type="submit" name="follow" class="button-modify-profile">Suivre</button>
                </form>
            <?php } else { ?>
                <form action="" method="post" class="button-follow">
                    <button type="submit" name="follow" class="button-following">Suivi</button>
                </form>
            <?php }
        }
    }

    // Getter
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Méthode permettant l'affichage des différentes catégories de message dans le profil utilisateur
     *
     * @return void
     */
    public function displayBoxes() {
        ?>
        <div id="message-content">
            <?php
            // La première section concerne l'affichage des messages
            $messageIds = $this->profileMessagesAndAnswers(true);
            // S'il y a des messages récupérés, on les affiche
            if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
            ?>
        </div>
        <div id="answer-content" style="display:none;">
            <?php
            // La deuxième concerne l'affichage des réponses
            $messageIds = $this->profileMessagesAndAnswers(false);
            // Meme mécanisme que les messages
            if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
            ?>
        </div>
        <div id="like-content" style="display:none;">
            <?php
            // La troisième concerne l'affichage des messages likés
            $messageIds = $this->likedMessages();
            if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
            ?>
        </div>
<?php
    }
}
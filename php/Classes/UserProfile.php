<?php
require_once("../Classes/Profile.php");
require_once("../Classes/Image.php");
require_once("../Classes/Animal.php");
class UserProfile extends Profile {

    public function __construct($conn, $username, $db)
    {
        parent::__construct($conn, $username, $db);
        $this->profileUser = User::getInstanceById($this->conn, $this->db, $this->username);
    }

    public function displayProfile() {
        $userId = $_SESSION['username'] ?? null;
        $globalUser = User::getInstanceById($this->conn, $this->db, $userId);
        if(!$globalUser) $loginStatus = false;
        else $loginStatus = $globalUser->isLoggedIn();


        if (isset($_POST['add-pet'])) {
            // Récupérer les données du formulaire
            $id = $_POST['id'];
            $name = $_POST['nom'];
            $age = $_POST['age'];
            $species = $_POST['species'];
            if(isset($_POST['adoption'])) {
                $adoption = $_POST['adoption'];
            }
            else {
                $adoption = null;
            }
            if(isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"]['tmp_name'])) {
                $image = new Image($_FILES["avatar"]);
                if ($image->getGD() !== null) {
                    $image->formatImage();
                    $formatedImage = $image->getFormatedImage();
                }
            }
            else {
                $formatedImage = null;
            }

            $bio = $_POST['bio'];
            $gender = $_POST['gender'];
            $avatar_pet = $_FILES['avatar_pet']['name']; // Vous devrez également gérer le téléchargement du fichier

            // Créer une instance de la classe Animal
            $animal = new Animal($this->conn, $this->db);

            // Appeler la fonction addAnimal
            $result = $animal->setAttributes($id, $name, $globalUser->getUsername(), $age, $gender, $formatedImage, $bio, $species, $adoption);

            if ($result) {
                // L'animal a été ajouté avec succès
                // Rediriger vers une autre page ou afficher un message de succès
            } else {
                // Une erreur s'est produite lors de l'ajout de l'animal
                // Afficher un message d'erreur ou gérer l'erreur
            }
        }
        global $loginStatus;
        /* DUPLICATED!!!! */
        if(isset($_POST['like']) && $loginStatus) $globalUser->likeMessage($_POST['like']);

        if(isset($_POST["submit"])) {
            include("./sendingMessage.php");
            Message::sendMessage($this->conn, $this->db, $_POST["submit"]);
        }

        ?>
            <img <?php if($this->profileUser->isOrganization()) { ?> class = "profile-picture-organisation" <?php } else { ?> class = "profile-picture" <?php } ?> src="data:image/jpeg;base64,<?php echo base64_encode($this->profileUser->loadAvatar()); ?>"  alt="Photo de profil">
            <?php
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
                <a href="./profile.php?username=<?php echo $row['id']; ?>"><img style = "border-radius: 50%; width: 4vw; height: 4vw; margin-left: 1vw;" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Bouton parcourir"></a>
                <?php
            }
            ?>
        </div>
        <?php
    }

    protected function displayButton($loginStatus, $globalUser) {

        if(!$loginStatus)
            return;

        if ($globalUser->getUsername() == $this->getUser()->getUsername()) {
            ?>
            <button class="button-modify-profile" onclick="openWindow('pop-up-profile')">Editer le profil</button>
            <button class="add-pet" onclick="openWindow('add-pet')">Ajouter un animal</button>
            <?php
        } else {
            if (!$globalUser->checkFollow($this->getUser()->getUsername(), 'utilisateur')) { ?>
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

    protected function queryMessagesAndAnswers($isMessage): string {
        if ($isMessage) {
            return "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id IS NULL) ORDER BY date DESC";
        } else {
            return "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id is not NULL) ORDER BY date DESC";
        }
    }

    public function addAnimal($id, $name, $age, $species, $adoption, $bio, $gender, $avatar_pet) {
        $stmt = $this->conn->prepare("INSERT INTO animals (id, name, age, species, adoption, bio, gender, avatar_pet) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiissss", $id, $name, $age, $species, $adoption, $bio, $gender, $avatar_pet);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function displayBoxes() {
        ?>
        <div id="message-content">
            <?php
            $messageIds = $this->profileMessagesAndAnswers(true);
            if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
            ?>
        </div>
        <div id="answer-content" style="display:none;">
            <?php
                $messageIds = $this->profileMessagesAndAnswers(false);
                if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
                ?>
        </div>
        <div id="like-content" style="display:none;">
            <?php
            $messageIds = $this->likedMessages();
            if($messageIds) Message::displayMessages($this->conn, $this->db, $messageIds);
            ?>
        </div>
<?php
    }
}
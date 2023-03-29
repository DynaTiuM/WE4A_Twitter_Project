<?php
require_once("../Classes/Profile.php");
require_once("../Classes/UserInterface.php");
require_once("../Classes/Image.php");
require_once("../Classes/Animal.php");
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
            if(isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"])) {
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
                <h4 style = "color: #3a3a3a"><?php echo $this->getUser()->numFollowing()." abonnements" ?></h4>
                <h4 style = "color: #3a3a3a"><?php echo $this->getUser()->numFollowers("utilisateur")." abonnés" ?></h4>
            </div>
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

    public function profilMessagesAndAnswers($isMessage) {
        if ($isMessage) {
            $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id IS NULL) ORDER BY date DESC";
        } else {
            $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id is not NULL) ORDER BY date DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
            return $messageIds;
        }
        else {
            if ($isMessage) {
                echo '<br><h4>Ce profil ne contient aucun message</h4>';
            } else {
                echo '<br><h4>Ce profil n\'a répondu à aucun message</h4>';
            }
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
}
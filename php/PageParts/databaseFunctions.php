<?php

date_default_timezone_set('CET');
// Function to open connection to database
//--------------------------------------------------------------------------------
function ConnectDatabase() {
    // Create connection
    $servername = "localhost";
    $username = "root2";
    $password = "root";
    $dbname = "we4a_project";

    // Check if connection exists already
    if (!isset($GLOBALS['conn'])) {
        $GLOBALS['conn'] = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($GLOBALS['conn']->connect_error) {
            die("Connection failed: " . $GLOBALS['conn']->connect_error);
        }
    }

    return $GLOBALS['conn'];
}

//Function to clean up an user input for safety reasons
//--------------------------------------------------------------------------------
function SecurizeString_ForSQL($string) {
    $string = trim($string);
    $string = stripcslashes($string);
    $string = addslashes($string);
    return htmlspecialchars($string);
}

// Function to check login. returns an array with 2 booleans
// Boolean 1 = is login successful, Boolean 2 = was login attempted
//--------------------------------------------------------------------------------
function CheckLogin(){
    global $conn, $username, $userID;

    $error = NULL;
    $loginSuccessful = false;

//Données reçues via formulaire?
    if(isset($_POST["username"]) && isset($_POST["password"])){
        $username = SecurizeString_ForSQL($_POST["username"]);
        $password = $_POST["password"];
        $loginAttempted = true;
    }
//Données via le cookie?
    elseif ( isset( $_COOKIE["username"] ) && isset( $_COOKIE["password"] ) ) {
        $username = $_COOKIE["username"];
        $password = $_COOKIE["password"];
        $loginAttempted = true;
    }
    else {
        $loginAttempted = false;
    }

//Si un login a été tenté, on interroge la BDD
    if ($loginAttempted){
        $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."'";
        $result = $conn->query($query);

        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $hashed_password = $row['mot_de_passe'];

            if (password_verify($password, $hashed_password)) {
                CreateLoginCookie($username, $hashed_password);
                $loginSuccessful = true;
            } else {
                $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte";
            }
        }
        else {
            $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte";
        }
    }

    return array($loginSuccessful, $loginAttempted, $error, $userID);
}

function isLogged() {
    $loginAttempted = false;
    if (isset($_COOKIE["username"]) && isset($_COOKIE["password"])) {
        $username = $_COOKIE["username"];
        $password = $_COOKIE["password"];
        $loginAttempted = true;
    }
    if ($loginAttempted) {
        global $conn;
        $query = "SELECT * FROM `utilisateur` WHERE username = ? AND mot_de_passe = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

//Méthode pour créer/mettre à jour le cookie de Login
//--------------------------------------------------------------------------------
function CreateLoginCookie($username, $encryptedPasswd){
    setcookie("username", $username, time() + 24*3600 );
    setcookie("password", $encryptedPasswd, time() + 24*3600);
}

function motificationProfile($type) {

    $username = SecurizeString_ForSQL($_GET["username"]);

    modificationAvatarProfile('utilisateur', $username);

    global $conn;

    $nom = SecurizeString_ForSQL($_POST['nom']);
    $bio = SecurizeString_ForSQL($_POST['bio']);

    if($type == 'utilisateur') {
        $prenom = SecurizeString_ForSQL($_POST['prenom']);
        $date = SecurizeString_ForSQL($_POST['date']);
        $password = SecurizeString_ForSQL($_POST['password']);

        $query = "UPDATE utilisateur SET prenom = '$prenom', nom = '$nom', date_de_naissance = '$date', mot_de_passe = '$password', bio = '$bio' WHERE username = '" . $username. "'";
    }
    else {
        $sexe = SecurizeString_ForSQL($_POST['sexe']);
        $age = SecurizeString_ForSQL($_POST['age']);
        $espece = SecurizeString_ForSQL($_POST['espece']);
        $adoption = SecurizeString_ForSQL($_POST['adoption']);

        $query = "UPDATE animal SET nom = '$nom', age = '$age', sexe = '$sexe', caracteristiques = '$bio', espece = '$espece', adopter = '$adoption' WHERE id = '" . $username. "'";
    }
    $conn->query($query);

}

function adoptAnimal($animal_id, $adoptant_username, $conn) {
    // Vérifier si l'animal existe
    $stmt = $conn->prepare("SELECT * FROM animal WHERE id = ?");
    $stmt->bind_param("s", $animal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 0) {
        return "L'animal n'existe pas.";
    }
// Vérifier si l'animal est déjà adopté
    $stmt = $conn->prepare("SELECT * FROM adoption WHERE animal_id = ?");
    $stmt->bind_param("s", $animal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        return "L'animal est déjà adopté.";
    }
    // Insérer l'adoption dans la table adoption
    $stmt = $conn->prepare("INSERT INTO adoption (animal_id, adoptant_username, date_adoption) VALUES (?, ?, ?)");
    $date = date('Y-m-d');
    $stmt->bind_param("sss", $animal_id, $adoptant_username, $date);
    $stmt->execute();

    return "L'adoption a été effectuée avec succès.";
}

function modificationAvatarProfile($type, $username) {
    global $conn;

    // Vérification que l'image est bien envoyée
    if (isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"]["tmp_name"])) {

        // Ouverture du fichier image
        $image_file = $_FILES['avatar']['tmp_name'];
        $image_data = file_get_contents($image_file);

        // Préparation de la requête SQL en utilisant une requête préparée
        $query = $conn->prepare("UPDATE $type SET avatar = ? WHERE username = ?");

        // Liaison des paramètres
        $query->bind_param('ss', $image_data, $username);

        // Exécution de la requête
        $query->execute();

        // Fermeture de la requête
        $query->close();
    }

}


// Modifying resolution of an image
function formatImage($image) {
    // Définir la nouvelle taille
    $new_width = 500;
    $ratio = $new_width / imagesx($image);
    $new_height = imagesy($image) * $ratio;

    // Créer une nouvelle image avec la nouvelle taille
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Redimensionner l'image
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, imagesx($image), imagesy($image));

    // Enregistrer l'image redimensionnée
    ob_start();
    imagejpeg($new_image);

    return ob_get_clean();
}

// Modifying a table into a GdImage
function createImage($image){
    if (isset($image) && is_uploaded_file($image["tmp_name"])) {
        $mime_type = mime_content_type($image["tmp_name"]);
        if ($mime_type == "image/jpeg") {
            return imagecreatefromjpeg($image["tmp_name"]);
        } elseif ($mime_type == "image/png") {
            return imagecreatefrompng($image["tmp_name"]);
        }
    }
    return null;
}

function getUserInformation($username) {
    global $conn;

    $username = SecurizeString_ForSQL($username);

    $query = "SELECT * FROM utilisateur WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
}

function getPetInformation($pet) {
    global $conn;

    $id = SecurizeString_ForSQL($pet);

    $query = "SELECT * FROM animal WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
}

function displayContentById($id, $parent = false) {

    $id = SecurizeString_ForSQL($id);

    global $conn;
    $query = "SELECT * FROM message WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        include("messageForm.php");
        displayContent($result->fetch_assoc(), $parent);
    }
}

function numNotifications() {
    global $conn;
    $username = SecurizeString_ForSQL($_COOKIE['username']);
    $query = "SELECT COUNT(*) FROM notification WHERE utilisateur_username = ? AND vue = ?";
    $stmt = $conn->prepare($query);
    $vue = 0;
    $stmt->bind_param("si", $username, $vue);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_Column();

}

function isFollowing($auteur_username) {
    global $conn;
    $sql = "SELECT COUNT(*) as count 
        FROM suivre
        WHERE utilisateur_username = ?
          AND (
              (suivi_type = 'utilisateur' AND suivi_id_utilisateur = ?)
            OR (suivi_type = 'animal' AND suivi_id_animal IN (SELECT animal_id FROM message_animaux WHERE message_id IN (SELECT id FROM message WHERE auteur_username = ?)))
              )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $auteur_username, $auteur_username, $auteur_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return true;
    }

    return false;

}

function getParentMessageId($id_son) {
    global $conn;

    $id_son = SecurizeString_ForSQL($id_son);

    $stmt = $conn->prepare("SELECT parent_message_id FROM message WHERE id = ?");
    $stmt->bind_param("i", $id_son);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['parent_message_id'];
    }
    $stmt->close();
}

function displayContentByCategory($category) {
    global $conn;
    $category = SecurizeString_ForSQL($category);

    $query = "SELECT * FROM message WHERE categorie = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        include("messageForm.php");
        displayContent($result->fetch_assoc());
    }
}

function displayPets($username) {
    global $conn;

    $query = "SELECT * FROM animal WHERE maitre_username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    return $stmt->get_result();
}

function loadAvatar($username) {
    global $conn;

    $sql = "SELECT avatar FROM utilisateur WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["avatar"];
    } else {
        $sql = "SELECT avatar FROM animal WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["avatar"];
        }
    }

    return "Aucune image trouvée.";
}





function mainMessagesQuery($loginStatus, $search, $level) {
    global $conn;

    if($level == null) {
        $level_ = 'IS NULL';
    }
    else {
        $level_ = "= ".SecurizeString_ForSQL($level);
    }

    if(isset($_GET['tag'])){
        $tag = SecurizeString_ForSQL($_GET['tag']);
        $stmt = $conn->prepare("SELECT DISTINCT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message
                JOIN hashtag ON message.id = hashtag.message_id
                JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE message.contenu like ? OR hashtag.tag = ?
            ORDER BY message.date DESC");
        $like_tag = "%$tag%";
        $stmt->bind_param("ss", $like_tag, $tag);
    }
    else {
        if($search != 'subs') {
            $stmt = $conn->prepare("SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                    FROM message
                    JOIN utilisateur ON message.auteur_username = utilisateur.username
                    WHERE message.parent_message_id {$level_}
                    ORDER BY message.date DESC");

        }
        else {
            if($loginStatus) {
                $stmt = $conn->prepare("SELECT DISTINCT message.*
                        FROM message
                        LEFT JOIN message_animaux ON message.id = message_animaux.message_id
                        LEFT JOIN animal ON message_animaux.animal_id = animal.id
                        LEFT JOIN suivre ON suivre.suivi_id_utilisateur = message.auteur_username OR (suivre.suivi_type = 'animal' AND suivre.suivi_id_animal = animal.id)
                        WHERE suivre.utilisateur_username = ?
                        ORDER BY message.date DESC");
                $stmt->bind_param("s", $_COOKIE['username']);
            }
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            displayContent($row);
        }
    }
    else {
        if($level == null) echo '<h4>Aucun contenu disponible</h4>';
        else echo '<h4>Aucune réponse disponible</h4>';
    }
}

function isOwner($username) {
    global $conn;
    $query = "SELECT maitre_username FROM animal WHERE id = '$username'";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if($row['maitre_username'] == $_COOKIE['username']) return true;
    }
    return false;
}

function numFollowers($username, $type) {
    global $conn;
    $query = "SELECT COUNT(*) FROM suivre WHERE suivi_id_$type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_column();

}

function numFollowing($username) {
    global $conn;

    $query = "SELECT COUNT(*) FROM suivre WHERE utilisateur_username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_Column();

}

function likeMessage($id_message) {
    global $conn;
    $id_user = SecurizeString_ForSQL($_COOKIE['username']);
    $date = date('Y-m-d H:i:s');

    if(!isLiked($id_message)) {
        $stmt = $conn->prepare("INSERT INTO like_message (message_id, utilisateur_username, date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $id_message, $id_user, $date);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("DELETE FROM like_message WHERE message_id = ? AND utilisateur_username = ?");
        $stmt->bind_param("ss", $id_message, $id_user);
        $stmt->execute();
        $stmt->close();
    }
}

function isLiked($id_message) {
    global $conn;

    ConnectDatabase();
    $loginStatus = isLogged();

    if($loginStatus) {
        $id_user = SecurizeString_ForSQL($_COOKIE['username']);

        $query = "SELECT * FROM like_message WHERE message_id = ? AND utilisateur_username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $id_message, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result && $result->num_rows > 0) {
            return true;
        }
    }
    return false;

}

function isCommented($id_message) {
    $query = "SELECT * FROM message WHERE parent_message_id = ?";
    global $conn;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_message);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        return true;
    }
    return false;

}

function numComments($id_message) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM message WHERE parent_message_id = ?");
    $stmt->bind_param("s", $id_message);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows > 0) {
        return $result->fetch_Column();
    }
}

function numLike($id_message) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM like_message WHERE message_id = ?");
    $stmt->bind_param("i", $id_message);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows > 0) {
        return $result->fetch_Column();
    }
}

function findLikedMessages() {
    global $conn;
    $id_user = SecurizeString_ForSQL($_GET['username']);

    $query = "SELECT message_id FROM like_message WHERE utilisateur_username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();


    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $query = "SELECT * FROM message WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $row['message_id']);
            $stmt->execute();
            $result2 = $stmt->get_result();

            if($result2){
                $row2 = $result2->fetch_assoc();
                displayContent($row2);
            }
        }
    }
    else {
        echo '<br><h4>Ce profil n\'a aimé aucun message</h4>';
    }

}

function countAllMessages($username, $type) {
    global $conn;

    if ($type == "utilisateur") {
        $query = "SELECT COUNT(*) FROM message WHERE auteur_username = ?";
    } else {
        $query = "SELECT COUNT(*) FROM message_animaux WHERE animal_id = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_Column();

}

function profilMessages() {
    global $conn;

    $username = SecurizeString_ForSQL($_GET["username"]);

    $type = determinePetOrUser($conn, $username);
    if($type == 'user') {
        $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = ? AND parent_message_id IS NULL) ORDER BY date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();


        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                displayContent($row);
            }
        }
        else {
            echo '<br><h4>Ce profil ne contient aucun message</h4>';
        }
    }
    else {
        $query = "SELECT message.*
            FROM message
                JOIN message_animaux
                    ON message.id = message_animaux.message_id
                JOIN animal
                    ON animal.id = message_animaux.animal_id
            WHERE animal.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                displayContent($row);
            }
        }
        else {
            echo '<br><h4>Ce profil ne contient aucun message</h4>';
        }
    }
}

function profilAnswers() {
    global $conn;

    $username = SecurizeString_ForSQL($_GET["username"]);

    $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
            FROM message 
            JOIN utilisateur ON message.auteur_username = utilisateur.username
            WHERE (auteur_username = '$username' AND parent_message_id is not NULL) ORDER BY date DESC";
    $result = $conn->query($query);

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            displayContent($row);
        }
    }
    else {
        echo '<br><h4>Ce profil n\'a répondu à aucun message</h4>';
    }
}

function getInformationMessage($row) {
    global $conn;

    $auteur_username = $row['auteur_username'];
    $contenu = $row['contenu'];
    $date = $row['date'];
    $id = $row['id'];

    // Convertir la date en timestamp
    $timestamp = strtotime($date);

    // Calculer la différence de temps
    $diff = date_diff(new DateTime("@$timestamp"), new DateTime());

    $days = $diff->d;
    $hours = $diff->h;
    $minutes = $diff->i;
    $seconds = $diff->s;

    if ($days > 0) {
        $diff = $days."j";
    } elseif ($hours > 0) {
        $diff = $hours."h";
    } elseif ($minutes > 0) {
        $diff = $minutes."m";
    } else {
        $diff = $seconds."s";
    }

    $avatar = loadAvatar($auteur_username);

    $image = $row["image"];
    $localisation = $row['localisation'];
    $category = $row['categorie'];

    $stmt = $conn->prepare("SELECT nom, prenom, organisation FROM utilisateur JOIN message ON utilisateur.username = message.auteur_username WHERE auteur_username = ?");
    $stmt->bind_param("s", $auteur_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result) {
        $row = $result->fetch_assoc();
        $prenom = $row['prenom'];
        $nom = $row['nom'];
        $organisation = $row['organisation'];
    }

    return array($id, $contenu, $diff, $avatar, $image, $localisation, $auteur_username, $prenom, $nom, $category, $organisation);
}

function follow_unfollow($to_follow, $type) {
    global $conn;
    if($type == 'user') $type = 'utilisateur';
    else $type = 'animal';

    $stmt = $conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
    $stmt->bind_param("ss", $_COOKIE['username'], $to_follow);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
        $stmt->bind_param("ss", $_COOKIE['username'], $to_follow);
        $stmt->execute();
        return;
    }

    $stmt = $conn->prepare("INSERT INTO suivre (utilisateur_username, suivi_type, suivi_id_$type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_COOKIE['username'], $type, $to_follow);
    $stmt->execute();

}

function checkFollow($to_follow, $type) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_type = ? AND suivi_id_$type = ?");
    $stmt->bind_param("sss", $_COOKIE['username'], $type, $to_follow);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function verifyUnicity($parameter) {
    global $conn;
    $query = "(SELECT username FROM utilisateur WHERE username = ?) UNION (SELECT id FROM animal WHERE id = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $parameter, $parameter);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        return false;
    }
    return true;
}

function addPet() {
    global $conn;

    if(!isset($_POST['adoption'])) {
        $adoption = 0;
    }
    else {
        $adoption = SecurizeString_ForSQL($_POST['adoption']);
    }
    if(!verifyUnicity($_POST['id'])) return "Identifiant déjà existant !";

    if (isset($_FILES["avatar_pet"]) && is_uploaded_file($_FILES["avatar_pet"]["tmp_name"])) {
        $image = file_get_contents($_FILES["avatar_pet"]["tmp_name"]);

        $query = "INSERT INTO animal (id, nom, maitre_username, age, sexe, avatar, caracteristiques, espece, adopter) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssisssss", $_POST['id'], $_POST['nom'], $_COOKIE['username'], $_POST['age'], $_POST['gender'], $image, $_POST['bio'], $_POST['species'], $_POST['adoption']);
        $stmt->execute();
        $stmt->close();
        return "Animal ajouté!";
    }

    $avatar = file_get_contents('images/default_avatar_pet.png');
    $avatarBLOB = mysqli_real_escape_string($conn, $avatar);
    $query = "INSERT INTO animal (id, nom, maitre_username, age, sexe, avatar, caracteristiques, espece, adopter) 
              VALUES ('" . $_POST['id'] . "', '" . $_POST['nom'] . "', '" . $_COOKIE['username'] . "', " . $_POST['age'] . ", '" . $_POST['gender'] . "', '$avatarBLOB', '" . $_POST['bio'] . "', '" . $_POST['species'] . "', '$adoption')";
    $conn->query($query);

    return "Animal ajouté!";
}

function determinePetOrUser($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return 'user';
    }
    return 'pet';

}

function findPets($id) {
    global $conn;

    $query = "SELECT animal.* FROM animal JOIN message_animaux ON animal.id = message_animaux.animal_id WHERE message_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result();
}

function changePassword($username, $new_password) {
    global $conn;
    $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE username = ?");
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt->bind_param("ss", $hashed_password, $username);
    $stmt->execute();
}

function getNotifications() {
    global $conn;
    $username = $_COOKIE['username'];
    $query = "SELECT notification.*, message.*, utilisateur.*, GROUP_CONCAT(animal.nom SEPARATOR ', ')
        FROM notification
        INNER JOIN message ON notification.message_id = message.id
        LEFT JOIN message_animaux ON message.id = message_animaux.message_id
        LEFT JOIN animal ON message_animaux.animal_id = animal.id
        INNER JOIN utilisateur ON message.auteur_username = utilisateur.username
        WHERE notification.utilisateur_username = ?
        GROUP BY notification.id
        ORDER BY notification.vue ASC, notification.date DESC;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        return $result;
    }
    return null;

}

function markNotificationAsRead($message_id) {
    global $conn;
    $query = "UPDATE notification SET vue = 1 WHERE message_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
}

//Méthode pour détruire les cookies de Login
//--------------------------------------------------------------------------------
function DestroyLoginCookie(){
    setcookie("username", NULL, -1);
    setcookie("password", NULL, -1);
}

// Function to check a new account form
//--------------------------------------------------------------------------------
function CheckNewAccountForm(){
    global $conn;

    $creationAttempted = false;
    $creationSuccessful = false;
    $error = NULL;
    $completed = isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["confirm"])
            && isset($_POST["prenom"]) && isset($_POST["nom"])
            && isset($_POST["date_de_naissance"]) &&  isset($_POST["organisation"]);

    //Données reçues via formulaire?
    if($completed && verifyUnicity($_POST['username'])){

        $creationAttempted = true;

        //Form is only valid if password == confirm, and username is at least 4 char long
        if ( strlen($_POST["username"]) < 4 ){
            $error = "Un nom utilisateur doit avoir une longueur d'au moins 4 lettres";
        }
        elseif ( $_POST["password"] != $_POST["confirm"] ){
            $error = "Le mot de passe et sa confirmation sont différents";
        }
        else {
            $email = SecurizeString_ForSQL($_POST["email"]);
            $username = SecurizeString_ForSQL($_POST["username"]);
            $nom = SecurizeString_ForSQL($_POST["nom"]);
            $prenom = SecurizeString_ForSQL($_POST["prenom"]);
            $date_de_naissance = $_POST["date_de_naissance"];
            $organisation = $_POST["organisation"];
            $avatar = file_get_contents('images/default_avatar.png');
            $avatarBLOB = mysqli_real_escape_string($conn, $avatar);
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

            $query = "INSERT INTO `utilisateur` VALUES ('$email', '$username', '$nom', '$prenom', '$date_de_naissance', '$password', '$avatarBLOB', '$organisation', null )";
            $conn->query($query);

            if( mysqli_affected_rows($conn) == 0 )
            {
                $error = "Erreur lors de l'insertion SQL. Essayez un nom/password sans caractères spéciaux";
            }
            else {
                $creationSuccessful = true;
            }
        }
	}
    else {
        $error = "Nom d'utilisateur déjà existant";
    }

    return array($creationAttempted, $creationSuccessful, $error);
}

// Function to close connection to database
//--------------------------------------------------------------------------------
function DisconnectDatabase(){
	global $conn;
	$conn->close();
}

// Function to get current URL, without file name
//--------------------------------------------------------------------------------
function GetUrl() {
    $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
    $url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
    $url .= dirname($_SERVER["REQUEST_URI"]);
    return $url;
}


?>
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
    $string = htmlspecialchars($string);
    return $string;
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
		$password = md5($_POST["password"]);
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
        $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."' AND mot_de_passe ='".$password."'";
        $result = $conn->query($query);

        if ($result->num_rows > 0){
            CreateLoginCookie($username, $password);
            $loginSuccessful = true;
        }
        else {
            $error = "Ce couple login/mot de passe n'existe pas. Créez un Compte";
        }
    }

    return array($loginSuccessful, $loginAttempted, $error, $userID);
}

function isLogged() {
    $loginAttempted = false;
    if ( isset( $_COOKIE["username"] ) && isset( $_COOKIE["password"] ) ) {
        $username = $_COOKIE["username"];
        $password = $_COOKIE["password"];
        $loginAttempted = true;
    }
    if ($loginAttempted){
        global $conn;
        $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."' AND mot_de_passe ='".$password."'";
        $result = $conn->query($query);

        if ($result->num_rows > 0){
            return true;
        }
        else {
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

    $username = $_GET["username"];

    modificationAvatarProfile('utilisateur', $username);

    global $conn;

    $nom = $_POST['nom'];
    $bio = $_POST['bio'];

    if($type == 'utilisateur') {
        $prenom = $_POST['prenom'];
        $date = $_POST['date'];
        $password = $_POST['password'];

        $query = "UPDATE utilisateur SET prenom = '$prenom', nom = '$nom', date_de_naissance = '$date', mot_de_passe = '$password', bio = '$bio' WHERE username = '" . $username. "'";
    }
    else {
        $sexe = $_POST['sexe'];
        $age = $_POST['age'];
        $espece = $_POST['espece'];

        $query = "UPDATE animal SET nom = '$nom', age = '$age', sexe = '$sexe', caracteristiques = '$bio', espece = '$espece' WHERE id = '" . $username. "'";
    }
    $conn->query($query);

}

function modificationAvatarProfile($type, $username) {
        global $conn;
        if (isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"]["tmp_name"])) {
            $image = addslashes(file_get_contents($_FILES['avatar']['tmp_name']));

            $query = "UPDATE $type SET avatar = '$image' WHERE username = '" . $username. "'";
            $conn->query($query);
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

    $query = "SELECT * FROM utilisateur WHERE username = '$username'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    }
}

function getPetInformation($pet) {
    global $conn;

    $query = "SELECT * FROM animal WHERE id = '$pet'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    }
}

function displayContentById($id, $parent = false) {

    global $conn;
    $query = "SELECT * FROM message WHERE id ='$id'";
    $result = $conn->query($query);

    if($result->num_rows > 0) {
        include("messageForm.php");
        displayContent($result->fetch_assoc(), $parent);
    }
}

function numNotifications() {
    global $conn;
    $username = SecurizeString_ForSQL($_COOKIE['username']);
    $query = "SELECT COUNT(*) FROM notification WHERE utilisateur_username = '$username' AND vue = 0";
    $result = $conn->query($query);

    return $result->fetch_Column();
}

function isFollowing($auteur_username) {
    global $conn;
    $sql = "SELECT COUNT(*) as count 
                FROM suivre
                WHERE utilisateur_username = '$auteur_username'
                  AND (
                      (suivi_type = 'utilisateur' AND suivi_id_utilisateur = 'id_utilisateur_suivi')
                    OR (suivi_type = 'animal' AND suivi_id_animal IN (SELECT animal_id FROM message_animaux WHERE message_id IN (SELECT id FROM message WHERE auteur_username = '$auteur_username')))
                      )";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return true;
    }

    return false;
}

function getParentMessageId($id_son) {
    global $conn;
    $query = "SELECT parent_message_id FROM message WHERE id = $id_son";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['parent_message_id'];
    }
}

function displayContentByCategory($category) {
    global $conn;
    $query = "SELECT * FROM message WHERE categorie = '$category'";
    $result = $conn->query($query);

    if($result->num_rows > 0) {
        include("messageForm.php");
        displayContent($result->fetch_assoc());
    }
}

function displayPets($username) {
    global $conn;

    $query = "SELECT * FROM animal WHERE maitre_username ='". $username. "'";

    return $conn->query($query);
}

function loadAvatar($username) {
    global $conn;
    $sql = "SELECT avatar FROM utilisateur WHERE username = '" . $username . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["avatar"];
    }
    else {
        $sql = "SELECT avatar FROM animal WHERE id = '" . $username . "'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["avatar"];
        }
    }

    echo "Aucune image trouvée.";
}




function mainMessagesQuery($loginStatus, $search, $level) {
    global $conn;

    if($level == null) {
        $level_ = 'IS NULL';
    }
    else {
        $level_ = "= ".$level;
    }

    if(isset($_GET['tag'])){
        $tag = $_GET['tag'];
        $query = "SELECT DISTINCT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                FROM message
                    JOIN hashtag ON message.id = hashtag.message_id
                    JOIN utilisateur ON message.auteur_username = utilisateur.username
                WHERE message.contenu like '%$tag%' OR hashtag.tag = '$tag'
                ORDER BY message.date DESC";
    }
    else {
        if($search != 'subs') {
            $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                        FROM message
                        JOIN utilisateur ON message.auteur_username = utilisateur.username
                        WHERE message.parent_message_id {$level_}
                        ORDER BY message.date DESC";

        }
        else {
            if($loginStatus) {
                $query = "SELECT DISTINCT message.*
                            FROM message
                            LEFT JOIN message_animaux ON message.id = message_animaux.message_id
                            LEFT JOIN animal ON message_animaux.animal_id = animal.id
                            LEFT JOIN suivre ON suivre.suivi_id_utilisateur = message.auteur_username OR (suivre.suivi_type = 'animal' AND suivre.suivi_id_animal = animal.id)
                            WHERE suivre.utilisateur_username = '{$_COOKIE['username']}'
                            ORDER BY message.date DESC;
                             ";
            }
        }
    }

    $result = $conn->query($query);

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
    $query = "SELECT COUNT(*) FROM suivre WHERE suivi_id_$type = '$username'";
    $result = $conn->query($query);

    return $result->fetch_Column();
}

function numFollowing($username) {
    global $conn;

    $query = "SELECT COUNT(*)FROM suivre WHERE utilisateur_username = '$username'";
    $result = $conn->query($query);

    return $result->fetch_Column();
}

function likeMessage($id_message) {
    global $conn;
    $id_user = $_COOKIE['username'];
    $date = date('Y-m-d H:i:s');

    if(!isLiked($id_message))
        $query = "INSERT INTO like_message VALUES (null, '$id_message', '$id_user', '$date')";
    else
        $query = "DELETE FROM like_message WHERE message_id = '$id_message' AND utilisateur_username = '$id_user'";

    $conn->query($query);

    return $query;
}

function isLiked($id_message) {
    global $conn;

    ConnectDatabase();
    $loginStatus = isLogged();

    if($loginStatus) {
        $id_user = $_COOKIE['username'];

        $query = "SELECT * FROM like_message WHERE message_id = '$id_message' and utilisateur_username = '$id_user'";
        $result = $conn->query($query);

        if($result && $result->num_rows > 0) {
            return true;
        }
    }
    return false;
}

function isCommented($id_message) {
    $query = "SELECT * FROM message WHERE parent_message_id = '$id_message'";
    global $conn;
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        return true;
    }
    return false;
}

function numComments($id_message) {
    global $conn;

    $query = "SELECT COUNT(*) FROM message WHERE parent_message_id = '$id_message'";
    $result = $conn->query($query);

    if($result && $result->num_rows > 0) {
        return $result->fetch_Column();
    }
}

function numLike($id_message) {
    global $conn;

    $query = "SELECT COUNT(*) FROM like_message WHERE message_id = '$id_message'";
    $result = $conn->query($query);

    if($result && $result->num_rows > 0) {
        return $result->fetch_Column();
    }
}

function findLikedMessages() {
    global $conn;
    $id_user = $_GET['username'];

    $query = "SELECT message_id FROM like_message WHERE utilisateur_username = '$id_user'";
    $result = $conn->query($query);

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $query = "SELECT * FROM message WHERE id = ". $row['message_id'];
            $result2 = $conn->query($query);
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
    if($type == "utilisateur") $query = "SELECT COUNT(*) FROM message WHERE auteur_username = '$username'";
    else $query = "SELECT COUNT(*) FROM message_animaux WHERE animal_id = '$username'";
    $result = $conn->query($query);

    return $result->fetch_Column();
}

function profilMessages() {
    global $conn;

    $username = $_GET["username"];

    $type = determinePetOrUser($conn, $username);
    if($type == 'user') {
        $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username
                FROM message
                JOIN utilisateur ON message.auteur_username = utilisateur.username
                WHERE (auteur_username = '$username' AND parent_message_id is NULL) ORDER BY date DESC";
        $result = $conn->query($query);

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
            WHERE animal.id = '$username'";
        $result = $conn->query($query);
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

    $username = $_GET["username"];

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

    $query = "SELECT nom, prenom, organisation FROM utilisateur JOIN message ON utilisateur.username = message.auteur_username WHERE auteur_username = '$auteur_username'";
    $result = $conn->query($query);

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

    $query = "SELECT * FROM suivre WHERE utilisateur_username = '". $_COOKIE['username']. "' AND suivi_id_$type = '$to_follow' ";
    $result = $conn->query($query);

    if($result->num_rows > 0) {
        $query = "DELETE FROM suivre WHERE utilisateur_username = '". $_COOKIE['username']. "' AND suivi_id_$type = '$to_follow' ";
        $conn->query($query);
        return;
    }

    $query = "INSERT INTO suivre (utilisateur_username, suivi_type, suivi_id_$type) VALUES ('" . $_COOKIE['username'] . "', '$type', '" . $to_follow . "')";

    $conn->query($query);
}

function checkFollow($to_follow, $type) {
    global $conn;
    $query = "SELECT * FROM suivre WHERE utilisateur_username = '".$_COOKIE['username']."' AND suivi_type = '$type' AND suivi_id_$type = '$to_follow'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function addPet() {
    global $conn;

    if (isset($_FILES["avatar_pet"]) && is_uploaded_file($_FILES["avatar_pet"]["tmp_name"])) {
        $image = file_get_contents($_FILES["avatar_pet"]["tmp_name"]);

        $query = "INSERT INTO animal (id, nom, maitre_username, age, sexe, avatar, caracteristiques, espece) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssissss", $_POST['id'], $_POST['nom'], $_COOKIE['username'], $_POST['age'], $_POST['gender'], $image, $_POST['bio'], $_POST['species']);
        $stmt->execute();
        $stmt->close();
        return;
    }

    $avatar = file_get_contents('images/default_avatar_pet.png');
    $avatarBLOB = mysqli_real_escape_string($conn, $avatar);
    $query = "INSERT INTO animal (id, nom, maitre_username, age, sexe, avatar, caracteristiques, espece) 
              VALUES ('" . $_POST['id'] . "', '" . $_POST['nom'] . "', '" . $_COOKIE['username'] . "', " . $_POST['age'] . ", '" . $_POST['gender'] . "', '$avatarBLOB', '" . $_POST['bio'] . "', '" . $_POST['species'] . "')";
    $conn->query($query);
}

function determinePetOrUser($conn, $username) {
    $query = "SELECT * FROM utilisateur WHERE username = '$username'";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        return 'user';
    }
    return 'pet';
}

function findPets($id) {
    global $conn;

    $query = "SELECT animal.* FROM animal JOIN message_animaux ON animal.id = message_animaux.animal_id WHERE message_id = '$id'";
    return $conn->query($query);
}

function changePassword($username, $new_password) {
    $username = SecurizeString_ForSQL($username);
    $new_password = md5($new_password);
    $query = "UPDATE utilisateur SET mot_de_passe = '$new_password' WHERE username = '$username';";
    global $conn;
    $conn->query($query);
}

function getNotifications() {
    global $conn;
    $username = SecurizeString_ForSQL($_COOKIE['username']);
    $query = "SELECT notification.*, message.*, utilisateur.*, GROUP_CONCAT(animal.nom SEPARATOR ', ')
        FROM notification
        INNER JOIN message ON notification.message_id = message.id
        LEFT JOIN message_animaux ON message.id = message_animaux.message_id
        LEFT JOIN animal ON message_animaux.animal_id = animal.id
        INNER JOIN utilisateur ON message.auteur_username = utilisateur.username
        WHERE notification.utilisateur_username = '$username'
        GROUP BY notification.id
        ORDER BY notification.vue ASC, notification.date DESC;";

    $result = $conn->query($query);
    if($result->num_rows > 0){
        return $result;
    }
    return null;
}

function markNotificationAsRead($message_id) {
    $query = "UPDATE notification SET vue = 1 WHERE message_id = $message_id";
    global $conn;
    $conn->query($query);
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
    if($completed){
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
		    $password = md5($_POST["password"]);

            $query = "INSERT INTO `utilisateur` VALUES ('$email', '$username', '$nom', '$prenom', '$date_de_naissance', '$password', '$avatarBLOB', '$organisation', null )";
            $conn->query($query);

            if( mysqli_affected_rows($conn) == 0 )
            {
                $error = "Erreur lors de l'insertion SQL. Essayez un nom/password sans caractères spéciaux";
            }
            else{
                $creationSuccessful = true;
            }
        }
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
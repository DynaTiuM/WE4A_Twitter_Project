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

//Méthode pour créer/mettre à jour le cookie de Login
//--------------------------------------------------------------------------------
function CreateLoginCookie($username, $encryptedPasswd){
    setcookie("username", $username, time() + 24*3600 );
    setcookie("password", $encryptedPasswd, time() + 24*3600);
}

function motificationProfile() {
    modificationAvatarProfile();

    global $conn;

    $username = $_GET["username"];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $date = $_POST['date'];
    $password = $_POST['password'];
    $bio = $_POST['bio'];

    $query = "UPDATE utilisateur SET prenom = '$prenom', nom = '$nom', date_de_naissance = '$date', mot_de_passe = '$password', bio = '$bio' WHERE username = '" . $username. "'";
    $conn->query($query);

}

function modificationAvatarProfile() {
        global $conn;
        $username = $_GET["username"];
        if (isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"]["tmp_name"])) {
            $image = addslashes(file_get_contents($_FILES['avatar']['tmp_name']));

            $query = "UPDATE utilisateur SET avatar = '$image' WHERE username = '" . $username. "'";
            $conn->query($query);
        }
}

function formatImage($image) {
    if (isset($image) && is_uploaded_file($image["tmp_name"])) {
        return file_get_contents($image['tmp_name']);
    }
    return 'null';
}

function getUserInformation() {
    global $conn;

    $query = "SELECT * FROM utilisateur WHERE username = '" . $_COOKIE['username']. "'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    }

}

function loadAvatar($username) {
    global $conn;
    $sql = "SELECT avatar FROM utilisateur WHERE username = '" . $username . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["avatar"];

    } else {
        echo "Aucune image trouvée.";
    }
}


function mainMessages($loginStatus) {
    global $conn;

    if(isset($_GET['tag'])){
        $tag = $_GET['tag'];
        $query = "SELECT DISTINCT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username FROM message JOIN hashtag ON message.id = hashtag.message_id JOIN utilisateur ON message.auteur_username=utilisateur.username WHERE message.contenu like '%$tag%' OR hashtag.tag = '$tag' ORDER BY message.date DESC";
    } else {
        if($loginStatus[0]) {
            $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username FROM message JOIN utilisateur ON message.auteur_username=utilisateur.username ORDER BY message.date DESC";
        }
    }
    $result = $conn->query($query);

    if($result) {
        displayContent($result);
    }
}

function profilMessages() {
    global $conn;

    $username = $_GET["username"];

    $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username FROM message JOIN utilisateur ON message.auteur_username=utilisateur.username WHERE auteur_username = '$username' ORDER BY date DESC";
    $result = $conn->query($query);

    if($result) {
        displayContent($result);
    }
}

function getInformationMessage($row) {
    $auteur_username = $row['auteur_username'];
    $contenu = $row['contenu'];
    $date = $row['date'];

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

    return array($contenu, $diff, $avatar, $image, $auteur_username);
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
    $completed = isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["confirm"])
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
            $username = SecurizeString_ForSQL($_POST["username"]);
            $nom = SecurizeString_ForSQL($_POST["nom"]);
            $prenom = SecurizeString_ForSQL($_POST["prenom"]);
            $date_de_naissance = $_POST["date_de_naissance"];
            $organisation = $_POST["organisation"];
            $avatar = file_get_contents('images/default_avatar.png');
            $avatarBLOB = mysqli_real_escape_string($conn, $avatar);
		    $password = md5($_POST["password"]);

            $query = "INSERT INTO `utilisateur` VALUES ('$username', '$nom', '$prenom', '$date_de_naissance', '$password', '$avatarBLOB', '$organisation', 'null' )";
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
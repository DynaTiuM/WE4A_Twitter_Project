<?php

date_default_timezone_set('CET');
// Function to open connection to database
//--------------------------------------------------------------------------------
function ConnectDatabase(){
    // Create connection
    $servername = "localhost";
    $username = "root2";
    $password = "root";
    $dbname = "we4a_project";
    global $conn;
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
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

		    $password = md5($_POST["password"]);

            $query = "INSERT INTO `utilisateur` VALUES ('$username', '$nom', '$prenom', '$date_de_naissance', '$password', 'null', '$organisation' )";
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

// Function to display a page with 10 posts for a blog
//--------------------------------------------------------------------------------
function DisplayPostsPage($blogID, $ownerName, $isMyBlog){
    global $conn;

    $query = "SELECT * FROM `post` WHERE `owner_login` = ".$blogID." ORDER BY `date_lastedit` DESC LIMIT 10";
    $result = $conn->query($query);
    if( mysqli_num_rows($result) != 0 ){

        if ($isMyBlog){
        ?>

        <form action="editPost.php" method="POST">
            <input type="hidden" name="newPost" value="1">
            <button type="submit">Ajouter un nouveau post!</button>
        </form>

        <?php    
        }

        while( $row = $result->fetch_assoc() ){

            $timestamp = strtotime($row["date_lastedit"]);
            echo '
            <div class="blogPost">
                <div class="postTitle">';

            if ($isMyBlog){

                echo '
                <div class="postModify">
                    <form action="editPost.php" method="GET">
                        <input type="hidden" name="postID" value="'.$row["ID_post"].'">
                        <button type="submit">Modifier/effacer</button>
                    </form>
                </div>';
            }
            else {
                echo '
                <div class="postAuthor">par '.$ownerName.'</div>
                ';
            }

            echo '
                <h3>•'.$row["title"].'</h3>
                <p>dernière modification le '.date("d/m/y à h:i:s", $timestamp ).'
            </div>
            ';

           

            echo'
            <p class="postContent">'.$row["content"].'</p>
            </div>
            ';
        }
    }
    else {
        echo '
        <p>Il n\'y a pas de post dans ce blog.</p>';

        if ($isMyBlog){
        ?>
            <form action="editPost.php" method="POST">
                <input type="hidden" name="newPost" value="1">
                <button type="submit">Ajouter un premier post!</button>
            </form>
        <?php
        }
        

    }
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
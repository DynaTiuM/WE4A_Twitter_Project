<?php
session_start();
require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once ("functions.php");

global $globalDb;
global $globalUser;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = new User($conn, $globalDb);
$newLoginStatus = $globalUser->checkLogin();

?>


<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <link rel = "stylesheet" href = "../css/connexion.css">
    <title>Connexion</title>
    <link rel="shortcut icon" href="../favicon.ico">

    <script src = "../js/windows.js"></script>

</head>

<body>
<div class = "Container">
	<?php include("./navigation.php");

    if(!$newLoginStatus[0]) {
        $newAccountStatus = $globalUser->checkNewAccountForm();
        if($newAccountStatus[1]){
            $_SESSION['username'] = $globalUser->getUsername();
        }
    }
    else {
        $_SESSION['username'] = $globalUser->getUsername();

        header("Location: subscriptions.php");
        exit();
    }
   
    if ($newLoginStatus[2] != NULL) { ?>
        <script>
            // Ouverture automatique de la fenêtre erreur-connexion
            window.onload = function() {
                openWindow('error-connection');
            }
        </script>
        <?php
    }

    displayCode();
    if(isset($_POST['submitEmail'])) {
        require_once("./sendEmail.php");
        $_SESSION['secret_code'] = mt_rand(100000, 999999);
        $_SESSION['start_time_code'] = time();
        $_SESSION['username_tmp'] = $_POST['username'];

        $secretCode = $_SESSION['secret_code'];
        $state = sendEmail($_POST['username'], $secretCode);
        if(!$state) {
            $state = 'Erreur lors de l\'envoi de l\'email';
            ?>
            <script>
                window.onload = function() {
                    openWindow('email-not-sent');
                }
            </script>
        <?php
        }
        else {
        ?>
            <script>
                window.onload = function() {
                    openWindow('code');
                }
            </script>
            <?php
        }

        displayEmailSent($state);
    }
    elseif (isset($_POST['submitCode'])) {
        if(isset($_SESSION['start_time_code'])) {
            $elapsed_time = time() - $_SESSION['start_time_code'];
            if ($elapsed_time >= 300) {
                ?>
                <script>
                    window.onload = function() {
                        openCodeWindow('code-too-late');
                    }
                </script>
                <?php
                session_destroy();
            }
            elseif ($_POST['codeValue'] != $_SESSION['secret_code']) {
                ?>
                <script>
                    window.onload = function() {
                        openCodeWindow('code-wrong');
                    }
                </script>
                <?php
            }
            else {
                header("Location: ../PageParts/changePassword.php");
            }
        }
    }
    ?>

	<div class = "MainContainer">

        <h3>Connexion à Twitturtle</h3>

        <?php if(!$newLoginStatus[0]) {?>
        <div class = "center">
            <button class = "connexion-button" onclick="openWindow('connection')">Connexion</button>
            <button class = "inscription-button" onclick="openWindow('register')">Inscription</button>
        </div>

        <?php }
        ?>

	</div>

    <?php
    include("./trends.php"); ?>

<?php
    if($newAccountStatus[1]){
        displayPopUp("Compte","Nouveau compte créé avec succès !");
        ?>
        <script>
            window.onload = function() {
                openWindow('pop-up');
            }
        </script>
    <?php
    }
    elseif ($newAccountStatus[0]){
        displayPopUp("Compte","$newAccountStatus[2]");
        ?>
        <script>
            window.onload = function() {
                openWindow('pop-up');
            }
        </script>
        <?php
    }

    displayConnection();
    displayRegister();
    displayErrorConnection();
    displayLostPassword();
    ?>
</div>
</body>


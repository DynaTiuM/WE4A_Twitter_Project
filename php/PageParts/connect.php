<?php
session_start();
require_once("../Classes/Database.php");
require_once("../Classes/User.php");

global $globalDb;
global $globalUser;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = new User($conn, $globalDb);
$newLoginStatus = $globalUser->checkLogin();


if(!$newLoginStatus[0]) {
    $newAccountStatus = $globalUser->checkNewAccountForm();

    if($newAccountStatus[1]){
        $_SESSION['username'] = $globalUser->getUsername();

        echo '<h1 class="successMessage">Nouveau compte créé avec succès!</h1>';
    }
    elseif ($newAccountStatus[0]){
        echo '<h1 class="errorMessage">'.$newAccountStatus[2].'</h1>';
    }
}
else {
    $_SESSION['username'] = $globalUser->getUsername();

    header("Location: index.php");
    exit();
}
if ($newLoginStatus[2] != NULL) { ?>
    <p class="errorMessage"><?php echo $newLoginStatus[2]; ?></p>
<?php
}
    if(isset($_POST['submitEmail'])) {
    require_once("./sendEmail.php");
    $state = sendEmail($_POST['username']);
    ?>
    <script>
        // Ouverture automatique de la fenêtre erreur-connexion
        window.onload = function() {
            openWindow('email-sent');
        }
    </script>
    <div id="email-sent" class="window-background">
        <div class="window-content">
            <?php  if($state == null) {?>

                <div><h2 class = "window-title">Utilisateur inconnu</h2></div>
                <p style = "font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1vw">Cet utilisateur n'existe pas.</p><br>
            <?php
            }
            else {?>
            <div><h2 class = "window-title">E-mail envoyé</h2></div>
            <p style = "font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1vw"><?php echo $state?></p><br>
            <?php
            } ?>
            <button class = "form-button" onclick="closeWindow('email-sent')">D'accord</button>
        </div>
    </div>
<?php
}
?>


<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <title>Connexion</title>
    <link rel="shortcut icon" href="../favicon.ico">

    <?php include("./windows.php");?>

</head>

<body>
<div class = "Container">
	<?php include("./navigation.php") ?>

	<div class = "MainContainer">

        <h3>Connexion à Twitturtle</h3>

        <?php if(!$newLoginStatus[0]) {?>
        <div class = "center">
            <button class = "connexion-button" onclick="openWindow('connexion')">Connexion</button>
            <button class = "inscription-button" onclick="openWindow('inscription')">Inscription</button>
        </div>

        <?php }
        if($newLoginStatus[2] != NULL) {
            ?>
            <script>
                // Ouverture automatique de la fenêtre erreur-connexion
                window.onload = function() {
                    openWindow('erreur-connexion');
                }
            </script>
        <?php
            }
        ?>

	</div>

    <div id="connexion" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('connexion')">&times;</span>
            <h2 class = "window-title">Connexion</h2>
            <?php include("./loginForm.php"); ?>
        </div>
    </div>

    <div id="inscription" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('inscription')">&times;</span>
            <h2 class = "window-title">Inscription</h2>
            <?php include("./newLoginForm.php"); ?>
        </div>
    </div>

    <div id="erreur-connexion" class="window-background">
        <div class="window-content">
            <div><h2 class = "window-title">Erreur de connexion</h2></div>
            <br>
            <div>
                <label>Nom d'utilisateur ou mot de passe incorrect</label>
            </div>
            <br>
            <div>
                <label>Veuillez réessayer</label>
            </div>
            <br>
            <br>
            <button class = "form-button" onclick="closeWindow('erreur-connexion')">D'accord</button>
        </div>
    </div>

    <div id="lost-password" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('lost-password')">&times;</span>
            <h2 class = "window-title">Mot de passe oublié</h2>
            <?php include("./lostPasswordForm.php"); ?>
        </div>
    </div>

    <?php include("./trends.php"); ?>
</div>
</body>


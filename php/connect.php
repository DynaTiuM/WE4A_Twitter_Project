<?php
require_once("./PageParts/databaseFunctions.php");
ConnectDatabase();
$newLoginStatus = CheckLogin();

if(!$newLoginStatus[0]) {
    $newAccountStatus = CheckNewAccountForm();
}

if(isset($_POST['submitEmail'])) {
    require_once("./PageParts/sendEmail.php");
    sendEmail($_POST['username']);
}
?>


<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Connexion</title>
    <link rel="shortcut icon" href="./favicon.ico">

    <?php include("./PageParts/windows.php");?>

</head>

<body>
<div class = "Container">
	<?php include("./PageParts/navigation.php")?>


	<div class = "MainContainer">

        <h3>Connexion à Twitturtle</h3>

        <?php if(!$newLoginStatus[0]) {?>
        <div class = "center">
            <button class = "connexion-button" onclick="openWindow('connexion')">Connexion</button>
            <button class = "inscription-button" onclick="openWindow('inscription')">Inscription</button>
        </div>

        <?php }
        else {
            header("Location: index.php");
            exit();
        }
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
            <?php include("./PageParts/loginForm.php"); ?>
        </div>
    </div>

    <div id="inscription" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('inscription')">&times;</span>
            <h2 class = "window-title">Inscription</h2>
            <?php include("./PageParts/newLoginForm.php"); ?>
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
            <?php include("./PageParts/lostPasswordForm.php"); ?>
        </div>
    </div>

    <?php include("./PageParts/trends.php"); ?>
</div>
</body>


<?php
require_once("./databaseFunctions.php");
ConnectDatabase();

if(isset($_POST['change-password'])) {
    if($_POST['new-password'] != $_POST['confirm-password']) {
        echo 'Les mots de passe ne sont pas identiques';
    }
    else changePassword($_POST['change-password'], $_POST['new-password']);
    header("Location: ../connect.php");
}

?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <title>Connexion</title>
    <link rel="shortcut icon" href="./favicon.ico">

    <?php include("./windows.php");?>

</head>

<body>
<div class = "Container">
    <div class = "MainContainer">
        <h3>Réinitialisation du mot de passe</h3>
        <p><?php echo $_GET['username']?></p>
        <form method = "post" action = "">
            <label>
                <input class = "answer" type = "text" name = "new-password" placeholder="Nouveau mot de passe">
            </label>
            <label>
                <input class = "answer" type = "text" name = "confirm-password" placeholder="Confirmer le nouveau mot de passe">
            </label>
            <input class = "form-button" name = "change-password" type = "submit" value = "<?php echo $_GET['username']?>">
        </form>
    </div>

    <div id="changed-password" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('lost-password')">&times;</span>
            <h2 class = "window-title">Mot de passe oublié</h2>
            <p>Mot de passe changé avec succès!</p>
        </div>
    </div>

</div>
</body>


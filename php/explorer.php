<?php
include("./PageParts/databaseFunctions.php");
ConnectDatabase();
$loginStatus = CheckLogin();

global $image;
if($loginStatus[0]) {
    $image = loadAvatar($_COOKIE['username']);
}
include("./PageParts/sendingMessage.php");
?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Twitturtle</title>
    <link rel="shortcut icon" href="./favicon.ico">
</head>

<body>
<div class = "Container">
    <?php include ("PageParts/navigation.php");?>

    <div class = "MainContainer">
        <h1>Accueil</h1>
        <?php if ($loginStatus[0]) { ?>

            <div class = "hub-messages">
                <?php
                include("./PageParts/messagesForm.php");
                mainMessages($loginStatus ,'explorer');
                ?>

            </div>
            <?php
        }
        else {
            echo '<h2>Connectez-vous pour accéder au contenu</h2>';
        }
        ?>
    </div>

    <?php
    include("./PageParts/trends.php");
    ?>
</div>

</body>

</html>


<?php
include("./PageParts/databaseFunctions.php");
ConnectDatabase();
CheckLogin();
?>


<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Connexion</title>
    <link rel="shortcut icon" href="./favicon.ico">
</head>

<body>
<div class = "Container">
	<?php include("./PageParts/navigation.php")?>

	<div class = "MainContainer">
        <?php include("./PageParts/loginForm.php"); ?>
	</div>

    <?php include("./PageParts/trends.php"); ?>

</div>
</body>


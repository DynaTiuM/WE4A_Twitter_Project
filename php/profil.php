<?php
include("./PageParts/databaseFunctions.php");
ConnectDatabase();
$loginStatus = CheckLogin();
?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Profil</title>
    <link rel="shortcut icon" href="./favicon.ico">
</head>

<body>
<div class = "Container">
    <?php include ("./PageParts/navigation.php")?>
    <div class = "MainContainer">
        <p>PROFIL</p>
    </div>

    <?php include ("./PageParts/trends.php")?>

</div>

</body>

</html>


<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>S'inscrire sur Twitturtle</title>
    <link rel="shortcut icon" href="./favicon.ico">
</head>
	

<body>
<div class = "Container">
	<div class = "Navigation">
		<a href = "index.php"><img src = "./images/logo_site.png" alt = "Logo" style = "width:5vw; height: 5vw; padding: 1.2vw; padding-bottom: 0;"></a>
		<ul>
			<li class = "NavigationButton"><a href = "index.php">Accueil</a></li>
			<li class = "NavigationButton">Explorer</li>
			<li class = "NavigationButton">Notifications</li>
			<li class = "NavigationButton">Messages</li>
			<li class = "NavigationButton"><a href = "profil.php">Profil</a></li>
			<li class = "NavigationButton"><a href = "connect.php">Se connecter</a></li>
		</ul>
	</div>

	<div class = "MainContainer">
        <form action="./index.php" method="post">
			<div class = "center">
            <?php
                include("./PageParts/databaseFunctions.php");
                ConnectDatabase();
                $newAccountStatus = CheckNewAccountForm();

                ?>

                <h2>Rejoignez Twitturtle</h2>
                <?php
                    if($newAccountStatus[1]){
                        echo '<h1 class="successMessage">Nouveau compte créé avec succès!</h1>';
                    }
                    elseif ($newAccountStatus[0]){
                        echo '<h1 class="errorMessage">'.$newAccountStatus[2].'</h1>';
                    }
                ?>
                <hr>
                <?php include("./PageParts/newLoginForm.php"); ?>
                <hr>
                <?php
                    DisconnectDatabase();
                ?> 
			</div>
		</form>
	</div>

    <div class = "Trends">
        <h2>Tendances</h2>
        <p>1ER</p>
        <p>2EME</p>
        <p>3EME</p>
    </div>
</div>
</body>

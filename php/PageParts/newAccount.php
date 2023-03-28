<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <title>S'inscrire sur Twitturtle</title>
    <link rel="shortcut icon" href="../favicon.ico">
</head>
	

<body>
<div class = "Container">
	<?php include("PageParts/navigation.php") ?>

	<div class = "MainContainer">
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
                    else {

                        include("./PageParts/newLoginForm.php");
                    }
                ?>
                <?php
                    DisconnectDatabase();
                ?>
			</div>
	</div>
    <?php include("./PageParts/trends.php");?>
</div>
</body>

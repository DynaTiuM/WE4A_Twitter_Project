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
        <h1>Profil</h1>
        <div class = "profil">
            <?php
            global $conn;
            if (isset($_GET['username'])) {
                $username =  $_GET["username"];

                $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."'";
                $result = $conn->query($query);

                if($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $prenom = $row["prenom"];
                    $nom = $row["nom"];
                    echo "<h3>" . $prenom . " " . $nom . "</h3>";
                    echo "<h4>" ."@" . $username . "</h4>";
                }
            }
            ?>

            <button>Editer le profil</button>
            <form action="" method="post">
                <input type="submit" name="delete_cookies" value="Déconnexion">
            </form>
            <?php
            if(isset($_POST['delete_cookies'])) {
                DestroyLoginCookie();
            }
            ?>
        </div>

        <div class = "tweets">
            <p>Tweets</p>
        </div>

        <div class = "likes">
            <p>J'aime</p>
        </div>
    </div>

    <?php include ("./PageParts/trends.php")?>
</div>

</body>

</html>


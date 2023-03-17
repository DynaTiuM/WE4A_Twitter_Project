<?php
require_once('./PageParts/databaseFunctions.php');

ConnectDatabase();
$loginStatus = CheckLogin();

if(isset($_POST['modification-profile'])) {
    motificationProfile();
}

?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Profil</title>
    <link rel="shortcut icon" href="./favicon.ico">

    <?php include("./PageParts/windows.php"); ?>

</head>

<body>
<div class = "Container">
    <?php include ("./PageParts/navigation.php")?>
    <div class = "MainContainer">
        <h1>Profil</h1>
        <div class = "profile">
            <img class = "profile-picture" src="data:image/jpeg;base64,<?php echo base64_encode(loadAvatar($_GET['username'])); ?>"  alt="Photo de profil">

            <?php
            global $conn;
            $username =  $_GET["username"];

            $query = "SELECT * FROM `utilisateur` WHERE username = '".$username."'";
            $result = $conn->query($query);

            if($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $prenom = $row["prenom"];
                $nom = $row["nom"];
                echo "<h3 class = 'name-profile'>" . $prenom . " " . $nom . "</h3>";

                if($_COOKIE['username'] == $username) {?>
                <button class = "button-modify-profile" onclick="openWindow('modification-profile')">Editer le profil</button>
<?php }
                echo "<h4>" ."@" . $username . "</h4>";
                if($row["bio"] != ("Bio" && 'null')) {
                    echo'<div class = "bio"><p>' . $row["bio"].'</p></div>';
                }
            }
            if ($loginStatus[0]) {
               if($_COOKIE['username'] == $username) {
            ?>

            <form action="" method="post">
                <input type="submit" name="delete_cookies" value="Déconnexion">
            </form>
                    <?php
                        if(isset($_POST['delete_cookies'])) {
                            DestroyLoginCookie();
                        }
                    }

            }
            ?>
        </div>

        <div class = "tweets" onclick="openWindow('tweet')">
            <h2>Tweets</h2>
            <?php include("./PageParts/messagesForm.php");
            profilMessages();
            ?>
        </div>

        <div class = "likes">
            <h2>J'aime</h2>
        </div>


    </div>
    <div id="modification-profile" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('modification-profile')">&times;</span>
            <h2 class = "window-title">Modification du profil</h2>
            <?php include("./PageParts/profilModificationForm.php"); ?>
        </div>
    </div>
    <?php include ("./PageParts/trends.php")?>
</div>

</body>

</html>


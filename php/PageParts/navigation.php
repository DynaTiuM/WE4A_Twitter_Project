<?php
require_once('./PageParts/databaseFunctions.php');

ConnectDatabase();
$loginStatus = isLogged();


include("windows.php");
if(isset($_POST['reply_to'])) include('popupNewMessage.php');
?>
<!DOCTYPE html>
<html lang = "fr">
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href="./css/navigation.css">
</head>
<body>

    <div class = "navigation">
        <a href = "index.php"><img src = "./images/logo.png" alt = "Logo" style = "width:3vw; height: 3vw; padding: 0.5vw; margin-left: 0.6vw; padding-bottom: 0;"></a>
        <ul>
            <li class="menu-item"><a href="index.php"><img src="./images/follow.png">Abonnements</a></li>
            <li class="menu-item"><a href="explorer.php"><img src="./images/explorer.png">Explorer</a></li>

            <?php if($loginStatus) { ?>
                <li class = "menu-item"><a href="notifications.php"><img src="./images/notification.png">Notifications</a></li>
                <li class = "menu-item"><a href="messages.php"><img src="./images/message.png">Messages</a></li>

                <li class = "menu-item"><a href = "profile.php?username=<?php echo urlencode($_COOKIE['username']); ?>"><img src="./images/profile.png">Profil</a></li>
                <?php
            } else {
                ?>
                <li class = "menu-item"><a href = "connect.php">Se connecter</a></li>
                <?php
            }
            ?>
        </ul>
        <?php if($loginStatus) { ?>
            <div class = "center">
                <a href="#" onclick="openWindowByNavigation('new-message')"><li class = "tweet-button">Message</li></a>
            </div>
            <?php
        }
        ?>
    </div>

</body>
</html>

<?php
require_once('./PageParts/databaseFunctions.php');

ConnectDatabase();
$loginStatus = isLogged();

if(isset($_POST["destroyCookies"])) {
    DestroyLoginCookie();
    header("Location: ./connect.php");
}

include("windows.php");
if(isset($_POST['reply_to'])) include('popupNewMessageForm.php');
?>
<!DOCTYPE html>
<html lang = "fr">
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href="./css/navigation.css">
</head>
<body>

    <div class = "navigation">
        <a href = "explorer.php"><img src = "./images/logo.png" alt = "Logo" style = "width:3vw; height: 3vw; padding: 0.5vw; margin-left: 0.6vw; padding-bottom: 0;"></a>
        <ul>
            <li class="menu-item" style = "font-weight: 900;"><a href="index.php"><img src="./images/follow.png">Abonnements</a></li>
            <li class="menu-item"><a href="explorer.php"><img src="./images/explorer.png">Explorer</a></li>

            <?php if($loginStatus) {
                $numNotifs = numNotifications();
                if($numNotifs == 0) {
                    ?>
                    <li class = "menu-item"><a href="./notifications.php"><img src="./images/notification.png">Notifications</a></li>
                    <?php
                }
                else {
                    ?>
                    <li class = "menu-item"><a href="./notifications.php"><img src="./images/notifications_not_read.png">Notifications (<?php echo $numNotifs?>)</a></li>
                    <?php
                }
                ?>
                <li class = "menu-item"><a href="messages.php"><img src="./images/message.png">Messages</a></li>

                <li class = "menu-item"><a href = "profile.php?username=<?php echo urlencode($_COOKIE['username']); ?>"><img src="./images/profile.png">Profil</a></li>
                <?php
            } else {
                ?>
                <li class = "menu-item"><a href = "connect.php"><img src="./images/enter.png">Se connecter</a></li>
                <?php
            }
            ?>
        </ul>
        <?php if($loginStatus) { ?>
                <div class = "center">
                    <form method = "post" action = "">
                        <input type ="submit" name = "new-message" class = "tweet-button" style ="border: none;" value = "Message">
                    </form>
                </div>

            <form action = "" method = "post" class = "center">
                <button type = "submit" name ="destroyCookies" class = "deconnexion">Déconnexion</button>
            </form>
            <?php

            if(isset($_POST['new-message'])) {
                require_once("./PageParts/popupnewMessage.php");
                popUpNewMessage(true);
            }
        }
        ?>
    </div>
</body>
</html>

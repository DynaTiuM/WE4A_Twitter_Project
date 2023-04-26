<?php

// On récupère la connexion à la base de données
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();

// S'il n'y a pas de sessions active :
if (session_status() == PHP_SESSION_NONE) {
    // On en démarre une nouvelle
    session_start();
}

// On récupère l'utilisateur qui regarde le site
$globalUser = User::getInstance($conn, $globalDb);

// On vérifie s'il est connecté
$loginStatus = $globalUser->isLoggedIn();

if(isset($_POST["destroySession"])) {
    session_destroy();
    header("Location: ./connect.php");
}

// Si l'utilisateur est connecté, et que le bouton d'envoi d'un nouveau message est cliqué :
if($loginStatus) {
    if(isset($_POST['new-message'])) {
        // On affiche le formulaire de création d'un nouveau message par pop-up
        displayNewMessageForm($conn, null);
        popUpNewMessage(true);
    }
}

?>
<!DOCTYPE html>
<html lang = "fr">
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href="../css/navigation.css">
    <script src = "../js/windows.js"></script>
</head>
<body>

    <div class = "navigation">
        <a href = "explorer.php"><img src = "../images/logo.png" alt = "Logo" style = "width:3vw; height: 3vw; margin-left: 0.6vw; padding: 0.5vw 0.5vw 0 1.2vw;"></a>
        <ul>
            <li class="menu-item" style = "font-weight: 900;"><a href="subscriptions.php"><img src="../images/follow.png">Abonnements</a></li>
            <li class="menu-item"><a href="explorer.php"><img src="../images/explorer.png">Explorer</a></li>

            <?php if($loginStatus) {
                require_once ("../Classes/Notification.php");
                // Pour la section notification de la navigation bar :
                $notification = new Notification($conn, $globalDb);
                // On récupère le nombre de notifications de l'utilisateur
                $numNotifications = $notification->numNotifications($globalUser->getUsername());
                // S'il y a aucune notification, on affiche seulement le texte "Notifications"
                if($numNotifications == 0) {
                    ?>
                    <li class = "menu-item"><a href="notifications.php"><img src="../images/notification.png">Notifications</a></li>
                    <?php
                }
                // Sinon, on affiche le nombre de notifications entre parenthèses ()
                else {
                    ?>
                    <li class = "menu-item"><a href="notifications.php"><img src="../images/notifications_not_read.png">Notifications (<?php echo $numNotifications?>)</a></li>
                    <?php
                }
                ?>
                <li class = "menu-item"><a href = "profile.php?username=<?php echo urlencode($_SESSION['username']); ?>"><img src="../images/profile.png">Profil</a></li>
                <?php
            } else {
                ?>
                <li class = "menu-item"><a href = "connect.php"><img src="../images/enter.png">Se connecter</a></li>
                <?php
            }
            ?>
        </ul>
        <?php
        // Si l'utilisateur est connecté, on affiche également la section déconenxion et la possibilité d'ajouter un nouveau message
        if($loginStatus) { ?>
                <div class = "center">
                    <form method = "post" action = "">
                        <input type ="submit" name = "new-message" class = "tweet-button" style ="border: none;" value = "Message">
                    </form>
                </div>

            <form action = "" method = "post" class = "center">
                <button type = "submit" name ="destroySession" class = "deconnexion">Déconnexion</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>

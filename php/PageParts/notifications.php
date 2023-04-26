<!DOCTYPE html>

<?php

// Si aucune session n'existe,
if (session_status() == PHP_SESSION_NONE) {
    // On en crée une nouvelle
    session_start();
}

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Notification.php");

// On récupère les instances d'utilisateur et de base de données
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = User::getInstance($conn, $globalDb);

// Si le formulaire d'adoption est envoyé :
if(isset($_POST['adoption-status'])) {
    $notification = new Notification($conn, $globalDb);
    // On met cette notification en lue par rapport à l'id de l'adoption
    $notification::setRead($conn, $_POST['notification-id']);
    require_once ("functions.php");
    // Et donc on étudie la valeur du status de l'adoption
    // Si l'adoption est acceptée,
    if($_POST['adoption-status'] == 'acceptee') {
        // Alors on accepte l'adoption grâce à l'id de la notification
        $notification->acceptAdoption($_POST['notification-id']);
        // Et donc on affiche que l'adoption a été acceptée
        displayPopUp("Adoption","Vous avez accepté l'adoption !");
        ?>
        <script>
            window.onload = function() {
                openWindow('pop-up');
            }
        </script>
        <?php
    }
    // Autrement, si l'adoption est refusée
    else {
        // On refuse l'adoption
        $notification->denyAdoption($_POST['notification-id']);
        // On affiche que l'adoption a été bien refusée
        displayPopUp("Adoption","Vous avez refusé l'adoption.");
        ?>
        <script>
            window.onload = function() {
                openWindow('pop-up');
            }
        </script>
        <?php
    }
}
?>

<html lang = "fr">
<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <link rel = "stylesheet" href = "../css/newMessage.css">
    <link rel = "stylesheet" href = "../css/message.css">
    <link rel = "stylesheet" href = "../css/notification.css">
    <link rel="shortcut icon" href="../favicon.ico">
</head>
<body>
<div class = "Container">
    <?php
    global $loginStatus;
    // On inclus la barre de navigation
    include("./navigation.php");
    include("./hubMessages.php");

    ?>

    <div class = "MainContainer">
        <div class = "h1-container">
            <h1>Notifications</h1>
        </div>
        <div class = "spacing"></div>
            <?php

            popUpNewMessage();

            // Si l'utilisateur est connecté
            if ($loginStatus) {
                // On crée une nouvelle instance de notification
                $notification = new Notification($conn, $globalDb);
                // On récupère toutes les notifications d'un utilisateur
                $notificationList = $notification->getNotifications($globalUser->getUsername());


                if($notificationList) {
                    // Pour chaqsue notification :
                    foreach($notificationList as $notifEntry) {
                        // On récupère les données de la notification
                        $row = $notifEntry[0]; // Les données de la notification
                        $read = $notifEntry[1]; // L'état de lecture (0 non lu, 1 lu)
                        ?>
                        <div <?php if(!$read) { ?> style = "background-color: #d3eae0" <?php } ?>>
                            <?php
                            // Pour chaque notification, on affiche donc la notification
                            echo $notification->displayNotification($row);
                            ?>
                        </div>
                        <?php
                    }
                }
                else {
                    echo '<h4>Vous n\'avez pas de notifications</h4>';
                }

            }
            else {
                echo '<h4>Connectez-vous pour accéder aux notifications</h4>';
            }
        ?>
    </div>

    <?php
    include("./trends.php");
    ?>
</div>

</body>

</html>
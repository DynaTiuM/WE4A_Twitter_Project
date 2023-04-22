<!DOCTYPE html>

<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Notification.php");

$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = User::getInstance($conn, $globalDb);

if(isset($_POST['adoption-status'])) {
    $notification = new Notification($conn, $globalDb);
    $notification::setRead($conn, $_POST['notification-id']);
    require_once ("functions.php");
    if($_POST['adoption-status'] == 'acceptee') {
        $notification->acceptAdoption($_POST['notification-id']);
        displayPopUp("Adoption","Vous avez accepté l'adoption !");
        ?>
        <script>
            window.onload = function() {
                openWindow('pop-up');
            }
        </script>
        <?php
    }
    else {
        $notification->denyAdoption($_POST['notification-id']);
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
    <link rel = "stylesheet" href = "../css/notification.css">
    <link rel="shortcut icon" href="../favicon.ico">
</head>
<body>
<div class = "Container">
    <?php
    global $loginStatus;
    include("./navigation.php");
    include("./hubMessages.php");

    ?>

    <div class = "MainContainer">
        <div class = "h1-container">
            <h1>Notifications</h1>
        </div>
        <div class = "spacing"></div>
            <?php

            include("./popupNewMessage.php");
            popUpNewMessage();
            if ($loginStatus) {
                $notification = new Notification($conn, $globalDb);
                $notificationList = $notification->getNotifications($globalUser->getUsername());

                if($notificationList) {

                    foreach($notificationList as $notifEntry) {
                        $row = $notifEntry[0]; // Les données de la notification
                        $read = $notifEntry[1]; // L'état de lecture (0 non lu, 1 lu)
                        ?>
                        <div <?php if(!$read) { ?> style = "background-color: #d3eae0" <?php } ?>>
                            <?php
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
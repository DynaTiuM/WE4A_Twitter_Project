<!DOCTYPE html>

<?php
session_start();
ob_start();

require_once("./functions.php");

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");
require_once("../Classes/UserProfile.php");
require_once("../Classes/AnimalProfile.php");
require_once("windowsProfile.php");

global $globalDb;
global $globalMessage;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalMessage = new Message($conn, $globalDb);
$userId = $_SESSION['username'] ?? null;
$globalUser = User::getInstanceById($conn, $globalDb, $userId);


$username = $_GET['username'];
require_once ("../Classes/Profile.php");
$type = Profile::determineProfileType($conn, $username);

global $profile;
if ($type == 'utilisateur') {
    $profile = new UserProfile($conn, $username, $globalDb);
}
elseif($type == 'animal') {
    $profile = new AnimalProfile($conn, $username, $globalDb);
}
else {
    echo 'Utilisateur non trouvé';
}

if(isset($_POST['follow'])) {
    $followId = $globalUser->follow_unfollow($username, $type);
    if ($followId) {
        require_once ("../Classes/Notification.php");
        $notification = new Notification($conn, $globalDb);
        $notification->createNotificationForFollow($userId, $username, $followId);
    }
}

if (isset($_POST['notification-id'])) {
    $notificationId = $_POST['notification-id'];

    require_once ("../Classes/Notification.php");
    Notification::setRead($conn, $notificationId);
}

if (isset($_SESSION['username'])) {
    $userId = $_SESSION['username'];
    $globalUser = User::getInstanceById($conn, $globalDb, $userId);
}

if(isset($_POST['reply_to'])) {
    popUpNewMessage();
    displayNewMessageForm($conn, $globalDb, $_POST['reply_to']);
}
if (isset($_POST['modification-profile'])) {
    $result = $profile->getUser()->updateProfile($_FILES['avatar'], $_POST['prenom'], $_POST['nom'], $_POST['date'],  $_POST['bio'],  $_POST['password'], $_POST['confirm']);
    displayConfirmationModificationProfile($result);
}

function displayConfirmationModificationProfile($result) {
    ?>
    <div id="display-confirmation-profile" class="window-background" style = "display: block; z-index: 300; position:fixed">
        <div class="window-content">
            <span class="close" onclick="closeWindow('display-confirmation-profile')">&times;</span>
            <?php echo '<h1>'. $result . '</h1>'?>
        </div>
    </div>
<?php
}
?>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <link rel = "stylesheet" href = "../css/profile.css">
    <link rel = "stylesheet" href = "../css/newMessage.css">
    <title>Profil</title>
    <link rel="shortcut icon" href="../favicon.ico">

    <script src="../js/windows.js"></script>
    <script src = "../js/profilBoxesManager.js"></script>

    <?php
    include("./popupNewMessage.php");
    ?>

</head>

<body>

    <div class = "Container">

        <?php include("./navigation.php");

        $profile->setNumberOfMessages(Message::countAllMessages($conn, $username, $type));

        ?>
        <div class = "MainContainer">
            <?php

            global $profile;
            ?>
            <div class = "h1-container">
                <h1 style = "margin-bottom: 0.2vw">Profil</h1>
                <?php
                $profile->displayNumMessages();
            ?>
                </div>
                <div class = "spacing"></div>
                    <div class = "profile">
                        <?php
                        $profile->displayProfile();
                        ?>
                    <div id="message-like-section">
                        <button id="message-button" class="message-section" disabled>Messages</button>
                        <?php if($type == 'utilisateur') {?>
                            <button id="answer-button" class="answer-section">Réponses</button>
                            <button id="like-button" class="like-section" >J'aime</button>
                            <?php
                        }
                        $profile->displayBoxes();
                        ?>
                    </div>
                </div>

            </div>
            <?php
            include("./trends.php");


            if ($type == 'utilisateur') {
                displayPopUpProfile("Modification du profil", "./profileModificationForm.php");
                displayAddPet();
            }
            elseif($type == 'animal') {
                displayPopUpProfile("Modification du profil", "./petProfileModificationForm.php");
            }
            ?>
    </div>


</body>
</html>




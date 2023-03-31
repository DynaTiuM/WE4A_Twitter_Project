<!DOCTYPE html>

<?php
session_start();
ob_start();

require_once("./functions.php");

require_once("./databaseFunctions.php");

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");
require_once("../Classes/UserProfile.php");
require_once("../Classes/AnimalProfile.php");
require_once("windowsProfile.php");

global $globalDb;
global $globalUser;
global $globalMessage;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalMessage = new Message($conn, $globalDb);

if (isset($_SESSION['username'])) {
    $userId = $_SESSION['username'];

    // Ici, vous pouvez utiliser les informations stockées dans la session pour créer l'instance de l'utilisateur.
    // Vous devrez peut-être ajuster la méthode getInstance() ou créer une nouvelle méthode pour créer une instance avec les données de session.
    $globalUser = User::getInstanceById($conn, $globalDb, $userId);
}

if(isset($_POST['reply_to'])) {
    popUpNewMessage();
    displayNewMessageForm($conn, $globalDb, $_POST['reply_to']);
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
    //popUpNewMessage();
    ?>

</head>

<body>

    <div class = "Container">

        <?php include("./navigation.php");
        $username = $_GET['username'];
        require_once ("../Classes/Profile.php");
        $type = Profile::determineProfileType($conn, $username);

        if(isset($_POST['follow'])) {
            $globalUser->follow_unfollow($conn, $username, $type);
        }

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

        $profile->setNumberOfMessages(Message::countAllMessages($conn, $username, $type));

        ?>
        <div class = "MainContainer">
            <?php

            if(isset($_POST['follow'])) {
                $globalUser->follow_unfollow($conn, $username, $type);
            }

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
            if ($type == 'utilisateur') {
                displayModificationProfile();
                displayAddPet();
            }
            elseif($type == 'animal') {
                displayModificationPetProfile();
            }

            include("./trends.php");

            ?>
    </div>


</body>
</html>




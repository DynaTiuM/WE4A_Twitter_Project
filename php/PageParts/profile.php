<!DOCTYPE html>

<?php
ob_start();

session_start();

require_once("./functions.php");
include("windows.php");

require_once("./databaseFunctions.php");

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");
require_once("../Classes/UserProfile.php");
require_once("../Classes/AnimalProfile.php");

global $globalDb;
global $globalUser;
global $globalMessage;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $organisation = $_SESSION['organisation'];

    // Ici, vous pouvez utiliser les informations stockées dans la session pour créer l'instance de l'utilisateur.
    // Vous devrez peut-être ajuster la méthode getInstance() ou créer une nouvelle méthode pour créer une instance avec les données de session.
    $globalUser = User::getInstanceByIdAndOrganisation($conn, $globalDb, $userId, $organisation);
}
else {

    echo' NON';
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Récupération des boutons
            const messageBtn = document.getElementById("message-button");
            const answerBtn = document.getElementById("answer-button");
            const likeBtn = document.getElementById("like-button");

            // Récupération des contenus
            const messageContent = document.getElementById("message-content");
            const answerContent = document.getElementById("answer-content");
            const likeContent = document.getElementById("like-content");

            // Fonction qui désactive les boutons et affiche le contenu correspondant
            function switchContent(btn, content) {
                // On désactive tous les boutons
                messageBtn.disabled = false;
                answerBtn.disabled = false;
                likeBtn.disabled = false;

                // On cache tous les contenus
                messageContent.style.display = "none";
                answerContent.style.display = "none";
                likeContent.style.display = "none";

                // On active le bouton cliqué et affiche le contenu correspondant
                btn.disabled = true;
                content.style.display = "block";
            }

            // Ajout des écouteurs d'événements pour chaque bouton
            messageBtn.addEventListener("click", function() {
                switchContent(this, messageContent);
            });

            answerBtn.addEventListener("click", function() {
                switchContent(this, answerContent);
            });

            likeBtn.addEventListener("click", function() {
                switchContent(this, likeContent);
            });
        });
    </script>

    <?php
    include("./popupNewMessage.php");
    //popUpNewMessage();
    ?>

</head>

<body>
    <div class = "Container">
        <?php include("./navigation.php") ?>
        <div class = "MainContainer">
                    <?php
                    if(isset($_POST['follow'])) {
                        //follow_unfollow($username, $type);
                    }
                    $username = $_GET['username'];

                    $type = determinePetOrUser($globalDb->getConnection(), $username);

                    global $profile;
                    if ($type == 'user') {
                        $profile = new UserProfile($conn, $username, $globalDb);
                        $profile->getUser()->setUserInformation();
                        $profile->setNumberOfMessages($globalMessage->countAllMessages($username, $type));
                        ?><div class = "h1-container">
                            <h1 style = "margin-bottom: 0.2vw">Profil</h1>
                            <?php
                        $profile->displayNumMessages();
                        ?>
                        </div>

                    <?php
                    } else {
                        $profile = new AnimalProfile($conn, $username, $globalDb);
                        //$profile->getUser()->setPetInformation();
                         $profile->setNumberOfMessages($globalMessage->countAllMessages($username, $type));
                        ?><div class = "h1-container">
                            <h1 style = "margin-bottom: 0.2vw">Profil</h1>
                            <?php
                        $profile->displayNumMessages();
                            }
                        ?>


            <div class = "spacing"></div>
            <div class = "profile">
                    <?php
                    $profile->displayProfile();
                    ?>
                </div>

                <div id="message-like-section">
                    <button id="message-button" class="message-section" disabled>Messages</button>
                    <?php if($type == 'user') {?>
                    <button id="answer-button" class="answer-section">Réponses</button>
                    <button id="like-button" class="like-section" >J'aime</button>
                        <?php
                    }?>
                    <div id="message-content">
                        <?php
                        $messageIds = $profile->profilMessagesAndAnswers(true);
                        if($messageIds) Message::displayMessages($conn, $globalDb, $messageIds);
                        ?>
                    </div>
                    <?php if($type == 'user') {?>
                        <div id="answer-content" style="display:none;">
                            <?php
                            $messageIds = $profile->profilMessagesAndAnswers(false);
                            if($messageIds) Message::displayMessages($conn, $globalDb, $messageIds);
                            ?>
                        </div>
                        <div id="like-content" style="display:none;">
                        <?php
                        $messageIds = $profile->likedMessages();
                        if($messageIds) Message::displayMessages($conn, $globalDb, $messageIds);
                        ?>
                        </div>
                        <?php
                        }?>
                </div>
            </div>

            <div id="add-pet" class="window-background">
                <div class="window-content">
                    <span class="close" onclick="closeWindow('add-pet')">&times;</span>
                    <h2 class = "window-title">Ajout d'un animal</h2>
                    <?php include("./addPetForm.php"); ?>
                </div>
            </div>
            <div id="modification-profile" class="window-background">
                <div class="window-content">
                    <span class="close" onclick="closeWindow('modification-profile')">&times;</span>
                    <h2 class = "window-title">Modification du profil</h2>
                    <?php include("./profileModificationForm.php"); ?>
                </div>
            </div>

            <div id="modification-pet-profile" class="window-background">
                <div class="window-content">
                    <span class="close" onclick="closeWindow('modification-pet-profile')">&times;</span>
                    <h2 class = "window-title">Modification du profil de l'animal</h2>
                    <?php include("./petProfileModificationForm.php"); ?>
                </div>
            </div>
        </div>
        <?php
        include("./trends.php") ?>
    </div>
</body>
</html>




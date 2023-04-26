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
// On récupère l'instance à la base de données
$globalDb = Database::getInstance();
// Puis on récupère la connexion à la base de données
$conn = $globalDb->getConnection();

// Puis on récupère l'utilisateur qui se promène sur le site, dans le cas où il est connecté
$userId = $_SESSION['username'] ?? null;
$globalUser = User::getInstanceById($conn, $globalDb, $userId);
$loginStatus = $globalUser->checkLogin();

// On récupère également le username de l'utilisateur du profil
$username = $_GET['username'];
require_once ("../Classes/Profile.php");
// On détermine le type de profil (animal ou utilisateur) car à la méthode static de la classe Profile
$type = Profile::determineProfileType($conn, $username);

global $profile;
// Puis en fonction du type de profil, on crée une instance d'un nouveau profil :
if ($type == 'utilisateur') {
    // Soit un nouveau profil d'utilisateur
    $profile = new UserProfile($conn, $username, $globalDb);
}
elseif($type == 'animal') {
    // Soit un profil d'animal
    $profile = new AnimalProfile($conn, $username, $globalDb);
}
else {
    echo 'Utilisateur non trouvé';
}

verificationPostFollow($loginStatus, $username, $type);
verificationPostSubmit($conn, $globalDb);
verificationPostNotification($conn);

// Si le formualaire de réponse à un message a été cliqué, cela signifie que l'utilisateur qui se promène sur le profil d'un autre utilisateur, a souhaité cliquer sur le bouton de commentaire de l'un des messages du profil
if(isset($_POST['reply_to'])) {
    // On affiche donc une nouvelle pop-up permettant l'affichage d'une pop-up de réponse à un message
    popUpNewMessage();
    displayNewMessageForm($conn, $globalDb, $_POST['reply_to']);
}

// Enfin, si le formulaire de modification de profil a été envoyé, cela signifie qu'il est nécessaire de mettre à jour les informations de l'utilisateur
if (isset($_POST['modification-profile'])) {
    // On met donc à jour son profil
    $result = $profile->getUser()->updateProfile($_FILES['avatar'], $_POST['prenom'], $_POST['nom'], $_POST['date'],  $_POST['bio'],  $_POST['password'], $_POST['confirm']);
    // Et on affiche le résultat :
    displayConfirmationModificationProfile($result);
}

/**
 * Fonction qui permet l'affichage de la pop-up de confirmation de modification du profil
 *
 * @param $result
 * @return void
 */
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
    <link rel = "stylesheet" href = "../css/message.css">
    <title>Profil</title>
    <link rel="shortcut icon" href="../favicon.ico">

    <script src="../js/windows.js"></script>
    <script src = "../js/profilBoxesManager.js"></script>

</head>

<body>
    <div class = "Container">
        <?php
        // On inclus la barre de navigation située sur la gauche
        include("./navigation.php");

        // On commence à calculer le nombre de messages que l'utilisateur du profil a posté
        $profile->setNumberOfMessages($profile->getUser()->countAllMessages());

        ?>
        <div class = "MainContainer">
            <div class = "h1-container">
                <h1 style = "margin-bottom: 0.2vw">Profil</h1>
                <?php
                // Sur le profil, on affiche le nombre de messages que l'utilisateur/animal possède
                $profile->displayNumMessages();
            ?>
                </div>
                <div class = "spacing"></div>
                    <div class = "profile">
                        <?php
                        // Puis on affiche l'intégralité du profil en fonction du type de profil (animal ou utilisateur)
                        $profile->displayProfile();
                        ?>
                    <div id="message-like-section">
                        <button id="message-button" class="message-section" disabled>Messages</button>
                        <?php
                        // S'il s'agit d'un profil utilisateur, il est important d'ajouter les sections de réponses au message et les likes de message
                        // En effet, le profil animal ne possède pas ces colonnes-ci, car un animal ne peut pas liker de message, ni répondre à un message
                        if($type == 'utilisateur') {?>
                            <button id="answer-button" class="answer-section">Réponses</button>
                            <button id="like-button" class="like-section" >J'aime</button>
                            <?php
                        }
                        // On affiche ainsi les sections de message
                        $profile->displayBoxes();
                        ?>
                    </div>
                </div>

            </div>
            <?php

            // On n'oublie pas d'afficher les tendances sur la droite de la page
            include("./trends.php");

            // Dans le cas où le profil est un profil utilisateur, on ajoute les pop-up qui peuvent apparaitre, c'est à dire :
            // Les pop-up de modification de profil d'un utilisateur et de modification d'un animal lorsque l'utilisateur clique sur l'un des boutons
            if ($type == 'utilisateur') {
                displayPopUpProfile("Modification du profil", "./profileModificationForm.php");
                displayAddPet();
            }
            // De meme pour la partie profil animal
            elseif($type == 'animal') {
                displayPopUpProfile("Modification du profil", "./petProfileModificationForm.php");
            }
            ?>
    </div>
</body>
</html>




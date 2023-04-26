<?php
session_start();
require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once ("functions.php");
require_once ("init.php");
global $globalDb;
global $globalUser;
$newLoginStatus = $globalUser->checkLogin();

?>

<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "../css/stylesheet.css">
    <link rel = "stylesheet" href = "../css/connexion.css">
    <title>Connexion</title>
    <link rel="shortcut icon" href="../favicon.ico">

    <script src = "../js/windows.js"></script>

</head>

<body>
<div class = "Container">
	<?php include("./navigation.php");

    // Si l'utilisateur n'est pas connecté :
    if(!$newLoginStatus[0]) {
        try {
            // On regarde le status de création d'un nouveau compte :
            $newAccountStatus = $globalUser->checkNewAccountForm();
            // Dans le cas où la création du compte a été réalisée avec succès :
            if($newAccountStatus[1]) {
                // On affecte le nom d'utilisateur à la session username
                $_SESSION['username'] = $globalUser->getUsername();
            }
        } catch (Exception $e) {
            echo "Erreur";
        }
    }
    // Sinon, si l'utilisateur est actuellement connecté :
    else {
        // On affecte le nom d'utilisateur à la session username
        $_SESSION['username'] = $globalUser->getUsername();

        // Et on renvoie directement l'utilisateur sur la section abonnements
        header("Location: subscriptions.php");
        exit();
    }

    // S'il y a une erreur lors de la connexion, on l'affiche en appelant l'ouverture de la pop-up error-connection
    if ($newLoginStatus[2] != NULL) { ?>
        <script>
            // Ouverture automatique de la fenêtre erreur-connexion
            window.onload = function() {
                openWindow('error-connection');
            }
        </script>
        <?php
    }

    // La fonction displayCode permet d'afficher le formulaire d'ajout du code récupéré par e-mail :
    displayCode();
    // Donc si le formulaire d'envoi de réinitialisation de mot de passe a été cliqué :
    if(isset($_POST['submitEmail'])) {
        // On a d'abord besoin du fichier sendEmail.php pour faire le lien avec ce dernier
        require_once("./sendEmail.php");
        // On crée une session avec le code secret, qui prend une valeur de 100000 à 999999 pour générer un code aléatoire de 6 chiffres
        $_SESSION['secret_code'] = mt_rand(100000, 999999);
        // On crée également une session de temps où le code a été envoyé à partir du moment où l'on a cliqué sur envoyer l'email
        $_SESSION['start_time_code'] = time();
        // Enfin, on crée une session permettant de récupérer le nom de l'utilisateur qui souhaite réinitialiser son mot de passe
        $_SESSION['username_tmp'] = $_POST['username'];

        $secretCode = $_SESSION['secret_code'];
        // Au final, on récupère l'état de l'email envoyé grâce à la fonction sendEmail réalisée dans le fichier sendEmail.php
        $state = sendEmail($_POST['username'], $secretCode);
        // S'il n'y a rien de renvoyé, alors il y a eu une erreur lors de l'envoi de l'email, on informe l'utilisatuer :
        if(!$state) {
            $state = 'Erreur lors de l\'envoi de l\'email';
            ?>
            <script>
                window.onload = function() {
                    openWindow('email-not-sent');
                }
            </script>
        <?php
        }
        // Sinon, on ouvre la deuxième pop-up permettant d'ajouter le code reçu par email :
        else {
        ?>
            <script>
                window.onload = function() {
                    openWindow('code');
                }
            </script>
            <?php
        }

        displayEmailSent($state);
    }
    // De la meme manière, cela raffraichira la page, et si le formulaire d'envoi du code reçu par email est set :
    elseif (isset($_POST['submitCode'])) {
        // Et que la session de démarrage du temps a été set également :
        if(isset($_SESSION['start_time_code'])) {
            // On vérifie le temps qu'il s'est écoulé entre la récupération du code et l'envoi sur le site :
            $elapsed_time = time() - $_SESSION['start_time_code'];
            // Si le temps est supérieur à 300s (5mins)
            if ($elapsed_time >= 300) {
                // On informe que l'utilisateur a pris trop de temps pour renseigner le code
                ?>
                <script>
                    window.onload = function() {
                        openCodeWindow('code-too-late');
                    }
                </script>
                <?php
                // Et on détruit la session !
                session_destroy();
            }
            // Sinon, on vérifie si les codes correspondent entre le code secret de la session et le code renseigné par l'utilisateur
            elseif ($_POST['codeValue'] != $_SESSION['secret_code']) {
                // Si ce n'est pas le cas, on affiche que le code est faux !
                ?>
                <script>
                    window.onload = function() {
                        openCodeWindow('code-wrong');
                    }
                </script>
                <?php
            }
            else {
                // Sinon, on amène directement l'utilisateur vers la page de réinitialisation de mot de passe
                header("Location: ../PageParts/changePassword.php");
            }
        }
    }
    ?>

	<div class = "MainContainer">

        <h3>Connexion à Twitturtle</h3>

        <?php
        // Si l'utilisateur n'est pas connecté, on affiche les boutons de connexion :
        if(!$newLoginStatus[0]) {?>
        <div class = "center">
            <button class = "connexion-button" onclick="openWindow('connection')">Connexion</button>
            <button class = "inscription-button" onclick="openWindow('register')">Inscription</button>
        </div>

        <?php }
        ?>

	</div>

    <?php
    include("./trends.php"); ?>

<?php
    // Si le compte a été créé avec succès, on informe l'utilisateur
    if($newAccountStatus[1]) {
        displayPopUp("Compte","Nouveau compte créé avec succès !");
        ?>
        <script>
            window.onload = function() {
                openWindow('pop-up');
            }
        </script>
    <?php
    }
    // Sinon, si une connexion a été tentée, on informe l'utilisateur les raisons pour laquelle la connexion n'a pas pu aboutir :
    elseif ($newAccountStatus[0]){
        displayPopUp("Compte","$newAccountStatus[2]");
        ?>
        <script>
            window.onload = function() {
                openWindow('pop-up');
            }
        </script>
        <?php
    }

    // On importe les formulaires de connexion/création de compte etc par l'intermédiaire de ces fonctions disponibles dans le fichier functions.php
    displayConnection();
    displayRegister();
    displayErrorConnection();
    displayLostPassword();
    ?>
</div>
</body>


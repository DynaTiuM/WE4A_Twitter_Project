<?php

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../PageParts/functions.php");

// Il s'agit de la page où l'utilisateur réinitialise son mot de passe

session_start();

// Tout d'abord, si le temps a bien été set :
if(isset($_SESSION['start_time_code'])) {
    // On vérifie à nouveau si le temps n'est pas écoulé (5minutes)
    $elapsed_time = time() - $_SESSION['start_time_code'];
    if ($elapsed_time >= 300) {
        // S'il est écoulé, on affiche que c'est trop tard pour changer son code à l'utilisateur, il n'a pas d'autre choix que de sortir de cette page
        ?>
        <script>
            window.onload = function() {
                openCodeWindow('code-too-late2');
            }
        </script>
        <?php
    }
    // Sinon, si le formulaire de changement de mot de passe a été envoyé,
    if(isset($_POST['submitChangePassword'])) {
        // On vérifie les deux mots de passe renseignés
        if($_POST['new_password'] == $_POST['confirm_password']) {
            $globalDb = Database::getInstance();
            $conn = $globalDb->getConnection();
            $user = User::getInstanceById($conn, $globalDb, $_SESSION['username_tmp']);
            // On récupère l'instance de l'utilisateur grâce à son nom d'utilisateur, et on change son mot de passe
            $user->changePassword($conn, $_POST['new_password']);
            // On détruit la session
            session_destroy();
            // Et on affiche que le mot de passe a bien été modifié
            passwordModified();
            ?>
            <script>
                window.onload = function() {
                    openCodeWindow('password-modified');
                }
            </script>
            <?php
            }
    }
    ?>


    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Réinitialisation du mot de passe | Twitturtle</title>
        <link rel="stylesheet" href="../css/stylesheet.css">
        <link rel="stylesheet" href="../css/home.css">
        <link rel="stylesheet" href="../css/changePassword.css">
        <script src = "../js/windows.js"></script>
    </head>
    <body>
    <div class="container">
        <header>
            <img src="../images/logo.png" alt="Logo Twitturtle" class="logo">
        </header>

        <?php
        // Dans la partie affichage, tout est censé s'afficher seulement si le temps n'est pas encore écoulé :
        if ($elapsed_time < 300) { ?>
        <section class="reset-password">
            <?php
            // Si les deux mots de passe ne correspondent pas, ou que l'utilisateur apparait pour la première fois sur cette page :
            if((isset($_POST['submitChangePassword']) && $_POST['new_password'] != $_POST['confirm_password'] ) || (!isset($_POST['submitChangePassword']))) {

                // On affiche la zone de réinitialisation de mot de passe :
                ?>
            <div class="title-container">
                <h1>Réinitialisation du mot de passe</h1>
            </div>
            <div class="text-container">
                <p class = "under_title">Nom d'utilisateur : <?php echo $_SESSION['username_tmp']; ?></p>
            </div>
            <?php
            }
            if(isset($_POST['submitChangePassword'])) {
                // Et donc si les deux mots de passe ne correspondent pas, on informe l'utilisateur par l'intermédiaire de l'affichage d'une nouvelle ligne :
                    if($_POST['new_password'] != $_POST['confirm_password']) {
                        ?>
                        <h2 class="error-password">Les deux mots de passe ne sont pas identiques !</h2>
                        <?php
                    }
                }
                // Et de même, Si les deux mots de passe ne correspondent pas, ou que l'utilisateur apparait pour la première fois sur cette page :
                if((isset($_POST['submitChangePassword']) && $_POST['new_password'] != $_POST['confirm_password'] ) || (!isset($_POST['submitChangePassword']))) {
                    ?>
                    <div class="reset-password-form flashcard-style">
                        <form action="" method="post">
                            <label for="new_password">Nouveau mot de passe :</label>
                            <input class ="new-password-form" type="password" id="new_password" name="new_password" required>
                            <label for="confirm_password">Confirmer le mot de passe :</label>
                            <input class ="new-password-form" type="password" id="confirm_password" name="confirm_password" required>
                            <button class="button" name="submitChangePassword" type="submit">Réinitialiser le mot de passe</button>
                        </form>
                    </div>
                </section>
                    <?php
                }
            }
        else {
            // Sinon, si le temps est écoulé, on affiche tout simplement la pop-up informant que le temps est écoulé
            ?>
            <div id="code-too-late2" class="window-background">
                <div class="window-content">
                    <h2 style = "color: black" class = "window-title">Erreur</h2>
                    <p style = "color: black">La période de changement de mot de passe a expirée.</p>
                    <br>
                    <button class = "form-button" onclick="window.location = '../PageParts/connect.php'">D'accord</button>
                </div>
            </div>
            <?php
        }?>
    </div>
    </body>

</html>
<?php
}
// Si la session de temps n'est pas lancée, cela signifie que l'utilisateur n'est pas censé se trouver sur cette page, on l'interdit d'accéder au contenu :
else {
    echo '<div>
    <h2 style = "display: flex; align-items: center; justify-content: center; margin-top: 4vw">Vous n\'êtes pas autorisé à accéder à cette page.</h2>
</div>';
}


?>
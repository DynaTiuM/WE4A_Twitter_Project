<?php

require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../PageParts/functions.php");



session_start();
if(isset($_SESSION['start_time_code'])) {
    $elapsed_time = time() - $_SESSION['start_time_code'];
    if ($elapsed_time >= 300) {
        ?>
        <script>
            window.onload = function() {
                openCodeWindow('code-too-late2');
            }
        </script>
        <?php
    }
    if(isset($_POST['submitChangePassword'])) {
        if($_POST['new_password'] == $_POST['confirm_password']) {
            $globalDb = Database::getInstance();
            $conn = $globalDb->getConnection();
            $user = User::getInstanceById($conn, $globalDb, $_SESSION['username_tmp']);
            $user->changePassword($conn, $_POST['new_password']);
            session_destroy();
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
        <link rel="stylesheet" href="../css/home.css">
        <link rel="stylesheet" href="../css/stylesheet.css">
        <script src = "../js/windows.js"></script>
    </head>
    <body>
    <div class="container">
        <header>
            <img src="../images/logo_white.png" alt="Logo Twitturtle" class="logo">
        </header>

        <?php
        if ($elapsed_time < 300) { ?>
        <section class="reset-password">
            <?php
            if((isset($_POST['submitChangePassword']) && $_POST['new_password'] != $_POST['confirm_password'] ) || (!isset($_POST['submitChangePassword']))) {
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
                    if($_POST['new_password'] != $_POST['confirm_password']) {
                        ?>
                        <h2 class="error-password">Les deux mots de passe ne sont pas identiques !</h2>
                        <?php
                    }
                }
                if((isset($_POST['submitChangePassword']) && $_POST['new_password'] != $_POST['confirm_password'] ) || (!isset($_POST['submitChangePassword']))) {
                ?>
                <div class="reset-password-form flashcard-style">
                    <form action="" method="post">
                        <label for="new_password">Nouveau mot de passe :</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <label for="confirm_password">Confirmer le mot de passe :</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button class="button" name="submitChangePassword" type="submit">Réinitialiser le mot de passe</button>
                    </form>
                </div>

        </section>   <?php
                }
            }
        else {
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
else {
    echo '<div>
    <h2 style = "display: flex; align-items: center; justify-content: center; margin-top: 4vw">Vous n\'êtes pas autorisé à accéder à cette page.</h2>
</div>';
}


?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #1F7C62;
        color: #fff;
    }

    form {
        background-color: white;
    }

    header {
        width: 100%;
        padding: 20px;
        text-align: center;
    }

    .error-password {
        margin-bottom: -1.5vw;
        margin-top: 1vw;
        color: #b95757;
        background-color: white;
        border-radius: 2vw;
        opacity: 0;
        animation-name: logoAnimation;
        animation-delay: 1.3s;
        animation-duration: 1s;
        animation-fill-mode: forwards;
    }

    .reset-password {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 1vw;
        width: 100%;
    }

    h1 {
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 4.15vw;
        animation-name: titleAnimation;
        animation-duration: 0.7s;
        animation-fill-mode: both;
    }

    .under_title {
        animation-delay: 0.5s;
    }

    .reset-password-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        padding: 2rem;
    }

    .reset-password-form label,
    .reset-password-form input {
        width: 30%;
        margin-bottom: 1rem;
        font-size: 1.5vw;
    }
    .reset-password-form.flashcard-style {
        background-color: #fff;
        margin-top: 3vw;
        color: #1F7C62;
        border-radius: 1vw;
        padding: 20px;
        width: 80%;
        max-width: 500px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0;
        animation-name: flashcardAnimation;
        animation-duration: 1s;
        animation-delay: 1s;
        animation-fill-mode: forwards;
    }

    .reset-password-form input {
        width: 100%;
        margin-top: 1vw;
        font-size: 1.5vw;
        margin-bottom: 2vw;
    }

    .button {
        margin-top: 0;
        opacity: 1;
        animation: none;
        font-size: 1.7vw;
        color: white;
        background-color: #1F7C62;
    }

</style>
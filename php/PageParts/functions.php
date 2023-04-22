<?php

function popUpNewMessage($forced = false) {
    if (isset($_POST['reply_to']) && !empty($_POST['reply_to']) || $forced) {
        // Afficher ici la section des messages avec la réponse au message sélectionné
        ?>
        <script>
            // Ouverture automatique de la fenêtre erreur-connexion
            window.onload = function() {
                openWindow('new-message');
            }
        </script>
        <?php
    }
}


function displayNewMessageForm($conn, $db, $messageId = null) {
    echo '<div id="new-message" class="window-background">
        <div class="window-content">';

    if (isset($messageId)) {
        echo '<span class="close" onclick="closeWindowToNewDestination(\'new-message\', location.href)">&times;</span>';
        echo '<h2 class="window-title">Nouveau commentaire</h2>';
        $message = new Message($conn, $db);
        $message->setId($messageId);
        $message->displayContentById($messageId);
    } else {
        echo '<span class="close" onclick="closeWindowToNewDestination(\'new-message\', location.href)">&times;</span>';
        echo '<h2 class="window-title">Nouveau message</h2>';
    }

    require_once("./newMessageForm.php");
    echo '</div></div>';
}

function displayErrorConnection() { ?>
    <div id="erreur-connexion" class="window-background">
        <div class="window-content">
            <div><h2 class = "window-title">Erreur de connexion</h2></div>
            <div>
                <label>Nom d'utilisateur ou mot de passe incorrect</label>
            </div>
            <br>
            <div>
                <label>Veuillez réessayer</label>
            </div>
            <br>
            <br>
            <button class = "form-button" onclick="closeWindow('error-connection')">D'accord</button>
        </div>
    </div>
    <?php
}

function displayConnection() { ?>
    <div id="connection" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('connection')">&times;</span>
            <h2 class = "window-title">Connexion</h2>
            <?php include("./loginForm.php"); ?>
        </div>
    </div>
    <?php
}

function displayRegister() { ?>
    <div id="register" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('register')">&times;</span>
            <h2 class = "window-title">Inscription</h2>
            <?php include("./newLoginForm.php"); ?>
        </div>
    </div>
    <?php
}

function displayEmailSent($state) { ?>
    <div id="email-not-sent" class="window-background">
        <div class="window-content">
            <div><h2 class = "window-title">Erreur d'envoi de l'E-mail</h2></div>
            <p class = "window-text"><?php echo $state. "<br>Il se peut que l'adresse e-mail ne soit pas enregistrée sur notre site."?></p>
            <br>
            <button class = "form-button" onclick="closeWindow('email-not-sent')">D'accord</button>
        </div>
    </div>
<?php
}

function displayLostPassword() {?>
    <div id="lost-password" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('lost-password')">&times;</span>
            <h2 class = "window-title">Mot de passe oublié</h2>
            <?php include("./lostPasswordForm.php"); ?>
        </div>
    </div>
<?php
}

function displayCode() {?>
    <div id="code" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('code')">&times;</span>
            <h2 class = "window-title">Code de réinitialisation</h2>
            <?php include("./codeForm.php"); ?>
        </div>
    </div>

    <div id="code-too-late" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('code-too-late')">&times;</span>
            <h2 class = "window-title">Erreur</h2>
            <p  class = "window-text">Le code que vous avez saisi n'est plus valide.</p>
        </div>
    </div>

    <div id="code-wrong" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('code-wrong')">&times;</span>
            <h2 class = "window-title">Erreur</h2>
            <p  class = "window-text">Le code que vous avez saisi n'est pas correct.</p>
        </div>
    </div>
    <?php
}

function passwordModified() {?>
    <div id="password-modified" class="window-background">
        <div class="window-content">
            <h2 class = "window-title" style = "color:black;">Succès</h2>
            <p style = "color: black" class = "window-text">Mot de passe modifié avec succès</p>
            <br>
            <button class = "form-button" onclick="window.location = '../PageParts/connect.php'">D'accord</button>
        </div>
    </div>
<?php
}

function displayPopUp($title, $message) { ?>
    <div id="pop-up" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('pop-up')">&times;</span>
            <h2 class = "window-title"><?php echo $title?></h2>
            <p class = "window-text"><?php echo $message?></p>
        </div>
    </div>
    <?php
}

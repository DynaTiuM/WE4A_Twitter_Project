<?php

/**
 * Fonction qui permet d'afficher une nouvelle pop-up de message
 *
 * @param $forced
 * @return void
 */
function popUpNewMessage($forced = false) {
    // Si le formulaire de réponse est cliqué, et que la réponse n'est pas vide (c'est à dire que l'on répond à un réel message existant)
    // OU que la pop-up est forcée, c'est à dire qu'elle est forcée à être ouverte, dans le cas du lancement de la pop-up depuis la navigation bar
    if (isset($_POST['reply_to']) && !empty($_POST['reply_to']) || $forced) {
        // Alors on affiche la section du message avec la réponse au message sélectionné
        ?>
        <script>
            // Ouverture automatique de la fenetre new-message
            window.onload = function() {
                openWindow('new-message');
            }
        </script>
        <?php
    }
}


/**
 * Fonction qui permet d'afficher un nouveau formulaire de commentaire ou message
 *
 * @param $conn
 * @param $db
 * @param $messageId
 * @return void
 */
function displayNewMessageForm($conn, $db, $messageId = null) {
    echo '<div id="new-message" class="window-background">
        <div class="window-content">';

    // On ajoute un span avec une fonction javascript qui permet de rediriger l'utilisateur vers le meme url lorsqu'il souhaite sortir de la pop-up de message
    echo '<span class="close" onclick="closeWindowToNewDestination(\'new-message\', location.href)">&times;</span>';
    // Dans le cas où un id de message est informé dans la fonction, c'est qu'il s'agit d'un réponse à un message
    if (isset($messageId)) {
        // On ajoute donc un titre spécifique nouveau commentaire
        echo '<h2 class="window-title">Nouveau commentaire</h2>';
        $message = new Message($conn, $db);
        $message->setId($messageId);
        // Et on affiche le message auquel on souhaite répondre :
        $message->displayContentById($messageId);
    } else {
        echo '<h2 class="window-title">Nouveau message</h2>';
    }

    // On n'oublie pas d'ajouter le formulaire de message, permettant de renseigner l'intégralité des éléments souhaités dans notre message (contenu, image, animaxu etc)
    require_once("./newMessageForm.php");
    echo '</div></div>';
}

/**
 * Fonctino permettant d'afficher une erreur de connexion
 *
 * @return void
 */
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

/**
 * Fonction permettant d'afficher le formulaire de connexion sur une pop-up
 *
 * @return void
 */
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

/**
 * Fonction permettant d'afficher le formulaire d'inscription sur une pop-up
 *
 * @return void
 */
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

/**
 * Fonction permettant d'afficher une erreur lors de l'envoi d'un e-mail
 *
 * @param $state
 * @return void
 */
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

/**
 * Fonction permettant d'afficher le formulaire de mot de passe oublié via une pop-up
 *
 * @return void
 */
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

/**
 * Fonction permettant d'afficher le formulaire d'ajout du code de réinitialisation via une pop-up
 *
 * @return void
 */
function displayCode() {?>
        <!-- Première pop-up permettant d'ajouter le code de réinitialisation -->
    <div id="code" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('code')">&times;</span>
            <h2 class = "window-title">Code de réinitialisation</h2>
            <?php include("./codeForm.php"); ?>
        </div>
    </div>

    <!-- Deuxième pop-up permettant d'informer à l'utilisateur qu'il a pris trop de temps pour renseigner le code  -->
    <div id="code-too-late" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('code-too-late')">&times;</span>
            <h2 class = "window-title">Erreur</h2>
            <p  class = "window-text">Le code que vous avez saisi n'est plus valide.</p>
        </div>
    </div>

    <!-- Troisièeme pop-up permettant d'informer l'utilisateur que le code renseigné est faux -->
    <div id="code-wrong" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('code-wrong')">&times;</span>
            <h2 class = "window-title">Erreur</h2>
            <p  class = "window-text">Le code que vous avez saisi n'est pas correct.</p>
        </div>
    </div>
    <?php
}

/**
 * Fonction permettant d'indiquer à l'utilisateur que le mot de passe a été modifié avec succès via une pop-up
 *
 * @return void
 */
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

/**
 * Fonction permettant d'afficher une pop-up
 *
 * @param $title
 * @param $message
 * @return void
 */
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

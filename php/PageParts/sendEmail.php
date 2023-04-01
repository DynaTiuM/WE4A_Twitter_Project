<?php

function sendEmail($username, $secretCode) {
    global $globalDb;

    $globalDb = Database::getInstance();
    $conn = $globalDb->getConnection();
    $globalUser = User::getInstanceById($conn, $globalDb, $username);
    if(!$globalUser) return false;
    $newLoginStatus = $globalUser->checkLogin();

    $find_user = $globalUser->getUserInformation();
    if($find_user == null) return null;
    $email_dest = $find_user['email'];
    require_once '../phpmailer/PHPMailer.php';
    require_once '../phpmailer/SMTP.php';
    require_once '../phpmailer/Exception.php';

// Création d'une instance de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();

// Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp-relay.sendinblue.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'raphael.perrin854@gmail.com';
    $mail->Password = '';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

// Configuration de l'expéditeur et du destinataire
    $mail->setFrom('raphael.perrin854@gmail.com', 'Twitturtle');
    $mail->addAddress($find_user['email'], $find_user['username']);

// Configuration du message
    $mail->isHTML(true);
    $mail->Subject = 'Réinitialisation mot de passe Twitturtle';
    $mail->Body =
        '<html lang = "fr">
        <head>
            <meta charset="UTF-8">
        </head>
        <body>
            <h1>Réinitialisation de votre mot de passe</h1>
            <p>Bonjour '.$find_user['prenom'].' '.$find_user['nom'].' ! <br>
            <br>
            Nous avons bien reçu votre demande de réinitialisation de mot de passe. Pour procéder à cette réinitialisation, voici le code à entrer sur notre site :
            <br>
            <p><b>'.$secretCode.'</p>
            <br>      
            Si vous n\'avez pas demandé de réinitialisation de mot de passe, veuillez nous contacter immédiatement.
            <br><br>
            Cordialement,
            <br>
            L\'équipe de support technique de Twitturtle. 
        </body>  
        </html>';

// Envoi de l'email
    if(!$mail->send()) {
        return false;
    } else {
        return 'Veuillez consulter votre messagerie électronique, y compris le dossier des courriers indésirables, afin de vérifier si vous avez bien reçu notre message.';
    }
}
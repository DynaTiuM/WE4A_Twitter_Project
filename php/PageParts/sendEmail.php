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

    // Utilisation de CSS inline, car certaines messageries ne prennent pas en charge les balises <style>, conversion réalisée à l'aide Juice CSS Inliner
    $mail->Body =
        '<div style="font-family: Arial, sans-serif; color: #333; background-color: #f5f5f5; padding: 1rem; max-width: 600px; margin: 0 auto;">
        <div style="background-color: #ffffff; border-radius: 5px; padding: 2rem;">
            <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">Réinitialisation de votre mot de passe</h1>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Bonjour aa a,</p>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Nous avons reçu une demande de réinitialisation de votre mot de passe sur Twitturtle. Pour procéder à cette réinitialisation, veuillez entrer le code suivant sur notre site :</p>
            <div style="font-size: 1.2rem; font-weight: bold; text-align: center; padding: 1rem; border: 1px solid #333; border-radius: 5px; margin-bottom: 1rem;">' . $secretCode . '</div>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Si vous n\'êtes pas à l\'origine de cette demande, nous vous prions de bien vouloir contacter notre équipe d\'assistance dès que possible.</p>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Cordialement,</p>
            <p style="font-weight: bold;">L\'équipe de support technique de Twitturtle</p>
        </div>
    </div>';

// Envoi de l'email
    if(!$mail->send()) {
        return false;
    } else {
        return 'Veuillez consulter votre messagerie électronique, y compris le dossier des courriers indésirables, afin de vérifier si vous avez bien reçu notre message.';
    }
}
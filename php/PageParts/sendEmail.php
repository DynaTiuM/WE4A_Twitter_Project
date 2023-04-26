<?php

// On importe les constantes de configuration qui permettent de se connecter à l'hébergeur d'emails
require_once("config.php");

/**
 * Fonction permettant d'envoyer un email à l'utilisateur
 *
 * @param $username
 * @param $secretCode
 * @return false|string|null
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendEmail($username, $secretCode) {
    global $globalDb;

    $globalDb = Database::getInstance();
    $conn = $globalDb->getConnection();
    $globalUser = User::getInstanceById($conn, $globalDb, $username);
    if(!$globalUser) return false;
    $newLoginStatus = $globalUser->checkLogin();

    // On récupère lesi informations de l'utilisateur, pour pouvoir récupérer son email lors du remplissage de son username dans le formulaire
    $find_user = $globalUser->getUserInformation();
    // Si aucun utilisateur n'est trouvé, on retourne null
    if($find_user == null) return null;

    // Sinon, on récupère l'email
    $email_dest = $find_user['email'];

    // On importe les fichiers nécessaires à la mise en place de PHPMailer
    require_once '../phpmailer/PHPMailer.php';
    require_once '../phpmailer/SMTP.php';
    require_once '../phpmailer/Exception.php';

    // Puis on crée une instance de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    // On configure le serveur SMTP :
    $mail->isSMTP();
    // On informe l'HOST grâce au fichier config.php
    $mail->Host = HOST;
    $mail->SMTPAuth = true;
    // Il en va de même pour le username et le password
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    // On informe le type de protocole de sécurité que l'on souhaite utiliser pour l'envoi de nos emails, ici on utilise le protocole TLS
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    // On choisis le type d'encodage en base 64 le charset en UTF-8 pour pouvoir afficher des caractères utiles lors de l'envoi de notre email
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // Au final, on configure l'email de l'expéditeur et du destinataire
    // Ici, l'expéditeur est l'email renseigné dans le fichier config.php, avec un nom d'envoi signé Twitturtle
    $mail->setFrom(EMAIL_USERNAME, 'Twitturtle');
    // Puis, on ajoute l'email du destinataire grâce à la colonne email de la table renvoyée
    $mail->addAddress($find_user['email'], $find_user['username']);

    // On fini de configurer le message en informant qu'il s'agit d'un message HTML, et on ajoute le titre du mail
    $mail->isHTML(true);
    $mail->Subject = 'Réinitialisation mot de passe Twitturtle';

    // ICI : utilisation de CSS inline, car certaines messageries ne prennent pas en charge les balises <style>, conversion réalisée à l'aide Juice CSS Inliner
    // Donc on écrit le corps du mail :
    $mail->Body =
        '<div style="font-family: Arial, sans-serif; color: #333; background-color: rgba(49,124,103,0.22); padding: 1rem; max-width: 600px; margin: 0 auto;">
        <div style="background-color: #ffffff; border-radius: 5px; padding: 2rem;">
            <h1 style="font-size: 1.5rem; margin-bottom: 1rem; color: #165e4a">Réinitialisation de votre mot de passe</h1>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Bonjour '. $find_user['prenom'].' '. $find_user['nom'].',</p>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Nous avons reçu une demande de réinitialisation de votre mot de passe sur Twitturtle. Pour procéder à cette réinitialisation, veuillez entrer le code suivant sur notre site :</p>
            <div style="font-size: 1.2rem; font-weight: bold; text-align: center; padding: 1rem; border: 1px solid #333; border-radius: 5px; margin-bottom: 1rem;">' . $secretCode . '</div>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Si vous n\'êtes pas à l\'origine de cette demande, nous vous prions de bien vouloir contacter notre équipe d\'assistance dès que possible.</p>
            <p style="font-size: 1rem; line-height: 1.5; margin-bottom: 1rem;">Cordialement,</p>
            <p style="font-weight: bold; color: #165e4a">L\'équipe de support technique de Twitturtle</p>
        </div>
    </div>';

    // Finalement, on envoie le mail, et s'il n'a pas été envoyé on return false
    if(!$mail->send()) {
        return false;
    }
    // Sinon, on informe l'utilisateur qu'un mail a été envoyé dans sa messagerie
    else {
        return 'Veuillez consulter votre messagerie électronique, y compris le dossier des courriers indésirables, afin de vérifier si vous avez bien reçu notre message.';
    }
}
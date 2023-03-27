<?php

function sendEmail($username) {

    $find_user = getUserInformation($username);
    if($find_user != null) {
        $email_dest = $find_user['email'];
        require_once './phpmailer/PHPMailer.php';
        require_once './phpmailer/SMTP.php';
        require_once './phpmailer/Exception.php';

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

// Configuration de l'expéditeur et du destinataire
        $mail->setFrom('raphael.perrin854@gmail.com', 'Twitturtle');
        $mail->addAddress('raphael.perrin854@gmail.com', $find_user['username']);

// Configuration du message
        $mail->isHTML(true);
        $mail->Subject = 'Sujet de l\'email';
        $mail->Body =
            '

<html lang = "fr">
<h1>Réinitialisation de votre mot de passe</h1>
<p>Bonjour '.$find_user['prenom'].' '.$find_user['nom'].' ! <br>
Pour réinitialiser votre mot de passe, cliquez sur le lien suivant :
            
<a>http://localhost/Project/WE4A_Twitter_Project/php/new-password?username='.$find_user['username'].'</a></p>
</html>';

        $mail->AltBody = 'Contenu de l\'email en texte brut';

// Envoi de l'email
        if(!$mail->send()) {
            echo 'Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo;
        } else {
            echo 'Email envoyé avec succès.';
        }

    }
}
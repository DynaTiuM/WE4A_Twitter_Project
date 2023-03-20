<?php
function sendMessage($type) {
    if(isset($_POST['reply_to'])) {
        $reply_id = $_POST['reply_to'];
    }
    else {
        $reply_id = $_GET['answer'];
    }

    if(isset($_POST["content"])) {
        $content = SecurizeString_ForSQL($_POST["content"]);
        $username = $_COOKIE["username"];

        global $conn;

        $stmt = $conn->prepare("INSERT INTO message (auteur_username, parent_message_id, date, contenu, localisation, image) VALUES (?, ?, ?, ?, ?, ?)");

        $image = createImage($_FILES["image"]);

// Redimensionner l'image si elle a été chargée avec succès
        if ($image !== null) {
            $image = formatImage($image);
        }

        $localisation = null;
        if(isset($_POST['localisation'])) {
            $localisation = $_POST['localisation'];
        }
        $parent_message_id = null;
        if($type == 'answer') {
            $parent_message_id = $reply_id;
        }

        $date = date('Y-m-d H:i:s');
        $stmt->bind_param("ssssss", $username, $parent_message_id, $date, $content, $localisation, $image);
        $stmt->execute();
        // On récupère l'id du message inséré
        $message_id = $stmt->insert_id;

        // We prevent the user to use the ' symbol to make a bugged hashtag
        $content = str_replace("\&#039", " ", $content);
        preg_match_all('/#([\p{L}0-9_]+)/u', $content, $matches);
        $hashtags = $matches[1];

        foreach ($hashtags as $hashtag) {
            $stmt = $conn->prepare("INSERT INTO hashtag (tag, message_id) VALUES (?, ?)");
            $stmt->bind_param("si", $hashtag, $message_id);
            $stmt->execute();
        }

        header("Location: explorer.php?answer=$reply_id");

        exit();
    }


}
<?php
function sendMessage() {
    $reply_id = null;
    if(isset($_POST['reply_to'])) {
        $reply_id = $_POST['reply_to'];
    }
    elseif(isset($_GET['answer'])){
        if($_GET['answer'] != "") {
            $reply_id = $_GET['answer'];
        }
    }

    if(isset($_POST["content"])) {
        $content = SecurizeString_ForSQL($_POST["content"]);
        $username = $_COOKIE["username"];

        global $conn;

        $stmt = $conn->prepare("INSERT INTO message (auteur_username, parent_message_id, date, contenu, localisation, image, categorie) VALUES (?, ?, ?, ?, ?, ?, ?)");

        $image = createImage($_FILES["image"]);

// Redimensionner l'image si elle a été chargée avec succès
        if ($image !== null) {
            $image = formatImage($image);
        }

        $localisation = null;
        $category = null;
        if(isset($_POST['localisation'])) $localisation = $_POST['localisation'];
        if(isset($_POST['category'])) if($_POST['category'] != 'classique') $category = $_POST['category'];

        $parent_message_id = $reply_id;

        $date = date('Y-m-d H:i:s');
        $stmt->bind_param("sssssss", $username, $parent_message_id, $date, $content, $localisation, $image, $category);
        $stmt->execute();

        // On récupère l'id du message inséré
        $message_id = $stmt->insert_id;

        session_start();
        if(!empty($_POST['animaux'])) {
            foreach($_POST['animaux'] as $animal_id){
                $stmt = $conn->prepare("INSERT INTO message_animaux (message_id, animal_id) VALUES (?, ?)");
                $stmt->bind_param("is", $message_id, $animal_id);
                $stmt->execute();
            }
        }

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
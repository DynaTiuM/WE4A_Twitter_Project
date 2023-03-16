<?php

if(isset($_POST["content"])) {
    $content = SecurizeString_ForSQL($_POST["content"]);
    $username = $_COOKIE["username"];

    preg_match_all('/#(\w+)/', $content, $matches);
    $hashtags = $matches[1];

    global $conn;

    $stmt = $conn->prepare("INSERT INTO message (auteur_username, date, contenu, localisation, image) VALUES (?, ?, ?, ?, ?)");
    $image = 'null';
    $localisation = 'null';
    $stmt->bind_param("sssss", $username, date('Y-m-d H:i:s'), $content, $localisation, $image);
    $stmt->execute();

    // On récupère l'id du message inséré
    $message_id = $stmt->insert_id;

    foreach ($hashtags as $hashtag) {
        $stmt = $conn->prepare("INSERT INTO hashtag (tag, message_id) VALUES (?, ?)");
        $stmt->bind_param("si", $hashtag, $message_id);
        $stmt->execute();
    }

    header("Location: index.php");
    exit();
}


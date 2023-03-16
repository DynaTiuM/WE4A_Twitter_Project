<?php

if(isset($_POST["content"])) {
    $content = SecurizeString_ForSQL($_POST["content"]);
    $username = $_COOKIE["username"];

    global $conn;

    $query = "INSERT INTO `message` VALUES (null, '$username', NOW(), '$content', null, null, null, null )";
    $conn->query($query);

    header("Location: index.php");
    exit();
}


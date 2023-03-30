<?php

date_default_timezone_set('CET');

// Utilisez $conn pour exécuter les requêtes SQL


function determinePetOrUser($conn, $identifier) {
    if (User::exists($conn, $identifier)) {
        return 'user';
    } elseif (Animal::exists($conn, $identifier)) {
        return 'pet';
    }
    return null;
}

/*$type = determinePetOrUser($conn, $identifier);
if ($type === 'user') {
    // Traitez l'identifiant comme un utilisateur
} elseif ($type === 'pet') {
    // Traitez l'identifiant comme un animal
} else {
    // Aucun enregistrement trouvé
}*/


// Function to check login. returns an array with 2 booleans
// Boolean 1 = is login successful, Boolean 2 = was login attempted
//--------------------------------------------------------------------------------

function motificationProfile($type) {
    global $conn;
    $username = $conn->secureString_ForSQL($_GET["username"]);

    modificationAvatarProfile($type, $username);

    $nom = $conn->secureString_ForSQL($_POST['nom']);
    $bio = $conn->secureString_ForSQL($_POST['bio']);

    if($type == 'utilisateur') {
        $user = new User($username);
        $prenom = $conn->secureString_ForSQL($_POST['prenom']);
        $date = $conn->secureString_ForSQL($_POST['date']);
        $password = $conn->secureString_ForSQL($_POST['password']);
        //$user->updateProfile($prenom, $nom, $date, $password, $bio);
    } else {
        $animal = new Animal($username);
        $sexe = $conn->secureString_ForSQL($_POST['sexe']);
        $age = $conn->secureString_ForSQL($_POST['age']);
        $espece = $conn->secureString_ForSQL($_POST['espece']);
        $adoption = $conn->secureString_ForSQL($_POST['adoption']);
        //$animal->updateProfile($nom, $age, $sexe, $bio, $espece, $adoption);
    }
}

function modificationAvatarProfile($type, $username) {
    global $conn;

    // Vérification que l'image est bien envoyée
    if (isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"]["tmp_name"])) {

        // Ouverture du fichier image
        $image_file = $_FILES['avatar']['tmp_name'];
        $image_data = file_get_contents($image_file);

        // Préparation de la requête SQL en utilisant une requête préparée
        $query = $conn->prepare("UPDATE $type SET avatar = ? WHERE username = ?");

        // Liaison des paramètres
        $query->bind_param('ss', $image_data, $username);

        // Exécution de la requête
        $query->execute();

        // Fermeture de la requête
        $query->close();
    }

}


function getParentMessageId($id_son) {
    global $conn;

    $id_son = SecurizeString_ForSQL($id_son);

    $stmt = $conn->prepare("SELECT parent_message_id FROM message WHERE id = ?");
    $stmt->bind_param("i", $id_son);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['parent_message_id'];
    }
    $stmt->close();
}


function numFollowers($username, $type) {
    global $conn;
    $query = "SELECT COUNT(*) FROM suivre WHERE suivi_id_$type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_column();

}



function getNotifications() {
    global $conn;
    $username = $_COOKIE['username'];
    $query = "SELECT notification.*, message.*, utilisateur.*, GROUP_CONCAT(animal.nom SEPARATOR ', ')
        FROM notification
        INNER JOIN message ON notification.message_id = message.id
        LEFT JOIN message_animaux ON message.id = message_animaux.message_id
        LEFT JOIN animal ON message_animaux.animal_id = animal.id
        INNER JOIN utilisateur ON message.auteur_username = utilisateur.username
        WHERE notification.utilisateur_username = ?
        GROUP BY notification.id
        ORDER BY notification.vue ASC, notification.date DESC;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        return $result;
    }
    return null;

}

function markNotificationAsRead($message_id) {
    global $conn;
    /*$query = "UPDATE notification SET vue = 1 WHERE message_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();*/
}

// Function to get current URL, without file name
//--------------------------------------------------------------------------------
function GetUrl() {
    $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
    $url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
    $url .= dirname($_SERVER["REQUEST_URI"]);
    return $url;
}


?>
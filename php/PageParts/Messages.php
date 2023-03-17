<?php
function mainMessages($loginStatus) {
        global $conn;

    if(isset($_GET['tag'])){
        $tag = $_GET['tag'];
        $query = "SELECT DISTINCT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username FROM message JOIN hashtag ON message.id = hashtag.message_id JOIN utilisateur ON message.auteur_username=utilisateur.username WHERE message.contenu like '%$tag%' OR hashtag.tag = '$tag' ORDER BY message.date DESC";
    } else {
        if($loginStatus[0]) {
            $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username FROM message JOIN utilisateur ON message.auteur_username=utilisateur.username ORDER BY message.date DESC";
        }
    }
        $result = $conn->query($query);

        if($result) {
            displayContent($result);
        }
}

function displayContent($result) {
    while($row = $result->fetch_assoc()) {
        $auteur_username = $row['auteur_username'];
        $contenu = $row['contenu'];
        $date = $row['date'];

        // Convertir la date en timestamp
        $timestamp = strtotime($date);

        // Calculer la différence de temps
        $diff = date_diff(new DateTime("@$timestamp"), new DateTime());

        $days = $diff->d;
        $hours = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;

        if ($days > 0) {
            $diff = $days."j";
        } elseif ($hours > 0) {
            $diff = $hours."h";
        } elseif ($minutes > 0) {
            $diff = $minutes."m";
        } else {
            $diff = $seconds."s";
        }

        ?>
        <div class="message">
            <a href = "profil.php">
                <img class = "AvatarMessage" src = "./images/titan.png">
            </a>
            <div class = "tweet-content">
                <div class = "tweet-header">
                    <h1 class="name"><?php echo $row["prenom"] . ' ' . $row["nom"]; ?></h1>
                    <?php
                    echo '<h1 class = "tweet-information">'. ' @' . $auteur_username . ' · ' . $diff . '</h1>'; ?>
                </div>
                <div class = "tweet-content">
                    <?php echo'<p>' . $contenu . '</p>'; ?>
                </div>
            </div>
        </div>
<?php
    }
}

function profilMessages() {
    global $conn;

    $username = $_GET["username"];

    $query = "SELECT message.*, utilisateur.nom, utilisateur.prenom, utilisateur.username FROM message JOIN utilisateur ON message.auteur_username=utilisateur.username WHERE auteur_username = '$username' ORDER BY date DESC";
    $result = $conn->query($query);

    if($result) {
        displayContent($result);
    }
}

    ?>
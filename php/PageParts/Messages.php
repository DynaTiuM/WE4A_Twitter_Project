<?php
function mainMessages($loginStatus) {
    if($loginStatus[0]) {
        global $conn;

        $query = "SELECT * FROM `message` ORDER BY date DESC";
        $result = $conn->query($query);

        if($result) {
            while($result->fetch_assoc()) {
                $query = "SELECT utilisateur.nom, utilisateur.prenom, message.owner, message.contenu, message.date FROM message JOIN utilisateur ON message.owner=utilisateur.username ORDER BY message.date DESC";
                $result = $conn->query($query);

                if($result) {
                    displayContent($result);
                }
            }
        }
    }
}

function displayContent($result) {
    while($row = $result->fetch_assoc()) {
        $owner = $row['owner'];
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
                    echo '<h1 class = "tweet-information">'. ' @' . $owner . ' · ' . $diff . '</h1>'; ?>
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

    $query = "SELECT * FROM message WHERE owner = '$username' ORDER BY date DESC";
    $result = $conn->query($query);

    if($result) {
        while($result->fetch_assoc()) {
            $query = "SELECT utilisateur.nom, utilisateur.prenom, message.owner, message.contenu, message.date FROM message JOIN utilisateur ON message.owner=utilisateur.username WHERE owner = '$username' ORDER BY message.date DESC";
            $result = $conn->query($query);

            if($result) {
                displayContent($result);
            }
        }
    }
}

    ?>
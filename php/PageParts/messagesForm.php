<?php

function displayContent($result) {
    while($row = $result->fetch_assoc()) {
        $information = getInformationMessage($row);
        $contenu = $information[0];
        $date = $information[1];
        $avatar = $information[2];
        $image = $information[3];
        $auteur_username = $information[4];

        ?>
        <div class="message">
            <a href ="profil.php?username=<?php echo $auteur_username; ?>" >
                <img class = "AvatarMessage" src="data:image/jpeg;base64,<?php echo base64_encode($avatar); ?>" >
            </a>
            <div class = "tweet-content">
                <div class = "tweet-header">
                    <h1 class="name"><?php echo $row["prenom"] . ' ' . $row["nom"]; ?></h1>
                    <?php
                    echo '<h1 class = "tweet-information">'. ' @' . $auteur_username . ' · ' . $date . '</h1>'; ?>
                </div>
                <div class = "tweet-content">
                    <?php echo'<p>' . stripcslashes($contenu) . '</p>'; ?>
                    <img src="data:image/png;base64,<?php echo base64_encode($image); ?>" >
                </div>
            </div>
        </div>
<?php
    }
}

    ?>
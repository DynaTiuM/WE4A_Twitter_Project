<?php

function displayContent($result) {
    while($row = $result->fetch_assoc()) {
        $information = getInformationMessage($row);
        $contenu = $information[0];
        $date = $information[1];
        $avatar = $information[2];
        $image = $information[3];
        $localisation = $information[4];
        $auteur_username = $information[5];

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
                    <?php if($localisation != null) {
                        echo '<div><img style="width: 1vw; float: left;" src="./images/localisation.png">
                                <p class="localisation-message" style="margin-left: 1vw;">' . $localisation . '</p>
                            </div>';
                    }?>
                    <?php echo'<p>' . stripcslashes($contenu) . '</p>';
                    if($image != null) {?>
                    <img class = "message-image" src="data:image/png;base64,<?php echo base64_encode($image); ?>" >

                    <?php }?>
                </div>
            </div>
        </div>
<?php
    }
}

    ?>
<?php

if (!function_exists('displayContent')) {
function displayContent($row) {
        $information = getInformationMessage($row);
        $prenom = $information[7];
        $nom = $information[8];
        $id = $information[0];
        $content = $information[1];
        $date = $information[2];
        $avatar = $information[3];
        $image = $information[4];
        $localisation = $information[5];
        $auteur_username = $information[6];

        $filename = basename($_SERVER['SCRIPT_FILENAME']);

        ?>
        <div class="message">
            <a href ="profil.php?username=<?php echo $auteur_username; ?>" >
                <img class = "AvatarMessage" src="data:image/jpeg;base64,<?php echo base64_encode($avatar); ?>" >
            </a>
            <div class>
                <div class = "tweet-header">
                    <h1 class="name"><?php echo $prenom . ' ' . $nom; ?></h1>
                    <?php
                    echo '<h1 class = "tweet-information">'. ' @' . $auteur_username . ' · ' . $date . '</h1>'; ?>
                </div>
                <div class = "tweet-content">
                    <?php if($localisation != null) {
                        echo '<div>
                                <img style="width: 1vw; float: left;" src="./images/localisation.png" alt = "Localisation">
                                <p class="localisation-message" style="margin-left: 1vw;">' . $localisation . '</p>
                            </div>';
                    }?>
                    <label>
                        <a class = "display-answer" href="explorer.php?answer=<?php echo $id ?>">
                            <p><?php echo stripcslashes($content) ?></p>
                        </a>
                    </label>
                    <?php
                    if($image != null) {?>
                    <img class = "message-image" src="data:image/png;base64,<?php echo base64_encode($image); ?>" >

                    <?php }?>
                </div>

                <div style="display: flex;">
                    <?php if(!isset($_GET['reply_to'])) { ?>
                        <div>
                            <form method="get" action="<?php echo $filename ?>">
                                <input type="hidden" name="reply_to" value="<?php echo $id?>">
                                <button type="submit" class="comment">
                                    <img style="width: 1.3vw; padding: 0.6vw;" src="./images/comment.png" alt="Commenter">
                                </button>
                            </form>
                        </div>
                    <?php } ?>
                    <form method="post" action="">
                        <input type="hidden" name="like" value="<?php echo $id?>">
                        <button type="submit" class="comment">
                            <?php if(isLiked($id)) { ?>
                                <img style="width: 1.3vw; padding: 0.6vw;" src="./images/liked.png" alt="Aimer">
                            <?php } else { ?>
                                <img style="width: 1.3vw; padding: 0.6vw;" src="./images/like.png" alt="Ne plus aimer">
                            <?php } ?>
                        </button>
                    </form>
                </div>


            </div>
        </div>
<?php
}
}
?>
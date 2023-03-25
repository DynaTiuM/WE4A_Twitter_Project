<?php

if (!function_exists('displayContent')) {
function displayContent($row) {
    require_once('./PageParts/databaseFunctions.php');

    ConnectDatabase();
    $loginStatus = isLogged();

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
        $category = $information[9];

        ?>
        <div class="message">
            <a href ="profile.php?username=<?php echo $auteur_username; ?>" >
                <img class = "avatar-message" src="data:image/jpeg;base64,<?php echo base64_encode($avatar); ?>" >
            </a>
            <div class>
                <div class = "tweet-header">
                    <h1 class="name"><?php echo $prenom . ' ' . $nom; ?></h1>
                    <?php
                    echo '<h1 class = "tweet-information">'. ' @' . $auteur_username . ' · ' . $date . '</h1>'; ?>

                   <!-- <div class = "parameters"><a>...</a></div> -->
                </div>
                <div class = "tweet-content">
                    <?php
                    switch($category) {
                        case null:
                            break;
                        case 'evenement':
                            echo '<div class = "event" style = "font-size: 0.8vw; padding: 0.4vw; margin-left: 0">'.$category.'</div>';
                            break;
                        case 'sauvetage':
                            echo '<div class = "rescue" style = "font-size: 0.8vw; padding: 0.4vw; margin-left: 0">'.$category.'</div>';
                            break;
                        case 'conseil':
                            echo '<div class = "advice" style = "font-size: 0.8vw; padding: 0.4vw; margin-left: 0">'.$category.'</div>';
                            break;
                    }
                    if($localisation != null) {
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
                    <?php if(!isset($_POST['reply_to']) && $loginStatus) { ?>
                        <div>
                            <form method="post" action="">
                                <input type="hidden" name="reply_to" value="<?php echo $id?>">
                                <button type="submit" class="comment">
                                    <img style="width: 1.5vw; padding: 0.6vw;" src="./images/comment.png" alt="Commenter">
                                </button>
                            </form>
                        </div>
                    <?php } ?>
                    <form method="post" action="">
                        <input type="hidden" name="like" value="<?php echo $id?>">
                        <button type="submit" class="comment">
                            <?php

                            if(isLiked($id)) { ?>
                                    <label style ="display: flex;">
                                        <img style="width: 1.5vw; padding: 0.6vw;" src="./images/liked.png" alt="Aimer">
                                        <span style =" margin-top: 1vw; margin-left: -0.3vw;"><?php echo numLike($id)?></span>
                                    </label>
                            <?php } else { ?>
                                <img style="width: 1.5vw; padding: 0.6vw;" src="./images/like.png" alt="Ne plus aimer">
                            <?php } ?>
                        </button>
                    </form>

                    <div id = "pets">
                        <div style = "display: flex; margin-left: 1vw; margin-top: 0.2vw;">
                            <?php $result = findPets($id);
                            while($row = $result->fetch_assoc()) {  ?>
                                <label>
                                    <a href = "./profile.php?username=<?php echo $row['id']; ?>"><img class="pet-image-message" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Animal : <?php echo $row['nom']?>"></a>
                                </label>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>




            </div>
        </div>
<?php
}
}
?>
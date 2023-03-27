<?php

if (!function_exists('displayContent')) {
    function displayContent($row, $parent = false) {
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
        $organisation = $information[10];

        if(isset($_GET['answer']) && (isFollowing($auteur_username))) {
            markNotificationAsRead($id);
        }
        ?>
            <div class="message">

                <a href ="profile.php?username=<?php echo $auteur_username; ?>" >
                    <img class = "avatar-message" src="data:image/jpeg;base64,<?php echo base64_encode($avatar); ?>" >
                </a>
                <div class>
                    <div class = "tweet-header">
                        <?php
                        if($organisation) echo "<a href = './profile.php?username=$auteur_username'><h2 class='name-profile'>" . $prenom . " " . $nom . "<img title=\"Ce compte est certifié car il s'agit d'une organisation\" phpmailer='./images/organisation.png' style='margin-left: 0.8vw; width:1.4vw; height: 1.4vw;'></h2></a>";
                        else  echo "<a href = './profile.php?username=$auteur_username'><h2 class = 'name-profile'>" . $prenom . " " . $nom . " </h2></a>";
                        echo '<h2 class = "tweet-information">'. ' @' . $auteur_username . ' · ' . $date . '</h2>'; ?>

                       <!-- <div class = "parameters"><a>...</a></div> -->
                    </div>
                        <div class = "tweet-content">
                            <?php

                            switch ($category) {
                                case null:
                                    break;
                                case 'evenement':
                                    echo '<a href = "./explorer.php?category=evenement"><span class = "event" style = "padding: 0.5vw; margin-left: 0; margin-top: 1vw">ÉVÉNEMENT</span></a>';
                                    break;
                                case 'sauvetage':
                                    echo '<a href = "./explorer.php?category=sauvetage"><span class = "rescue" style = "padding: 0.5vw; margin-left: 0; margin-top: 1vw">SAUVETAGE</span></a>';
                                    break;
                                case 'conseil':
                                    echo '<a href = "./explorer.php?category=conseil"><span class = "advice" style = "padding: 0.5vw; margin-left: 0; margin-top: 1vw">CONSEIL</span></a>';
                                    break;
                            }
                            if($localisation != null) {
                                echo '<div>
                                        <img style="width: 1vw; float: left;" phpmailer="./images/localisation.png" alt = "Localisation">
                                        <p class="localisation-message" style="margin-left: 1vw;">' . $localisation . '</p>
                                    </div>';
                            }?>

                            <a class = "display-answer" href="./explorer.php?answer=<?php echo $id ?>">
                            <label>
                                    <p><?php echo stripcslashes($content) ?></p>
                            </label>
                            <?php
                            if($image != null) {?>
                            <img class = "message-image" src="data:image/png;base64,<?php echo base64_encode($image); ?>" >
                            <?php }?>

                            </a>
                        </div>

                    <?php if(!$parent) { ?>
                    <div style="display: flex;">
                        <?php if(!isset($_POST['reply_to'])) { ?>
                            <div>
                                <form method="post" action="">
                                    <input type="hidden" name="reply_to" value="<?php echo $id?>">
                                    <button type="submit" class="comment" <?php if(!$loginStatus) { ?> disabled<?php } ?>>
                                        <label style ="display: flex;">
                                        <img style="width: 1.5vw; padding: 0.6vw;" src="./images/comment.png" alt="Commenter">
                                        <?php if(isCommented($id)) { ?>
                                        <span style =" margin-top: 1vw; margin-left: -0.3vw; font-size: 1vw"><?php echo numComments($id)?></span>
                                        <?php } ?>
                                        </label>
                                    </button>
                                </form>
                            </div>
                        <?php } ?>
                        <form method="post" action="">
                            <input type="hidden" name="like" value="<?php echo $id?>">
                            <button type="submit" class="comment"  <?php if(!$loginStatus) { ?> disabled<?php } ?>>
                                <label style ="display: flex;">
                                <?php
                                if(isLiked($id)) { ?>
                                    <img style="width: 1.5vw; padding: 0.6vw;" src="./images/liked.png" alt="Aimer">

                                <?php } else { ?>
                                    <img style="width: 1.5vw; padding: 0.6vw;" src="./images/like.png" alt="Ne plus aimer">
                                <?php }
                                if(numLike($id) > 0) {?>
                                <span style =" margin-top: 1vw; margin-left: -0.3vw; font-size: 1vw"><?php echo numLike($id)?></span>
                                <?php }?>
                                </label>
                            </button>
                        </form>
                        <div id = "pets">
                            <div style = "display: flex; margin-left: 1vw; margin-top: 0.2vw;">
                                <?php $result = findPets($id);
                                while($row = $result->fetch_assoc()) {  ?>
                                    <label>
                                        <a href = "./profile.php?username=<?php echo $row['id']; ?>"><img style = "margin-left: 0.3vw" class="pet-image-message" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Animal : <?php echo $row['nom']?>"></a>
                                    </label>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php } ?>

                </div>
            </div>
          <?php
    }
}
?>
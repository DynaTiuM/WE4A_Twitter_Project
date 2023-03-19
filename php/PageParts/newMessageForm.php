<?php
$filename = basename($_SERVER['SCRIPT_FILENAME']);

include("adressSearch.php");
?>

<!DOCTYPE html>

<html lang ="fr">
<head>
    <meta charset = "utf-8">
    <script src="https://maps.googleapis.com/maps/api/js?key=KEY&libraries=places"></script>

    <link rel = "stylesheet" href = "./css/stylesheet.css">

</head>

<body>

<div class = "NewMessage">
    <form action="" method="post" enctype="multipart/form-data">
        <a href="profil.php?username=<?php echo $_COOKIE['username']; ?>">
            <img class = "AvatarMessage"  src="data:image/jpeg;base64,<?php echo base64_encode($image); ?> " />
        </a>
        <label>
            <textarea name = "content" class = "message-content" placeholder="Message" rows="2" maxlength="240" required></textarea>
        </label>
        <span class = "Border" style="width: 80%;"></span>
        <div class = "ButtonPosition">
            <button class = "Tweeter" type = "submit" name = "submit">Envoyer</button>
        </div>

        <div class = "icons">
            <div class = "button-wrap">
                <label for ="file-input"></label>
                <input id="file-input" type="file" name="image" accept=".jpg, .jpeg, .png">
            </div>

            <img onclick="showMap()" src="./images/localisation.png" class ="icon">
        </div>
        </form>

</div>
<div id="new-answer" class="window-background">
    <div class="window-content">
        <button class="close" onclick="window.location.href='explorer.php'">&times;</button>
        <h2 class="window-title">Nouveau commentaire</h2>
        <?php if(isset($_GET['reply_to'])) {
            displayContentById($_GET['reply_to']);
        } ?>
        <?php include("./PageParts/newMessageForm.php"); ?>
    </div>
</div>


</body>

</html>

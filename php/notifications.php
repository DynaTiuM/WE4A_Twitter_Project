<!DOCTYPE html>
<html lang = "fr">
<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <link rel="shortcut icon" href="./favicon.ico">
</head>
<body>
<div class = "Container">
    <?php
    global $loginStatus;
    include("./PageParts/navigation.php");
    include("./PageParts/hubMessages.php");

    /* DUPLICATED!!!! */
    if(isset($_POST['like']) && $loginStatus) likeMessage($_POST['like']);

    if(isset($_POST["submit"])) {
        include("./PageParts/sendingMessage.php");
        sendMessage($_POST["submit"]);
    }

    ?>

    <div class = "MainContainer">
        <div class = "h1-container">
            <h1>Notifications</h1>
        </div>
        <div class = "spacing"></div>
            <?php

            include("./PageParts/popupnewMessage.php");
            popUpNewMessage();
            if ($loginStatus) {
                $notifications = getNotifications();
                if($notifications) {
                    while($row = $notifications->fetch_assoc()) {
                        $id = $row['message_id'];
                        ?>
                        <div <?php if($row['vue'] == 0) { ?> style = "background-color: #d3eae0" <?php } ?>>
                            <?php
                            displayContentById($id);
                            ?>
                        </div>
                    <?php
                    }
                }
                else {
                    echo '<h4>Vous n\'avez pas de notifications</h4>';
                }

            }
            else {
                echo '<h4>Connectez-vous pour accéder aux notifications</h4>';
            }
        ?>
    </div>

    <?php
    include("./PageParts/trends.php");

    include("./PageParts/popupNewMessageForm.php");
    ?>
</div>

</body>

</html>
<!DOCTYPE html>
<html lang = "fr">
<head>
    <title>Abonnements</title>
</head>
<body>
<?php
require_once('./PageParts/hubMessages.php');

displayContainer('subs');

function mainMessages($loginStatus) {
    ?>
    <div class = "hub-messages">
        <?php
        include("./PageParts/messageForm.php");
        if(isset($_GET['answer']))
            mainMessagesQuery($loginStatus, 'subs', $_GET['answer']);
        else
            mainMessagesQuery($loginStatus, 'subs', null);
        ?>

    </div>
    <?php
}
?>
</body>
</html>

<!DOCTYPE html>
<html lang = "fr">
<head>
    <title>Explorer</title>
</head>
<body>
<?php
require_once('./PageParts/hubMessages.php');

displayContainer('explorer');



function explorerMessages($loginStatus) {
    ?>
    <div class = "hub-messages">
        <div class = "center">
            <div style ="display: inline-flex; margin-bottom: 0">
                <a href = "./explorer.php?category=sauvetage"><p class = "rescue" style = "font-size: 1.3vw">Sauvetages</p></a>
                <a href = "./explorer.php?category=evenement"><p class = "event" style = "font-size: 1.3vw">Événements</p></a>
                <a href = "./explorer.php?category=conseil"><p class = "advice" style = "font-size: 1.3vw">Conseils</p></a>
            </div>
        </div>

        <?php
        include("./PageParts/messageForm.php");

        if(isset($_GET['answer'])) {
            if($_GET['answer'] != '') {
                $parent_message_id = getParentMessageId($_GET['answer']);
                if($parent_message_id) {
                    ?>
                    <div class ="parent-message">
                        <?php displayContentbyId($parent_message_id, true);?>
                        <span class = "container-parent-message"></span>
                    </div>
                    <?php
                }
            }

            displayContentById($_GET['answer']);
            include("./PageParts/adressSearch.php");
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./PageParts/newMessageForm.php");
            mainMessagesQuery($loginStatus, 'explorer', $_GET['answer']);
        }
        elseif(isset($_GET['category'])) {
            displayContentByCategory($_GET['category']);
        }
        else {
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./PageParts/newMessageForm.php");
            mainMessagesQuery($loginStatus, 'explorer', null);
        }

        ?>
    </div>
    <?php
}

?>
</body>
</html>

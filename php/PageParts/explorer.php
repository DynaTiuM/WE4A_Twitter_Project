<?php
require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");

global $globalDb;
global $globalUser;
$globalDb = new Database();
$conn = $globalDb->getConnection();
$globalUser = new User($conn, $globalDb);
$message = new Message($conn);
?>


<!DOCTYPE html>
<html lang = "fr">
<head>
    <title>Explorer</title>
</head>
<body>
<?php
require_once('./hubMessages.php');

displayContainer('explorer');

function explorerMessages($loginStatus) {
    global $message;
    ?>
    <div class = "hub-messages">
        <div class = "center">
            <div style ="display: inline-flex; margin-bottom: 0">
                <a href = "explorer.phpategory=sauvetage"><p class = "rescue" style = "font-size: 1.3vw">Sauvetages</p></a>
                <a href = "explorer.phpategory=evenement"><p class = "event" style = "font-size: 1.3vw">Événements</p></a>
                <a href = "explorer.phpategory=conseil"><p class = "advice" style = "font-size: 1.3vw">Conseils</p></a>
            </div>
        </div>

        <?php
        include("./messageForm.php");

        if(isset($_GET['answer'])) {
            if($_GET['answer'] != '') {
                $parent_message_id = getParentMessageId($_GET['answer']);
                if($parent_message_id) {
                    ?>
                    <div class ="parent-message">
                        <?php $message->displayContentbyId($parent_message_id, true);?>
                        <span class = "container-parent-message"></span>
                    </div>
                    <?php
                }
            }

            displayContentById($_GET['answer']);
            include("./adressSearch.php");
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");
            mainMessagesQuery($loginStatus, 'explorer', $_GET['answer']);
        }
        elseif(isset($_GET['category'])) {
            displayContentByCategory($_GET['category']);
        }
        else {
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");
            $message->mainMessagesQuery($loginStatus, 'explorer', null);
        }

        ?>
    </div>
    <?php
}

?>
</body>
</html>

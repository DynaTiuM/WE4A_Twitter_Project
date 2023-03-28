<?php
function mainMessages($loginStatus) {
    global $globalMessage;
    ?>
    <div class = "hub-messages">
        <?php
        include("./messageForm.php");
        if(isset($_GET['answer']))
            $globalMessage->mainMessagesQuery($loginStatus, 'subs', $_GET['answer']);
        else
            $globalMessage->mainMessagesQuery($loginStatus, 'subs', null);
        ?>

    </div>
    <?php
}

function explorerMessages($loginStatus) {
    global $globalMessage;
    ?>
    <div class = "hub-messages">
        <div class = "center">
            <div style ="display: inline-flex; margin-bottom: 0">
                <a href = "explorer.php?category=sauvetage"><p class = "rescue" style = "font-size: 1.3vw">Sauvetages</p></a>
                <a href = "explorer.php?category=evenement"><p class = "event" style = "font-size: 1.3vw">Événements</p></a>
                <a href = "explorer.php?category=conseil"><p class = "advice" style = "font-size: 1.3vw">Conseils</p></a>
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
                        <?php $globalMessage->displayContentbyId($parent_message_id);?>
                        <span class = "container-parent-message"></span>
                    </div>
                    <?php
                }
            }

            $globalMessage->displayContentById($_GET['answer']);
            include("./adressSearch.php");
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");
            $globalMessage->mainMessagesQuery($loginStatus, 'explorer', $_GET['answer']);
        }
        elseif(isset($_GET['category'])) {
            $globalMessage->displayContentByCategory($_GET['category']);
        }
        else {
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");
            $globalMessage->mainMessagesQuery($loginStatus, 'explorer', null);
        }

        ?>
    </div>
    <?php
}
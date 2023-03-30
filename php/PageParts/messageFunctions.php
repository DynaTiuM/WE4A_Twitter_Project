<?php
function mainMessages($loginStatus) {
    global $conn, $globalDb; // Ajoutez cette ligne
    ?>
    <div class = "hub-messages">
        <?php
        include("./messageForm.php");
        if(isset($_GET['answer'])) {
            $messageIds = Message::mainMessagesQuery($conn, $globalDb, $loginStatus, 'subs', $_GET['answer']);
            if($messageIds)
                Message::displayMessages($conn, $globalDb, $messageIds);
            else
                echo '<h4><br>Aucune réponse disponible</h4>';
        }
        else {
            $messageIds = Message::mainMessagesQuery($conn, $globalDb, $loginStatus, 'subs', null);
            if($messageIds)
                Message::displayMessages($conn, $globalDb, $messageIds);
            else
                echo '<h4><br>Aucun message disponible</h4>';
        }
        ?>

    </div>
    <?php
}

function explorerMessages($loginStatus) {
    global $conn, $globalDb;
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
            if (isset($_GET['answer'])) {
                if ($_GET['answer'] != '') {
                    $message = new Message($conn, $globalDb);
                    $message->loadMessageById($_GET['answer']);
                    $parent_message_id = $message->getParentMessageId();

                    if ($parent_message_id) {
                        ?>
                        <div class="parent-message">
                            <?php
                            $parent_message = new Message($conn, $globalDb);
                            $parent_message->loadMessageById($parent_message_id);
                            $parent_message->displayContent();
                            ?>
                            <span class="container-parent-message"></span>
                        </div>
                        <?php
                    }

                    $message->displayContent();
                }
            }



            include("./adressSearch.php");
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");
            $messageIds = Message::mainMessagesQuery($conn, $globalDb, $loginStatus, 'explorer', $_GET['answer']);
            if($messageIds)
                Message::displayMessages($conn, $globalDb, $messageIds);
            else
                echo '<h4><br>Aucune réponse disponible</h4>';
        }
        elseif (isset($_GET['category'])) {
            $messageIds = Message::displayMessagesByCategory($conn, $globalDb, $_GET['category']);
            if($messageIds)
                Message::displayMessages($conn, $globalDb, $messageIds);
            else
                echo '<h4><br>Aucun message disponible dans cette catégorie</h4>';

        }
        else {
            if(!isset($_POST['reply_to']) && !isset($_POST['new-message']) && $loginStatus) include("./newMessageForm.php");
            $messageIds = Message::mainMessagesQuery($conn, $globalDb, $loginStatus, 'explorer', null);
            if($messageIds)
                Message::displayMessages($conn, $globalDb, $messageIds);
            else
                echo '<h4><br>Aucun message disponible</h4>';
        }

        ?>
    </div>
    <?php
}
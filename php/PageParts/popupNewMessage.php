<div id="new-message" class="window-background">
    <div class="window-content">
        <?php if(isset($_GET['reply_to'])) {

            echo '<span class="close" onclick="closeWindowToNewDestination(\'new-message\')">&times;</span>';

            echo '<h2 class = "window-title">Nouveau commentaire</h2>';

            displayContentById($_GET['reply_to']);
        }
        else {
            ?>

            <span class="close" onclick="closeWindow('new-message')">&times;</span>
            <h2 class = "window-title">Nouveau message</h2>
        <?php
        }
        require_once("./PageParts/newMessageForm.php");  ?>
    </div>
</div>
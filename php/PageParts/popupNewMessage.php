<div id="new-message" class="window-background">
    <div class="window-content">
        <?php if(isset($_POST['reply_to'])) {

            echo '<span class="close" onclick="closeWindowToNewDestination(\'new-message\', location.href)">&times;</span>';

            echo '<h2 class = "window-title">Nouveau commentaire</h2>';

            displayContentById($_POST['reply_to']);
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

<div id="display-pet" class="window-background">
    <div class="window-content">
        <span class="close" onclick="closeWindow('display-pet')">&times;</span>
        <h2 class = "window-title">Sélectionner animaux</h2>

       <?php require_once("./PageParts/selectPetForm.php");  ?>
    </div>
</div>
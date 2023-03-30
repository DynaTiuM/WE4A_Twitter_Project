<?php

function displayModificationProfile() {
    ?>
<div id="modification-profile" class="window-background">
    <div class="window-content">
        <span class="close" onclick="closeWindow('modification-profile')">&times;</span>
        <h2 class = "window-title">Modification du profil</h2>
        <?php include("./profileModificationForm.php"); ?>
    </div>
</div>
<?php
}

function displayAddPet() {
    ?>
<div id="add-pet" class="window-background">
    <div class="window-content">
        <span class="close" onclick="closeWindow('add-pet')">&times;</span>
        <h2 class = "window-title">Ajout d'un animal</h2>
        <?php include("./addPetForm.php"); ?>
    </div>
</div>
<?php
}

function displayModificationPetProfile() {
    ?>
    <div id="modification-pet-profile" class="window-background">
        <div class="window-content">
            <span class="close" onclick="closeWindow('modification-pet-profile')">&times;</span>
            <h2 class = "window-title">Modification du profil de l'animal</h2>
            <?php include("./petProfileModificationForm.php"); ?>
        </div>
    </div>
<?php
}

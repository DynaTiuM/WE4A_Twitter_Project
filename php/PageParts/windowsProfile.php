<?php

/**
 * Fonction permettant d'afficher une pop-up avec un titre spécifique et une adresse de contenu spécifique
 *
 * @param $title
 * @param $url
 * @return void
 */
function displayPopUpProfile($title, $url) {
    ?>
<div id="pop-up-profile" class="window-background">
    <div class="window-content">
        <span class="close" onclick="closeWindow('pop-up-profile')">&times;</span>
        <h2 class = "window-title"><?php echo $title ?></h2>
        <?php include($url); ?>
    </div>
</div>
<?php
}

/**
 * Fonction permettant d'afficher le formulaire d'ajout d'un animal
 *
 * @return void
 */
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


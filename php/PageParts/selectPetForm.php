<option><?php
    $result = displayPets();
    while($row = $result->fetch_assoc()) {
        ?>
        <div class="image-container">
            <label for="<?php echo $row['id']?>">
                <img class="image-modification" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Bouton parcourir">
            </label>
            <label><?php echo $row['nom']?></label>
        </div>
        <input type = "checkbox" id = "<?php echo $row['id']?>" name = "animaux[]" value = "<?php echo $row['id']?>">
    <?php }

    ?></option>
<option><?php
    $result = displayPets();
    while($row = $result->fetch_assoc()) {
        ?>
        <label for ="<?php echo $row['id']?>"><?php echo $row['nom']?></label>
        <input type = "checkbox" id = "<?php echo $row['id']?>" name = "animaux[]" value = "<?php echo $row['id']?>">
    <?php }

    ?></option>
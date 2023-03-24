<?php
require_once('./PageParts/databaseFunctions.php');

ConnectDatabase();
$loginStatus = isLogged();
?>

<div class="Trends">
    <h1>Tendances pour vous</h1>
    <?php
    global $conn;


    $query = "SELECT tag, COUNT(*) AS count FROM hashtag GROUP BY tag ORDER BY count DESC LIMIT 10";

    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo"<div class = hashtag_block>";
        echo "<a href='explorer.php?tag=" . $row['tag'] . "' class='trend'>" ."#" . $row['tag'] ."</a>";
        echo "<p class='hashtags_count'>" . $row['count'] . " Messages" . "</p>";
        echo"</div>";
    }
    ?>
    <br>
    <?php
    if($loginStatus) {
        $result = displayPets($_COOKIE['username']);
        if($result->num_rows > 0) {
            ?>

            <h1>Animaux</h1>
            <div class = "center">
                <?php
                while($row = $result->fetch_assoc()) {?>
                    <a href = './profile.php?username=<?php echo $row['id']?>'><img class="pet-preview" src="data:image/jpeg;base64,<?php echo base64_encode($row['avatar']); ?>" alt="Animal : <?php echo $row['nom']?>"></a>
                    <p><?php echo $row['nom']?></p>
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }

    ?>
</div>
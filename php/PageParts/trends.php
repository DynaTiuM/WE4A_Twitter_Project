<?php
require_once('./PageParts/databaseFunctions.php');

ConnectDatabase();
$loginStatus = isLogged();
?>
<!DOCTYPE html>
<html lang = "fr">
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href = "./css/trends.css">
</head>
<body>

</body>
</html>
<div class="trends">
    <h1>Tendances pour vous</h1>
    <?php
    global $conn;


    $query = "SELECT tag, COUNT(*) AS count FROM hashtag GROUP BY tag ORDER BY count DESC LIMIT 10";

    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo"<a  href='explorer.php?tag=" . $row['tag'] . "'><div class = hashtag_block>";
        echo "<p class = 'trend'>" ."#" . $row['tag'] ."</p>";
        echo "<p class='hashtags_count'>" . $row['count'] . " Messages" . "</p>";
        echo"</div></a>";
    }
    ?>
    <?php
    if($loginStatus) {
        ?>
        <br>
        <h1>Catégories</h1>
        <a href = "explorer.php?category=sauvetage"><p class = "category">Sauvetage</p></a>
        <a href = "explorer.php?category=evenement"><p class = "category">Événements</p></a>
        <a href = "explorer.php?category=conseil"><p class = "category">Conseils</p></a>
    <?php
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
    else {
        echo '<br><h4>Connectez-vous pour accéder aux catégories</h4><br>';
    }

    ?>
</div>
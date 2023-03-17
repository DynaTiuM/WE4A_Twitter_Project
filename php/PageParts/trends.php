<div class="Trends">
    <h1>Tendances pour vous</h1>
    <?php
    global $conn;


    $query = "SELECT tag, COUNT(*) AS count FROM hashtag GROUP BY tag ORDER BY count DESC LIMIT 10";

    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo"<div class = hashtag_block>";
        echo "<a href='index.php?tag=" . $row['tag'] . "' class='trend'>" ."#" . $row['tag'] ."</a>";
        echo "<p class='hashtags_count'>" . $row['count'] . " Messages" . "</p>";
        echo"</div>";
    }
    ?>
</div>

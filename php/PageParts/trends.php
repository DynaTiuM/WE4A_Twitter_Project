<div class="Trends">
    <h2>Tendances</h2>
    <?php
    global $conn;

    $query = "SELECT tag, COUNT(*) AS count FROM hashtag GROUP BY tag ORDER BY count DESC LIMIT 10";

    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<p>" ."#" . $row['tag'] . " (" . $row['count'] . ")" . "</p>";
    }
    ?>
</div>

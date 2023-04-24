<?php
require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");

$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = User::getInstance($conn, $globalDb);
$loginStatus = $globalUser->isLoggedIn();
?>
<!DOCTYPE html>
<html lang = "fr">
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href = "../css/trends.css">
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
</div>
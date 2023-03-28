<?php
require_once("../Classes/Database.php");
require_once("../Classes/User.php");

global $globalDb;
global $globalUser;
$globalDb = new Database();
$conn = $globalDb->getConnection();
$globalUser = new User($conn, $globalDb);
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
    <title>Abonnements</title>
</head>
<body>
<?php
require_once('./hubMessages.php');
displayContainer('subs');

//displayContainer('subs');

function mainMessages($loginStatus) {
    ?>
    <div class = "hub-messages">
        <?php
        include("./messageForm.php");
        if(isset($_GET['answer']))
            mainMessagesQuery($loginStatus, 'subs', $_GET['answer']);
        else
            mainMessagesQuery($loginStatus, 'subs', null);
        ?>

    </div>
    <?php
}
?>
</body>
</html>

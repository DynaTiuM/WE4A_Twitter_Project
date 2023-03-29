<?php
require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");

global $globalDb;
global $globalUser;
global $globalMessage;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = User::getInstance($conn, $globalDb);
$globalMessage = new Message($conn, $globalDb);
?>
<!DOCTYPE html>
<html lang = "fr">
<head>
    <title>Explorer</title>
</head>
<body>
<?php
require_once('./hubMessages.php');
require_once('./messageFunctions.php');
displayContainer('explorer', $conn);


?>
</body>
</html>

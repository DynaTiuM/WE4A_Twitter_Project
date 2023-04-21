<?php
require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");

global $globalDb;
global $globalUser;
global $globalMessage;
global $conn;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = User::getInstance($conn, $globalDb);
$globalMessage = new Message($conn, $globalDb);
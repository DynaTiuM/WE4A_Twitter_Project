<?php
require_once("../Classes/Database.php");
require_once("../Classes/User.php");
require_once("../Classes/Message.php");

// fichier .php permettant d'initialiser les instances de la base de données, de la connexion à la base de données, l'utilisateur global qui parcours le site et le message global

global $globalDb;
global $globalUser;
global $globalMessage;
global $conn;
$globalDb = Database::getInstance();
$conn = $globalDb->getConnection();
$globalUser = User::getInstance($conn, $globalDb);
$globalMessage = new Message($conn, $globalDb);
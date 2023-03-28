<?php
function getDatabaseInstance() {
    static $globalDb = null;

    if ($globalDb === null) {
        $globalDb = new Database();
    }

    return $globalDb;
}

function getUserInstance($globalDb) {
    static $globalUser = null;

    if ($globalUser === null) {
        $conn = $globalDb->getConnection();
        $globalUser = new User($conn, $globalDb);
    }

    return $globalUser;
}

function getMessageInstance($globalDb) {
    static $globalMessage = null;

    if ($globalMessage === null) {
        $conn = $globalDb->getConnection();
        $globalMessage = new Message($conn);
    }

    return $globalMessage;
}


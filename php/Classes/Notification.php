<?php

class Notification
{
    private $id;
    private $username_notification;
    private $read = false;

    function __construct($username_notification, $read) {
        $this->id = $id;
    }
    function numNotifications() {
        global $conn;
        $username = SecurizeString_ForSQL($_COOKIE['username']);
        $query = "SELECT COUNT(*) FROM notification WHERE utilisateur_username = ? AND vue = ?";
        $stmt = $conn->prepare($query);
        $vue = 0;
        $stmt->bind_param("si", $username, $vue);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_Column();

    }
    public function getNotifications() { /* ... */ }
    public function markNotificationAsRead($message_id) { /* ... */ }
}
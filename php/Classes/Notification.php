<?php

class Notification
{
    private $id;
    private $username_notification;
    private $read = false;

    function __construct($username_notification) {
        $this->username_notification = $username_notification;
    }
    function numNotifications() {
        global $conn;
        $query = "SELECT COUNT(*) FROM notification WHERE utilisateur_username = ? AND vue = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $this->username_notification, $this->read);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_Column();

    }
    public function getNotifications() { /* ... */ }
    public function markNotificationAsRead($message_id) { /* ... */ }
}
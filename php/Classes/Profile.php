<?php

class Profile
{
    public function loadAvatar($username) { /* ... */ }
    public function updateAvatar($image) {
        if (isset($image) && is_uploaded_file($image["tmp_name"])) {
            $image_file = $image['tmp_name'];
            $image_data = file_get_contents($image_file);

            $conn = Database::getConnection();
            $query = $conn->prepare("UPDATE " . $this->getTableName() . " SET avatar = ? WHERE username = ?");

            $query->bind_param('ss', $image_data, $this->getUsername());

            $query->execute();

            $query->close();
        }
    }

    abstract protected function getTableName();

    public function modificationProfile($type) { /* ... */ }
    public function modificationAvatarProfile($type, $username) { /* ... */ }
    public function numFollowers($username, $type) { /* ... */ }
}
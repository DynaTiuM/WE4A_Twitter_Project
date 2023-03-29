<?php

class Image
{
    private $gdImage;
    private $formatedImage;

    public function __construct($image) {
        $this->gdImage = $this->createImage($image);
    }
    public function formatImage() {
        // Définir la nouvelle taille
        $new_width = 500;
        $ratio = $new_width / imagesx($this->gdImage);
        $new_height = imagesy($this->gdImage) * $ratio;

        // Créer une nouvelle image avec la nouvelle taille
        $new_image = imagecreatetruecolor($new_width, $new_height);

        // Redimensionner l'image
        imagecopyresampled($new_image, $this->gdImage, 0, 0, 0, 0, $new_width, $new_height, imagesx($this->gdImage), imagesy($this->gdImage));

        // Enregistrer l'image redimensionnée
        ob_start();
        imagejpeg($new_image);

        $this->formatedImage = ob_get_clean();
    }
    private function createImage($image) {
        if (isset($image) && is_uploaded_file($image["tmp_name"])) {
            $mime_type = mime_content_type($image["tmp_name"]);
            if ($mime_type == "image/jpeg") {
                return imagecreatefromjpeg($image["tmp_name"]);
            } elseif ($mime_type == "image/png") {
                return imagecreatefrompng($image["tmp_name"]);
            }
        }
        return null;
    }

    public function getGD() {
        return $this->gdImage;
    }

    public function getFormatedImage() {
        return $this->formatedImage;
    }
}
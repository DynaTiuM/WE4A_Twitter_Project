<?php


class Image {
    private $gdImage;
    private $formatedImage;

    /**
     * Constructeur de la classe image, transformant le paramètre entré en image GD
     *
     * @param $image
     */
    public function __construct($image) {
        $this->gdImage = $this->createImage($image);
    }

    /**
     * Formatage de l'image en résolution de largeur de 500px
     *
     * @return void
     */
    public function formatImage() {
        // Si l'image n'est pas chargée, on sort de la méthode
        if(!$this->gdImage) return;

        // On défini la nouvelle taille de l'image en largeur à 500 pixels
        $newWidth = 500;

        // Il est nécessaire de garder le ratio initial de l'image, on l'effectue grâce à un calcul mathématiques simple :
        // On calcul le ratio en prenant notre nouvelle largeur de 500px et en la divisant par l'ancienne
        // On obtient donc un nombre, qui lorsqu'on le multiplie par notre ancienne hauteur, on obtient notre nouvelle hauteur d'image, non déformée par proportionalité
        // Ici, la méthode imagesx(), méthode intégrée dans image GD, permet de retourner la largeur de notre image GD en pixels
        // Donc le ratio est :
        $ratio = $newWidth / imagesx($this->gdImage);
        // Il suffit simplement de calculer la nouvelle hauteur grâce au ratio calculé :
        $newHeight = imagesy($this->gdImage) * $ratio;

        // Ainsi, il ne nous reste plus qu'à initialiser la taille de notre nouvelle image, l'image est encore vide :
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Enfin, il faut réaliser l'opération permettant la conversion, avec :
        // $newImage : la nouvelle image de destination à créer
        // gdImage : notre image originale
        // 0, 0 : les coordonnées du point de départ dans l'image à créer
        // 0, 0 : les coordonnées du point de départ dans l'image originale
        // On ajoute les paramètres de nouvelle taille, ainsi que celles de base
        imagecopyresampled($newImage, $this->gdImage, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($this->gdImage), imagesy($this->gdImage));

        // On enregistre enfin la nouvelle image crée en format jpeg
        ob_start();
        imagejpeg($newImage);

        $this->formatedImage = ob_get_clean();
    }


    /**
     * Méthode pour créer une image GD à partir d'un fichier image téléchargé
     *
     * @param $image
     * @return GdImage|resource|null
     */
    private function createImage($image) {
        // On vérifie d'abord si l'image a bien été téléchargée
        if (isset($image) && is_uploaded_file($image["tmp_name"])) {
            // On obtient le type MIME de l'image envoyée grâce à la fonction intégrée par GD mime_content_type
            $mimeType = mime_content_type($image["tmp_name"]);
            // Un type MIME permet d'identifier le format du fichier
            // Il est composé de deux parties :
            // 1) le type (ici image)
            // 2) le sous-type (ici jpeg ou png car on souhaite seulement ces formats de fichier d'image)

            // Ainsi, si le type MIME est JPEG, on crée une image GD format jpeg
            if ($mimeType == "image/jpeg") {
                return imagecreatefromjpeg($image["tmp_name"]);
            }
            // Sinon s'il est png, on crée une image GD format png
            elseif ($mimeType == "image/png") {
                return imagecreatefrompng($image["tmp_name"]);
            }
        }

        // Sinon, on retourne et crée rien
        return null;
    }


    // Getters
    public function getGD() {
        return $this->gdImage;
    }

    public function getFormatedImage() {
        return $this->formatedImage;
    }
}
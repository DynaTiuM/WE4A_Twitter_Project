<?php

require_once ("Entity.php");


// La classe Animal est une classe qui communique directement avec la base de données pour y effectuer des opérations
class Animal extends Entity {

    // Attributs privés essentiels que possède un animal
    // Ces attributs correspondent aux colonnes de la table animaux dans la base de données
    private $name;
    private $masterUsername;
    private $age;
    private $gender;
    private $characteristics;
    private $species;
    private $adopt;

    /**
     * Constructeur prenant en paramètres une instance de mysqli() et une instance de la base de données
     *
     * @param mysqli $conn Instance de la classe mysqli
     * @param Database $db Instance de la classe Database
     */
    public function __construct($conn, $db) {
        parent::__construct($conn, $db);
    }


    // Méthodes Getters classiques :
    public function getAdoption() {
        return $this->adopt;
    }

    public function getMasterUsername() {
        return $this->masterUsername;
    }
    public function getCharacteristics() {
        return $this->characteristics;
    }
    public function getSpecies() {
        return $this->species;
    }

    public function getGender() {
        return $this->gender;
    }
    public function getAge() {
        return $this->age;
    }
    public function getName() {
        return $this->name;
    }

    /**
     * Méthode static permettant de récupérer l'instance d'un animal par rapport à son username
     *
     * @param mysqli $conn Instance de la classe mysqli
     * @param Database $db Instance de la classe Database
     * @param string $username Username de l'animal (id)
     *
     * @return Animal
     */
    public static function getInstanceById($conn, $db, $username) {

        // Nous créons une nouvelle instance de la classe animal
        $animal = new Animal($conn, $db);

        // Nous préparons la requête, qui permettra de trouver l'animal par rapport à son id (=username)
        $query = "SELECT * FROM animal WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Si un animal est trouvé
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Nous mettons à jour les paramètres de la classe animal
            $animal->username = $row['id'];
            $animal->masterUsername = $row['maitre_username'];
            $animal->characteristics = $row['caracteristiques'];
            $animal->name = $row['nom'];
            $animal->age = $row['age'];
            $animal->gender = $row['sexe'];
            $animal->species = $row['espece'];
            $animal->adopt = $row['adopter'];

            // Nous retournons ainsi l'animal trouvé
            return $animal;
        } else {
            // Sinon, si aucun animal n'est retrouvé dans le recherche SQL, nous retournons null
            return null;
        }
    }

    /**
     * Fonction permettant d'ajouter les attributs de l'animal lors de la création de son profil.
     *
     * @param int $id ID de l'animal
     * @param string $name Nom de l'animal
     * @param string $masterUsername Username du maitre de l'animal
     * @param string $age Age de l'animal
     * @param string $gender Sexe de l'animal
     * @param array $avatar Avatar de l'animal
     * @param string $characteristics Bio de l'animal
     * @param string $species Espece de l'animal
     * @param bool $adoption L'animal est à adopter
     *
     * @return string
     */
    public function setAttributes($id, $name, $masterUsername, $age, $gender, $avatar, $characteristics, $species, $adoption): string {
        global $globalUser;

        // Si l'adoption n'est pas renseignée, on la met par défaut sur false
        if (!isset($_POST['adoption'])) {
            $this->adopt = 0;
        }
        // Sinon, on ajoute les informations d'adoption en fonction de ce que l'utilisateur a coché
        else {
            $this->adopt = $this->db->secureString_ForSQL($_POST['adoption']);
        }

        // Utilisation de la classe Utilisateur pour vérifier l'unicité de l'ID
        // Cela permet d'éviter d'ajouter un nouvel animal dont l'ID est déjà existant
        if (!$globalUser->verifyUnicity($_POST['id'])) {
            // On informe l'utilisateur concernant le duplicata d'ID.
            return "Identifiant déjà existant !";
        }

        // Dans le cas où un avatar a été chargé, il est nécessaire tout d'abord de vérifier s'il est upload
        if (isset($avatar) && is_uploaded_file($avatar["tmp_name"])) {
            // Si c'est le cas,, on récupère son contenu dans une variable $image
            $image = file_get_contents($avatar["tmp_name"]);
        } else {
            // Sinon, cela signifie que l'utilisateur a choisi de ne pas mettre d'avatar, on ajoute alors un avatar par défaut à la création du profil
            $image = file_get_contents('../images/default_avatar_pet.png');
        }

        // Il ne reste plus qu'à réaliser la requête SQL permettant d'insérer toutes les valeurs dans la table animal
        $query = "INSERT INTO animal (id, nom, maitre_username, age, sexe, avatar, caracteristiques, espece, adopter) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssissssi", $id, $name, $masterUsername, $age, $gender, $image, $characteristics, $species, $adoption);
        $stmt->execute();
        $stmt->close();

        // On retourne l'information à l'utilisateur.
        return "Animal ajouté avec succès !";
    }

    /**
     * Fonction permettant de modifier le profil d'un animal.
     *
     * @param string $name Nom de l'animal
     * @param string $age Age de l'animal
     * @param string $gender Sexe de l'animal
     * @param array $avatar Avatar de l'animal
     * @param string $bio Bio de l'animal
     * @param string $species Espece de l'animal
     * @param bool $adoption L'animal est à adopter
     *
     * @return string
     */
    public function updateProfile($name, $age, $avatar, $gender, $bio, $species, $adoption = null): string {

        // On prépare tout d'abord la requête SQL dont les informations seront nécessairement réenvoyées
        $query = "UPDATE animal SET nom = ?, age = ?, sexe = ?, caracteristiques = ?, espece = ?";

        // Nous créons un tableau que nous appelons param, qui possède des attributs dans le même sens que ceux de la modification dans la requete SQL
        $params = array($name, $age, $gender, $bio, $species);
        // Pour chaque attribut, on leur attribue leur type correspondant à ceux de la base de données
        //(par exemple ici s pour name car il s'agit d'un string, et i pour l'age car il s'agit d'un nombre)
        $types = "sisss";

        // Dans le cas où un avatar est upload lors de la modification du profil,
        if (isset($avatar) && is_uploaded_file($avatar['tmp_name'])) {
            // Nous importons la classe Image, qui va nous aider à créer notre image pour la base de données
            require_once ("../Classes/Image.php");
            // A partir de la variable $avatar, nous créons une instanciation de la classe Image, qui prend en paramètres $avatar
            $avatar = new Image($avatar);
            // Par l'intermédiaire de la classe Image et de son constructeur, un attribut gdImage est stocké dans la classe avatar
            // Grâce à cette gdImage, nous allons pouvoir la reformater avant de l'insérer dans la base de données
            // Cela permet ainsi de réduire sa taille si elle est trop grande, pour ne pas envoyer une image trop lourde dans la base de données
            $avatar->formatImage();
            // Ainsi, nous concaténons la requete de base en ajoutant avatar = ?, car ici une nouvelle image a été upload
            $query .= ", avatar = ?";
            // Et nous ajoutons la nouvelle image formatée dans les paramètres de la requete.
            $params[] = $avatar->getFormatedImage();
            // Son type étant évidemment un string
            $types .= "s";
        }

        // De même, dans le cas où l'adoption n'est pas nulle, nous mettons à jour dans la base de données en concaténant la requete
        if ($adoption !== null) {
            $query .= ", adopter = ?";
            $params[] = $adoption;
            $types .= "s";
        }

        // Il est important de terminer la requete en ajoutant la condition WHERE
        $query .= " WHERE id = ?";
        $params[] = $this->username;
        $types .= "s";

        // Finalement, notre requete SQL peut prendre différentes formes de concaténation en fonction de ce qui a été mis à jour
        // Il ne reste plus qu'à préparer la requete
        $stmt = $this->conn->prepare($query);

        // Et de lier les paramètres avec le tableau de types et leur paramètres correspondants
        // Ici, nous utilisons ... devant $param pour décompresser notre tableau params
        // En effet, la méthode bind_param() attend des éléments séparés, et non un tableau en paramètres
        // ...$param est donc équivalent à $name, $age, $gender, $bio, $species, etc en fonction de la requete
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        // Dans le cas où la requete a affecté des lignes, nous mettons à jour les attributs de la classe animal
        if ($stmt->affected_rows > 0) {
            $this->name = $name;
            $this->age = $age;
            $this->gender = $gender;
            $this->characteristics = $bio;
            $this->species = $species;
            if ($this->adopt != null) $this->adopt = $adoption;

            // Nous retournons l'information à l'utilisateur
            return "Profil modifié avec succès !";
        }

        // Sinon, nous informons l'utilisateur qu'il y a eu un problème lors de la modification du profil
        return "Erreur lors de la modification du profil !";
    }


    /**
     *
     * Méthode permettant de déterminer tous les messages du profil d'un animal par requête SQL
     *
     * @param bool $isMessage
     * @return string
     */
    public function queryMessagesAndAnswers($isMessage = true) : string {
        // On sélectionne toutes les informations du message (*) de la table message
        // On lie cette table avec la table message_animaux, la liaison s'effectue en fonction de l'id du message
        // Puis enfin on lie la table animal avec la table message_animaux par l'id de l'animal
        // La condition ici est l'id de l'animal
        // Ceci permet ainsi de récupérer tous les messages en fonction de l'id d'un animal
        return "SELECT message.*
                FROM message
                    JOIN message_animaux
                        ON message.id = message_animaux.message_id
                    JOIN animal
                        ON animal.id = message_animaux.animal_id
                WHERE animal.id = ?";
    }


    /**
     * Fonction permettant de récupérer l'avatar d'un animal à partir de la base de données
     *
     * @return string
     */
    public function loadAvatar() : string {
        $sql = "SELECT avatar FROM animal WHERE id = ?";
        return $this->selectSQLAvatar($sql);
    }

    /**
     * Méthode permettant de compter tous les messages d'un animal
     *
     * @return mixed
     */
    public function countAllMessages() {
        // Cette requete SQL récupère tout simplement le nombre de messages d'un animal en fonction de son ID
        $query = "SELECT COUNT(*) FROM message_animaux WHERE animal_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Cette méthode renvoie ainsi le nombre de messages
        return $result->fetch_column();
    }
}
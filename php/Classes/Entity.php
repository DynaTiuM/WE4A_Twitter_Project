<?php

abstract class Entity {

    protected $username;
    protected $conn;
    protected $db;
    protected $avatar;

    /**
     * Constructeur prenant en paramètres une instance de mysqli() et une instance de la base de données
     *
     * @param mysqli $conn Instance de la classe mysqli
     * @param Database $db Instance de la classe Database
     */
    public function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    /**
     * Méthode commune aux classes Animal et User qui retourne le nombre d'abonnés d'un animal/utilisateur
     *
     * @param $type
     * @return mixed
     */
    function numFollowers($type) {
        $query = "SELECT COUNT(*) FROM suivre WHERE suivi_id_$type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_column();
    }

    //Setter & Getter
    public function setUsername($username) {
        $this->username = $username;
    }
    public function getUsername() {
        return $this->username;
    }

    /**
     * Méthode commune aux classes Animal & User qui retourne l'avatar de l'animal/utilisateur
     *
     * @param $query
     * @return mixed|string
     */
    protected function selectSQLAvatar($query) {
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Si le nombre de lignes récupérées est supérieur à 0, ça signifie qu'un utilisateur/animal a été trouvé
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // On retourne donc l'avatar
            return $row["avatar"];
        }

        // Sinon on return qu'aucune image n'a été trouvée
        return "Aucune image trouvée.";
    }

    /**
     * Méthode qui retourne un avatar en base64 pour permettre l'affichage sur le site
     *
     * @return string
     */
    public function getAvatarEncoded64() : string {
        return base64_encode($this->loadAvatar());
    }

    public function getAvatar() {
        return $this->loadAvatar();
    }

    /**
     * Méthode permettant, lorsqu'un utilisateur souhaite suivre autre utilisateur/animal, de s'abonner ou de se désabonner de ce dernier
     *
     * @param $toFollow
     * @param $type
     * @return null
     */
    public function followUnfollow($toFollow, $type) {
        require_once ("../Classes/Notification.php");
        // On détermine le type de suivi grâce à la variable $type
        // Si le type n'est pas utilisateur, il est forcément animal
        if ($type != 'utilisateur') $type = 'animal';

        // On prépare la requete SQL pour savoir si l'utilisateur en question suit déjà l'utilisateur qu'il souhaite suivre
        // Ici, utilisateur_username est l'utilisateur qui veut suivre un animal ou utilisateur
        // Et suivi_id_$type est une concaténation de suivi_id_ et $type qui permet de vérifier le suivi soit en fonction
        // d'un animal, soit d'un utilisateur
        $stmt = $this->conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
        $stmt->bind_param("ss", $this->username, $toFollow);
        $stmt->execute();
        $result = $stmt->get_result();

        // S'il y a un résultat, cela signifie qu'il est déjà abonné, il faut donc le désabonner (car lorsqu'on clique sur le bouton
        // d'abonnement une deuxieme fois, ça nous désabonne
        if ($result->num_rows > 0) {
            // On récupère la colonne de l'id avec le raccourci fetch_assoc()['id'] (cela nous permet d'éviter de réaliser une opération
            // intermédiaire comme $row = $result->fetch_assoc()['id'] puis $followId = $row['id']
            $followId = $result->fetch_assoc()['id'];

            // Nous créons une notification de suppression de notification
            $notification = new Notification($this->conn, $this->db);
            // On supprime ainsi la notification de suivi par rapport à l'id de suivi
            $notification->deleteFollowNotifications($followId);

            // Une fois que la notification est supprimée, il est important de supprimer également le suivi de l'utilisateur
            $stmt = $this->conn->prepare("DELETE FROM suivre WHERE utilisateur_username = ? AND suivi_id_$type = ?");
            $stmt->bind_param("ss", $this->username, $toFollow);
            $stmt->execute();
            return;
        }

        // Dans le cas où l'utilisateur n'est pas encore abonné, il faut donc ajouter un nouveau suivi
        $stmt = $this->conn->prepare("INSERT INTO suivre (utilisateur_username, suivi_type, suivi_id_$type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->username, $type, $toFollow);
        $stmt->execute();

        // Il est nécessaire par la suite de récupérer l'id nouvellement inseré, cela est possible grâce à l'attribut insert_id
        $followId = $stmt->insert_id;

        // Il ne reste plus qu'à créer une nouvelle notification
        $notification = new Notification($this->conn, $this->db);
        // En y précisant le nom de l'utilisateur suiveur, l'utilisateur/animal à suivre, et l'id de suivi
        $notification->createNotificationForFollow($this->username, $toFollow, $followId);
    }

    /**
     * Méthode qui permet de vérifier si un utilisateur suit déjà un animal ou utilisateur
     *
     * @param $to_follow
     * @param $type
     * @return bool
     */
    public function checkFollow($to_follow, $type): bool {
        // Cette requete permet de vérifier s'il existe une ligne dans la base de données où l'utilisateur suit déjà un profil spécifique
        // Il y a donc 3 critères à prendre en compte : l'utilisateur suiveur, le type de suivi animal ou utilisateur et le username/id de l'utilisateur à suivre
        $stmt = $this->conn->prepare("SELECT * FROM suivre WHERE utilisateur_username = ? AND suivi_type = ? AND suivi_id_$type = ?");
        $stmt->bind_param("sss", $this->username, $type, $to_follow);
        $stmt->execute();
        $result = $stmt->get_result();

        // S'il y a un résultat, c'est que l'utilisateur qui visite le profil suit déjà l'utilisateur du profil
        if ($result && $result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Méthode qui permet de vérifier l'unicité de username entre les utilisateurs, entre les animaux et entre les animaux/utilisateurs
     * Retourne True si l'utilisateur est unique, False sinon
     *
     * @param $parameter
     * @return bool
     */
    public function verifyUnicity($username) {
        // La première partie de la requete SELECT username FROM utilisateur WHERE username = ?
        // -> Sélectionne tous les enregistrements de la table utilisateur où le nom d'utilisateur est égal à celui entré en paramètres
        // Il en va de même pour SELECT id FROM animal WHERE id = ? du côté des animaux
        // UNION permet de combiner le résultat des deux requetes, pour récupérer l'ensemble des noms d'utilisateurs qui peuvent avoir le meme nom que celui entré en paramètres
        // (logiquement il devrait y en avoir qu'un seul à chaque fois étant donné qu'il y a forcément unicité)
        $query = "(SELECT username FROM utilisateur WHERE username = ?) UNION (SELECT id FROM animal WHERE id = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Finalement, si le nombre de lignes retourné est supérieur à 0, cela signifie forcément qu'il y a déjà un utilisateur/animal possédant ce nom d'utilisateur
        if($result->num_rows > 0) {
            return false;
        }

        // Sinon, on retourne true, l'unicité est vérifiée !
        return true;
    }
}

<?php

/**
 * Classe Abstraite étant donné qu'un profil est soit un profil animal, soit un profil utilisateur
 * Cependant, ces deux types de profils peuvent posséder des similarités
 * C'est pourquoi les classes AnimalProfile Et UserProfile héritent toutes deux de la classe Profile
 */
abstract class Profile {
    protected $username;
    protected $conn;
    protected $db;
    protected int $numberOfMessages;
    protected $profileUser;

    /**
     * Constructeur classique de la classe Profile
     *
     * @param $conn
     * @param $username
     * @param $db
     */
    public function __construct($conn, $username, $db) {
        $this->conn = $conn;
        $this->username = $username;
        $this->db = $db;
    }

    /**
     * Méthode permettant d'afficher le nombre de messages de l'utilisateur/animal sur le profil
     *
     * @return void
     */
    public function displayNumMessages() : void {
        ?>
        <div style = "margin-left: 1vw; font-family: 'Plus Jakarta Sans', sans-serif;">
            <p style = "margin-top: 0; padding-top: 0; font-size: 0.9vw;"><?php echo $this->numberOfMessages?> Messages</p>
        </div>
        <?php
    }

    /**
     * Méthode permettant de déterminer le type de profil. Etant donné qu'un Profile est abstrait, la détermination s'effectue par méthode Static
     *
     * @param $conn
     * @param $username
     * @return string
     */
    public static function determineProfileType($conn, $username) {
        $count = 0;
        // Cette requete SQL vérifie si le username appartient à un utilisateur
        $query = "SELECT COUNT(*) FROM utilisateur WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $count = $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // Si count est supérieur à 0, cela signifie qu'il existe un utilisateur possédant ce username
        if ($count > 0) {
            return 'utilisateur';
        }
        // Sinon, c'est qu'il s'agit nécessairement d'un animal
        else {
            return 'animal';
        }
    }

    /**
     * Méthode permettant de connaitre les messages aimés par l'utilisateur
     *
     * @return array|void
     */
    public function likedMessages() {
        // La requête SQL est simple :
        // On récupère toutes les informations des messages qui ont été aimé (car on a besoin du plus d'informations possible pour afficher
        // Les messages par la suite
        // Une jointure s'effectue avec la table like_message pour connaitre l'utilisateur qui a liké le message
        // Si l'utilisateur concorde avec l'utilisateur du profil, alors la requete SQL récupère la ligne et ainsi de suite
        $query =    "SELECT message.* FROM message
                        JOIN like_message ON message.id = like_message.message_id
                    WHERE like_message.utilisateur_username = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Ainsi, pour stocker tous les messages likés, on initialise d'abord un tableau vide
        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Et pour chaque ligne qui a été sélectionnée, on ajoute l'id du message dans le tableau
                $messageIds[] = $row['id'];
            }
            // On retourne finalement le tableau d'ids de message
            return $messageIds;
        } else {
            // Sinon, on informe tout simplement que l'utilisateur n'a aimé aucun message
            echo '<br><h4>Ce profil n\'a aimé aucun message</h4>';
        }
    }

    //Setter
    public function setNumberOfMessages($number) {
        $this->numberOfMessages = $number;
    }

    //Getter
    public function getUser() {
        return $this->profileUser;
    }

    /**
     * Méthode permettant de récupérer tous les messages et réponses d'un utilisateur/animal
     *
     * @param $isMessage
     * @return array|void
     */
    public function profileMessagesAndAnswers($isMessage) {
        // La requete SQL est réalisée dans la classe adéquate soit de l'animal, soit de l'utilisateur :
        $query = $this->getUser()->queryMessagesAndAnswers($isMessage);
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        // Il ne reste plus qu'à exécuter la requete
        $stmt->execute();
        $result = $stmt->get_result();

        // Le procédé reste toujours le meme que la section likes, avec l'initialisation d'un tableau et son remplissage en fonction des ids des messages
        $messageIds = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageIds[] = $row['id'];
            }
            return $messageIds;
        }
        else {
            // Il est important de scinder les deux parties du profil : d'un coté s'il s'agit de la section message, d'afficher qu'il n'y a aucun message,
            if ($isMessage) {
                echo '<br><h4>Ce profil ne contient aucun message</h4>';
            }
            // D'un autre coté, s'il s'agit de la section réponse, d'afficher qu'il n'y a pas de réponses.
            else {
                echo '<br><h4>Ce profil n\'a répondu à aucun message</h4>';
            }
        }
    }


    /**
     * Méthode permettant d'afficher le profil d'un animal ou d'un utilisateur
     *
     * @return mixed
     */
    abstract public function displayProfile(); // Méthode abstraite à implémenter dans les classes filles

    /**
     * Méthode permettant d'afficher les catégories de messages du profil
     *
     * @param $loginStatus
     * @param $globalUser
     * @return mixed
     */
    abstract protected function displayButton($loginStatus, $globalUser); // Méthode abstraite à implémenter dans les classes filles
}
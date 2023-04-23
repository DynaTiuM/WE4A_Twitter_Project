<?php

// Classe permettant la liaison avec la base de données, l'établissement à la connexion et la sécurisation des strings
class Database {
    private $conn;
    private static $instance;


    /**
     * Constructeur permettant d'initaliser la base de données
     *
     */
    public function __construct() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "we4a_project";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    /**
     * Méthode respectant le modèle singleton : Dans le cas où aucune instance de la base de données n'existe, on en crée une nouvelle
     * Sinon, on récupère seulement l'instance déjà existante
     * Il s'agit ici d'un modèle cohérent étant donné qu'établissement de la connexion avec la base de données doit être unique
     *
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Méthode simple permettant de sécuriser un minimum les chaines de caractères qui sont envoyées à la base de données
     */
    public function secureString_ForSQL($string): string {
        $string = trim($string);
        $string = stripcslashes($string);
        $string = addslashes($string);
        return htmlspecialchars($string);
    }

    /**
     * Méthode permettant de récupérer la connexion à la base de données
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Méthode permettant de fermer la connexion à la base de données
     */
    public function close() {
        $this->conn->close();
    }
}
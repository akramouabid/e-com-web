<?php
/**
 * Classe Database - Gestion de la connexion MySQL
 * Utilise PDO pour les requêtes sécurisées (prévention SQL injection)
 */

class Database {
    // Paramètres de connexion
    private $host = 'localhost';
    private $db_name = 'ecom_bookstore';
    private $username = 'root';
    private $password = '';//ddl
    private $charset = 'utf8mb4';
    private $pdo;
    /**
     * Établit la connexion à la base de données
     */
    public function connect() {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
        
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            // Mode erreur
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Récupération sous forme de tableau associatif
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $this->pdo;
        } catch (PDOException $e) {
            die("Erreur de connexion: " . $e->getMessage());
        }
    }
    
    /**
     * Retourne l'instance PDO
     */
    public function getPDO() {
        return $this->pdo;
    }
}
?>

<?php
/**
 * Classe Auth - Gestion de l'authentification et sessions
 * Responsable : login, register, logout, vérification rôle
 */

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Enregistrement d'un nouvel utilisateur
     * @param string $username
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string]
     */
    public function register($username, $email, $password) {
        // Validation basique
        if (strlen($username) < 3) {
            return ['success' => false, 'message' => 'Nom d\'utilisateur trop court (min 3 caractères)'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Mot de passe trop court (min 6 caractères)'];
        }
        
        // Vérifier si user existe déjà
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Nom d\'utilisateur ou email déjà utilisé'];
        }
        
        // Créer le compte
        $password_hash = $password;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password_hash, role)
                VALUES (?, ?, ?, 'user')
            ");
            $stmt->execute([$username, $email, $password_hash]);
            
            return ['success' => true, 'message' => 'Inscription réussie!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
        }
    }
    
    /**
     * Connexion utilisateur
     * @param string $username
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => array]
     */
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT id, username, email, role, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        }
        
        if (!($password== $user['password_hash'])) {
            return ['success' => false, 'message' => 'Mot de passe incorrect'];
        }
        
        // Créer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        return [
            'success' => true,
            'message' => 'Connexion réussie!',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }
    
    /**
     * Déconnexion utilisateur
     */
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Déconnecté'];
    }
    
    /**
     * Vérifier si utilisateur est connecté
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Vérifier si utilisateur est admin
     */
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Obtenir les infos de l'utilisateur connecté
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ];
    }
}
?>

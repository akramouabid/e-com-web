<?php
/**
 * Classe User - Gestion des données utilisateur
 */

class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Récupérer un utilisateur par ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Récupérer tous les utilisateurs (admin)
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Mettre à jour le rôle d'un utilisateur
     */
    public function updateRole($user_id, $role) {
        if (!in_array($role, ['user', 'admin'])) {
            return false;
        }
        
        $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $user_id]);
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function delete($user_id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$user_id]);
    }
    public function count(): int {
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    return (int) $stmt->fetchColumn();
    }
}
?>

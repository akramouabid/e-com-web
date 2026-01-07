<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Cart.php';

try {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => true,
            'count' => 0
        ]);
        exit;
    }

    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    
    $user_id = $_SESSION['user_id'];
    $cart = new Cart($pdo, $user_id);
    
    // Obtenir le nombre d'articles
    $count = $cart->getItemCount();
    
    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
    
} catch (Exception $e) {
    error_log("Erreur get-cart-count: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'count' => 0,
        'message' => 'Erreur serveur'
    ]);
}
?>
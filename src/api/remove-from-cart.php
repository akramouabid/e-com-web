<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Cart.php';

try {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Vous devez être connecté'
        ]);
        exit;
    }

    // Vérifier les données POST
    if (!isset($_POST['cart_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Données manquantes'
        ]);
        exit;
    }

    $cart_id = intval($_POST['cart_id']);

    // Validation
    if ($cart_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID invalide'
        ]);
        exit;
    }

    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    
    $user_id = $_SESSION['user_id'];
    $cart = new Cart($pdo, $user_id);
    
    // Supprimer l'article
    $result = $cart->removeItem($cart_id);
    
    // Retourner le résultat
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Erreur remove-from-cart: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
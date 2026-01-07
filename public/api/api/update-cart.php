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
    if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Données manquantes'
        ]);
        exit;
    }

    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    // Validation
    if ($cart_id <= 0 || $quantity < 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Données invalides'
        ]);
        exit;
    }

    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    
    $user_id = $_SESSION['user_id'];
    $cart = new Cart($pdo, $user_id);
    
    // Mettre à jour la quantité
    $result = $cart->updateQuantity($cart_id, $quantity);
    
    // Retourner le résultat
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Erreur update-cart: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
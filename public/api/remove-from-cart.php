<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Cart.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié']);
        exit;
    }

    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;

    if ($cart_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
        exit;
    }

    $db = new Database();
    $pdo = $db->connect();

    $cart = new Cart($pdo, $_SESSION['user_id']);
    $result = $cart->removeItem($cart_id);

    echo json_encode($result);
    exit;

} catch (Exception $e) {
    error_log('Erreur remove-from-cart: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
    exit;
}

?>

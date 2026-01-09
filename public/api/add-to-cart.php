<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Cart.php';

try {
    // Vérifier méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit;
    }

    // Vérifier utilisateur connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Récupérer et valider les paramètres
    $book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if ($book_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
        exit;
    }

    // Connexion DB
    $db = new Database();
    $pdo = $db->connect();

    $cart = new Cart($pdo, $user_id);
    $result = $cart->addItem($book_id, $quantity);

    echo json_encode($result);
    exit;

} catch (Exception $e) {
    error_log('Erreur add-to-cart: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
    exit;
}

?>

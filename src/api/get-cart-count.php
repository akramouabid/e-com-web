<?php
header('Content-Type: application/json');
session_start();

require_once realpath(__DIR__ . '/../../config/Database.php');
require_once realpath(__DIR__ . '/../../classes/Auth.php');
require_once realpath(__DIR__ . '/../../classes/Cart.php');

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

// Si pas connecté, retourner 0
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => true, 'count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart($pdo, $user_id);
$count = $cart->getItemCount();

echo json_encode(['success' => true, 'count' => $count]);
?>

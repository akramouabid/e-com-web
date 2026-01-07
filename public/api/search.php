<?php
session_start();

// Définir le type de contenu JSON
header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage (à retirer en production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Auth.php';
require_once __DIR__ . '/../../src/classes/Book.php';

// Log the request
error_log("🔍 [SEARCH API] Request received - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("🔍 [SEARCH API] GET params: " . json_encode($_GET));

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

// Vérifier si connecté
if (!$auth->isLoggedIn()) {
    error_log("❌ [SEARCH API] User not logged in");
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

error_log("✅ [SEARCH API] User logged in: " . $_SESSION['username']);

$keyword = htmlspecialchars($_POST['keyword'] ?? $_GET['keyword'] ?? '');
$category_id = isset($_POST['category_id']) || isset($_GET['category_id']) ? intval($_POST['category_id'] ?? $_GET['category_id']) : null;
$min_price = isset($_POST['min_price']) || isset($_GET['min_price']) ? floatval($_POST['min_price'] ?? $_GET['min_price']) : null;
$max_price = isset($_POST['max_price']) || isset($_GET['max_price']) ? floatval($_POST['max_price'] ?? $_GET['max_price']) : null;

error_log("🔍 [SEARCH API] Search params - keyword: '$keyword', category: $category_id, price: $min_price-$max_price");

$book = new Book($pdo);
$results = $book->search($keyword, $category_id, $min_price, $max_price);

error_log("✅ [SEARCH API] Found " . count($results) . " results");

echo json_encode([
    'success' => true,
    'results' => $results,
    'count' => count($results)
]);
?>

<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Book.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

// Vérifier si admin
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

$book_id = intval($_POST['book_id'] ?? 0);

if ($book_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$book = new Book($pdo);
if ($book->delete($book_id)) {
    echo json_encode(['success' => true, 'message' => 'Livre supprimé']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
}
?>

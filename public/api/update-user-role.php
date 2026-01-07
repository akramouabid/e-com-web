<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Auth.php';
require_once __DIR__ . '/../../src/classes/User.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

// Vérifier si admin
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

$user_id = intval($_POST['user_id'] ?? 0);
$role = htmlspecialchars($_POST['role'] ?? '');

if ($user_id <= 0 || !in_array($role, ['user', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$user = new User($pdo);
if ($user->updateRole($user_id, $role)) {
    echo json_encode(['success' => true, 'message' => 'Rôle mis à jour']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}
?>

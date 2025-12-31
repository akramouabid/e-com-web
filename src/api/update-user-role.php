<?php
header('Content-Type: application/json');
session_start();

require_once realpath(__DIR__ . '/../config/Database.php');
require_once realpath(__DIR__ . '/../classes/Auth.php');
require_once realpath(__DIR__ . '/../classes/User.php');

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);
$userClass = new User($pdo);

if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

$user_id = $_POST['user_id'];
$role = $_POST['role'];

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Error getting user id']);
    exit;
}

if (!$role) {
    echo json_encode(['success' => false, 'message' => 'Error getting role']);
    exit;
}

if ($user_id < 0 || !in_array($role, ['admin', 'user'])) {
    echo json_encode(['success' => false, 'message' => 'Error Invalid data']);
    exit;
}

if ($userClass->updateRole($user_id, $role)) {
    echo json_encode(['success' => true, 'message' => 'Success yay']);
    exit;
}
else {
    echo json_encode(['success' => false, 'message' => 'An error occured when updating roles']);
    exit;
}
?>
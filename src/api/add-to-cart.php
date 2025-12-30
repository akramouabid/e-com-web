<?php
session_start();

// Définir le type de contenu JSON
header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage (à retirer en production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Cart.php';

try {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Vous devez être connecté pour ajouter au panier'
        ]);
        exit;
    }

    // Vérifier les données POST
    if (!isset($_POST['book_id']) || !isset($_POST['quantity'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Données manquantes (book_id ou quantity)'
        ]);
        exit;
    }

    $book_id = intval($_POST['book_id']);
    $quantity = intval($_POST['quantity']);

    // Validation
    if ($book_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID du livre invalide'
        ]);
        exit;
    }

    if ($quantity <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Quantité invalide'
        ]);
        exit;
    }

    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    
    $user_id = $_SESSION['user_id'];
    $cart = new Cart($pdo, $user_id);
    
    // Ajouter au panier - LA MÉTHODE RETOURNE UN TABLEAU !
    $result = $cart->addItem($book_id, $quantity);
    
    // Retourner directement le résultat
    echo json_encode($result);
    
} catch (Exception $e) {
    // Logger l'erreur
    error_log("Erreur add-to-cart: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
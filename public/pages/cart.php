<?php
session_start();

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Auth.php';
require_once __DIR__ . '/../../src/classes/Cart.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

// Vérifier si connecté
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart($pdo, $user_id);
$cart_items = $cart->getItems();
$cart_total = $cart->getTotal();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - LibreBooks</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="../index.php" class="logo"><img src="../../database/img/logo.png" id="logo-img" >LibreBooks</a>
            </div>
            
            <div class="nav-menu">
                <a href="../index.php" class="nav-link">Accueil</a>
                <a href="cart.php" class="nav-link cart-link active">
                    🛒 Panier <span id="cart-count" class="cart-badge"><?php echo count($cart_items); ?></span>
                </a>
                <a href="login.php?action=logout" class="nav-link logout">Déconnexion</a>
            </div>
        </div><img src="" alt="">
    </nav>

    <!-- Panier -->
    <main class="container">
        <section class="cart-section">
            <h1>Mon panier</h1>
            
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Votre panier est vide</p>
                    <a href="../index.php" class="btn btn-primary">Continuer vos achats</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Livre</th>
                                    <th>Prix</th>
                                    <th>Quantité</th>
                                    <th>Sous-total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr class="cart-item" data-cart-id="<?php echo $item['id']; ?>">
                                        <td>
                                            <div class="cart-item-info">
                                                <img src="<?php echo "../../database/img/".$item['cover_image'] ?? '../assets/images/no-cover.jpg'; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['title']); ?>" class="cart-item-image">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                                    <p><?php echo htmlspecialchars($item['author']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="item-price"><?php echo number_format($item['price'], 2); ?> €</td>
                                        <td>
                                            <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" data-cart-id="<?php echo $item['id']; ?>">
                                        </td>
                                        <td class="item-subtotal">
                                            <?php echo number_format($item['price'] * $item['quantity'], 2); ?> €
                                        </td>
                                        <td>
                                            <button class="btn btn-danger remove-item" data-cart-id="<?php echo $item['id']; ?>">
                                                Supprimer
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="cart-summary">
                        <h3>Résumé</h3>
                        <div class="summary-line total">
                            <span><strong>Total:</strong></span>
                            <span><strong><?php echo number_format($cart_total, 2); ?> €</strong></span>
                        </div>
                        
                        <button class="btn btn-primary btn-block btn-large">Procéder au paiement</button>
                        <a href="../index.php" class="btn btn-secondary btn-block">Continuer vos achats</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 LibreBooks. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="../assets/js/cart.js"></script>
    <script>
        // Mettre à jour la quantité
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const cartId = this.getAttribute('data-cart-id');
                const quantity = this.value;
                
                // CORRECTION DU CHEMIN
                fetch('../api/update-cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `cart_id=${cartId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la mise à jour');
                });
            });
        });
        
        // Supprimer un article
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const cartId = this.getAttribute('data-cart-id');
                
                if (confirm('Êtes-vous sûr?')) {
                    // CORRECTION DU CHEMIN
                    fetch('../api/remove-from-cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `cart_id=${cartId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la suppression');
                    });
                }
            });
        });
    </script>
</body>
</html>
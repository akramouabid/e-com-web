<?php
session_start();

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Auth.php';
require_once __DIR__ . '/../../src/classes/Book.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);
$book = new Book($pdo);

// Vérifier si connecté
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$book_detail = $book->getById($book_id);

if (!$book_detail) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book_detail['title']); ?> - LibreBooks</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="../index.php" class="logo">📚 LibreBooks</a>
            </div>
            
            <div class="nav-menu">
                <a href="../index.php" class="nav-link">Accueil</a>
                <a href="cart.php" class="nav-link cart-link">
                    🛒 Panier <span id="cart-count" class="cart-badge">0</span>
                </a>
                <a href="login.php?action=logout" class="nav-link logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Détail du livre -->
    <main class="container">
        <section class="book-detail-section">
            <div class="breadcrumb">
                <a href="../index.php">Accueil</a> > <span><?php echo htmlspecialchars($book_detail['title']); ?></span>
            </div>
            
            <div class="book-detail">
                <div class="book-detail-image">
                    <img src="<?php echo $book_detail['cover_image'] ?? '../assets/images/no-cover.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($book_detail['title']); ?>">
                </div>
                
                <div class="book-detail-info">
                    <h1><?php echo htmlspecialchars($book_detail['title']); ?></h1>
                    
                    <p class="book-author">
                        <strong>Auteur:</strong> <?php echo htmlspecialchars($book_detail['author']); ?>
                    </p>
                    
                    <p class="book-category">
                        <strong>Catégorie:</strong> <?php echo htmlspecialchars($book_detail['category_name']); ?>
                    </p>
                    
                    <?php if ($book_detail['isbn']): ?>
                        <p class="book-isbn">
                            <strong>ISBN:</strong> <?php echo htmlspecialchars($book_detail['isbn']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($book_detail['publisher']): ?>
                        <p class="book-publisher">
                            <strong>Éditeur:</strong> <?php echo htmlspecialchars($book_detail['publisher']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($book_detail['publication_year']): ?>
                        <p class="book-year">
                            <strong>Année:</strong> <?php echo htmlspecialchars($book_detail['publication_year']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($book_detail['pages']): ?>
                        <p class="book-pages">
                            <strong>Pages:</strong> <?php echo htmlspecialchars($book_detail['pages']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="book-price-section">
                        <div class="book-price-large"><?php echo number_format($book_detail['price'], 2); ?> €</div>
                        <div class="book-stock <?php echo $book_detail['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                            <?php echo $book_detail['stock'] > 0 ? 'En stock' : 'Rupture de stock'; ?>
                        </div>
                    </div>
                    
                    <div class="book-actions">
                        <form id="add-to-cart-form" class="add-to-cart-form">
                            <input type="hidden" name="book_id" value="<?php echo $book_detail['id']; ?>">
                            
                            <div class="quantity-input">
                                <label for="quantity">Quantité:</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                       max="<?php echo $book_detail['stock']; ?>" class="form-control">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-large" 
                                    <?php echo $book_detail['stock'] === 0 ? 'disabled' : ''; ?>>
                                Ajouter au panier
                            </button>
                        </form>
                    </div>
                    
                    <div id="form-message" class="form-message"></div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="book-description">
                <h2>Description</h2>
                <p><?php echo htmlspecialchars($book_detail['description']); ?></p>
            </div>
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
        document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../src/api/add-to-cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('form-message');
                if (data.success) {
                    messageDiv.className = 'form-message success';
                    messageDiv.textContent = data.message;
                    updateCartCount();
                    // Attendre 2s avant de réinitialiser
                    setTimeout(() => messageDiv.textContent = '', 2000);
                } else {
                    messageDiv.className = 'form-message error';
                    messageDiv.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('form-message').textContent = 'Erreur lors de l\'ajout';
            });
        });
    </script>
</body>
</html>

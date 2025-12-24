<?php
session_start();

// Inclure la configuration
require_once __DIR__ . '/../src/config/Database.php';
require_once __DIR__ . '/../src/classes/Auth.php';
require_once __DIR__ . '/../src/classes/Book.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);
$book = new Book($pdo);

// Récupérer tous les livres
$books = $book->getAll(12, 0);
$categories = $book->getCategories();
$total_books = $book->count();

// Calculer le nombre de pages
$items_per_page = 12;
$total_pages = ceil($total_books / $items_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;
$books = $book->getAll($items_per_page, $offset);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibreBooks - Boutique de Livres en Ligne</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php" class="logo">📚 LibreBooks</a>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Accueil</a>
                <a href="pages/shop.php" class="nav-link">Boutique</a>
                
                <?php if ($auth->isLoggedIn()): ?>
                    <span class="nav-link">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    
                    <?php if ($auth->isAdmin()): ?>
                        <a href="pages/admin/dashboard.php" class="nav-link admin-link">Admin</a>
                    <?php endif; ?>
                    
                    <a href="pages/cart.php" class="nav-link cart-link">
                        🛒 Panier <span id="cart-count" class="cart-badge">0</span>
                    </a>
                    <a href="pages/login.php?action=logout" class="nav-link logout">Déconnexion</a>
                <?php else: ?>
                    <a href="pages/login.php" class="nav-link">Connexion</a>
                    <a href="pages/register.php" class="nav-link register">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Section Héros -->
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue dans notre librairie en ligne</h1>
            <p>Découvrez des milliers de livres de tous genres</p>
        </div>
    </section>

    <!-- Contenu principal -->
    <main class="container">
        <section class="shop-section">
            <div class="sidebar">
                <h3>Filtrer</h3>
                
                <!-- Recherche -->
                <div class="filter-group">
                    <label>Recherche</label>
                    <input type="text" id="search-input" placeholder="Titre, auteur..." class="filter-input">
                </div>
                
                <!-- Catégories -->
                <div class="filter-group">
                    <label>Catégorie</label>
                    <select id="category-filter" class="filter-input">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Prix -->
                <div class="filter-group">
                    <label>Prix min</label>
                    <input type="number" id="price-min" placeholder="0" class="filter-input" min="0">
                    
                    <label>Prix max</label>
                    <input type="number" id="price-max" placeholder="100" class="filter-input" min="0">
                </div>
                
                <button id="apply-filters" class="btn btn-primary">Appliquer filtres</button>
                <button id="reset-filters" class="btn btn-secondary">Réinitialiser</button>
            </div>
            
            <div class="shop-content">
                <h2>Nos livres</h2>
                
                <div id="books-container" class="books-grid">
                    <?php if (empty($books)): ?>
                        <p class="no-results">Aucun livre trouvé</p>
                    <?php else: ?>
                        <?php foreach ($books as $b): ?>
                            <div class="book-card">
                                <div class="book-image">
                                    <img src="<?php echo $b['cover_image'] ?? '/assets/images/no-cover.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($b['title']); ?>">
                                </div>
                                <div class="book-info">
                                    <h3><?php echo htmlspecialchars($b['title']); ?></h3>
                                    <p class="author">par <?php echo htmlspecialchars($b['author']); ?></p>
                                    <p class="category">
                                        <small><?php echo htmlspecialchars($b['category_name']); ?></small>
                                    </p>
                                    <div class="book-price"><?php echo number_format($b['price'], 2); ?> €</div>
                                    <a href="pages/book-detail.php?id=<?php echo $b['id']; ?>" class="btn btn-info">
                                        Voir détails 
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($current_page > 1): ?>
                            <a href="?page=1" class="page-link">« Première</a>
                            <a href="?page=<?php echo $current_page - 1; ?>" class="page-link">« Précédente</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="page-link <?php echo $i === $current_page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?php echo $current_page + 1; ?>" class="page-link">Suivante »</a>
                            <a href="?page=<?php echo $total_pages; ?>" class="page-link">Dernière »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 LibreBooks. Tous droits réservés.</p>
            <p>Projet académique de développement Web e-commerce</p>
        </div>
    </footer>

    <script src="assets/js/ajax.js"></script>
    <script src="assets/js/cart.js"></script>
    <script>
        // Initialiser les filtres
        document.getElementById('apply-filters').addEventListener('click', applyFilters);
        document.getElementById('reset-filters').addEventListener('click', resetFilters);
        document.getElementById('search-input').addEventListener('keyup', debounce(applyFilters, 500));
    </script>
</body>
</html>

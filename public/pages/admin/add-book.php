<?php
session_start();

require_once __DIR__ . '/../../../src/config/Database.php';
require_once __DIR__ . '/../../../src/classes/Auth.php';
require_once __DIR__ . '/../../../src/classes/Book.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

// Vérifier si admin
if (!$auth->isAdmin()) {
    header('Location: ../../index.php');
    exit;
}

$bookClass = new Book($pdo);
$categories = $bookClass->getCategories();

$error = '';
$success = '';

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => htmlspecialchars($_POST['title'] ?? ''),
        'author' => htmlspecialchars($_POST['author'] ?? ''),
        'description' => htmlspecialchars($_POST['description'] ?? ''),
        'isbn' => htmlspecialchars($_POST['isbn'] ?? ''),
        'publisher' => htmlspecialchars($_POST['publisher'] ?? ''),
        'category_id' => intval($_POST['category_id'] ?? 0),
        'price' => floatval($_POST['price'] ?? 0),
        'stock' => intval($_POST['stock'] ?? 0),
        'pages' => intval($_POST['pages'] ?? 0),
        'publication_year' => intval($_POST['publication_year'] ?? 0),
        'cover_image' => $_POST['cover_image'] ?? null
    ];
    
    if (empty($data['title']) || empty($data['author'])) {
        $error = 'Titre et auteur sont obligatoires';
    } elseif ($data['price'] <= 0) {
        $error = 'Le prix doit être supérieur à 0';
    } else {
        if ($bookClass->create($data)) {
            $success = 'Livre ajouté avec succès!';
            $_POST = [];
        } else {
            $error = 'Erreur lors de l\'ajout du livre';
        }
    }
}

$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre - LibreBooks</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <!-- Navigation Admin -->
    <nav class="navbar admin-navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="/index.php" class="logo">📚 LibreBooks Admin</a>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Tableau de bord</a>
                <a href="manage-books.php" class="nav-link">Gérer les livres</a>
                <a href="add-book.php" class="nav-link active">Ajouter un livre</a>
                <span class="nav-link">Admin: <?php echo htmlspecialchars($user['username']); ?></span>
                <a href="../login.php?action=logout" class="nav-link logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Formulaire d'ajout -->
    <main class="admin-container">
        <h1>Ajouter un nouveau livre</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <section class="admin-section">
            <form method="POST" action="" class="admin-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Titre du livre *</label>
                        <input type="text" id="title" name="title" required class="form-control"
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                               placeholder="Ex: Le Seigneur des Anneaux">
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Auteur *</label>
                        <input type="text" id="author" name="author" required class="form-control"
                               value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>"
                               placeholder="Ex: J.R.R. Tolkien">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" id="isbn" name="isbn" class="form-control"
                               value="<?php echo htmlspecialchars($_POST['isbn'] ?? ''); ?>"
                               placeholder="Ex: 978-2-02-110345-8">
                    </div>
                    
                    <div class="form-group">
                        <label for="publisher">Éditeur</label>
                        <input type="text" id="publisher" name="publisher" class="form-control"
                               value="<?php echo htmlspecialchars($_POST['publisher'] ?? ''); ?>"
                               placeholder="Ex: Pocket">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Catégorie</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo isset($_POST['category_id']) && $_POST['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Prix (€) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0.01" required class="form-control"
                               value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
                               placeholder="Ex: 19.99">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="stock">Stock (quantité)</label>
                        <input type="number" id="stock" name="stock" min="0" class="form-control"
                               value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="pages">Nombre de pages</label>
                        <input type="number" id="pages" name="pages" min="0" class="form-control"
                               value="<?php echo htmlspecialchars($_POST['pages'] ?? ''); ?>"
                               placeholder="Ex: 480">
                    </div>
                    
                    <div class="form-group">
                        <label for="publication_year">Année de publication</label>
                        <input type="number" id="publication_year" name="publication_year" min="1800" class="form-control"
                               value="<?php echo htmlspecialchars($_POST['publication_year'] ?? date('Y')); ?>"
                               placeholder="Ex: 2020">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6" class="form-control"
                              placeholder="Description complète du livre..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="cover_image">URL de la couverture</label>
                    <input type="url" id="cover_image" name="cover_image" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['cover_image'] ?? ''); ?>"
                           placeholder="https://example.com/cover.jpg">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">Ajouter le livre</button>
                    <a href="manage-books.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 LibreBooks Admin. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>

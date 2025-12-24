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
$books = $bookClass->getAll(100, 0);

$error = '';
$success = '';
$book_to_edit = null;

// Si édition d'un livre
if (isset($_GET['id'])) {
    $book_to_edit = $bookClass->getById(intval($_GET['id']));
    if (!$book_to_edit) {
        header('Location: manage-books.php');
        exit;
    }
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
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
    } else {
        if ($bookClass->update(intval($_GET['id']), $data)) {
            $success = 'Livre modifié avec succès';
            $book_to_edit = $bookClass->getById(intval($_GET['id']));
        } else {
            $error = 'Erreur lors de la modification';
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
    <title>Gérer les livres - LibreBooks</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <!-- Navigation Admin -->
    <nav class="navbar admin-navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="../../index.php" class="logo">📚 LibreBooks Admin</a>
            </div>
            
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Tableau de bord</a>
                <a href="manage-books.php" class="nav-link active">Gérer les livres</a>
                <a href="add-book.php" class="nav-link">Ajouter un livre</a>
                <span class="nav-link">Admin: <?php echo htmlspecialchars($user['username']); ?></span>
                <a href="../login.php?action=logout" class="nav-link logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Contenu -->
    <main class="admin-container">
        <h1>Gérer les livres</h1>
        
        <?php if ($book_to_edit): ?>
            <!-- Formulaire d'édition -->
            <section class="admin-section">
                <h2>Modifier le livre</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="admin-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Titre *</label>
                            <input type="text" id="title" name="title" required class="form-control"
                                   value="<?php echo htmlspecialchars($book_to_edit['title']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="author">Auteur *</label>
                            <input type="text" id="author" name="author" required class="form-control"
                                   value="<?php echo htmlspecialchars($book_to_edit['author']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="isbn">ISBN</label>
                            <input type="text" id="isbn" name="isbn" class="form-control"
                                   value="<?php echo htmlspecialchars($book_to_edit['isbn']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="publisher">Éditeur</label>
                            <input type="text" id="publisher" name="publisher" class="form-control"
                                   value="<?php echo htmlspecialchars($book_to_edit['publisher']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">Catégorie</label>
                            <select id="category_id" name="category_id" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo $cat['id'] == $book_to_edit['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Prix (€) *</label>
                            <input type="number" id="price" name="price" step="0.01" required class="form-control"
                                   value="<?php echo $book_to_edit['price']; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" min="0" class="form-control"
                                   value="<?php echo $book_to_edit['stock']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="pages">Nombre de pages</label>
                            <input type="number" id="pages" name="pages" min="0" class="form-control"
                                   value="<?php echo $book_to_edit['pages']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="publication_year">Année de publication</label>
                            <input type="number" id="publication_year" name="publication_year" min="1800" class="form-control"
                                   value="<?php echo $book_to_edit['publication_year']; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5" class="form-control"><?php echo htmlspecialchars($book_to_edit['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="cover_image">URL de la couverture</label>
                        <input type="url" id="cover_image" name="cover_image" class="form-control"
                               value="<?php echo htmlspecialchars($book_to_edit['cover_image']); ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
                        <a href="manage-books.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </section>
        <?php else: ?>
            <!-- Liste des livres -->
            <div class="admin-actions">
                <a href="add-book.php" class="btn btn-primary">+ Ajouter un livre</a>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $b): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($b['title']); ?></td>
                            <td><?php echo htmlspecialchars($b['author']); ?></td>
                            <td><?php echo htmlspecialchars($b['category_name']); ?></td>
                            <td><?php echo number_format($b['price'], 2); ?> €</td>
                            <td><?php echo $b['stock']; ?></td>
                            <td>
                                <a href="manage-books.php?id=<?php echo $b['id']; ?>" class="btn btn-small btn-info">Éditer</a>
                                <button class="btn btn-small btn-danger" onclick="deleteBook(<?php echo $b['id']; ?>)">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 LibreBooks Admin. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        function deleteBook(bookId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce livre?')) {
                fetch('/src/api/delete-book.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `book_id=${bookId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>

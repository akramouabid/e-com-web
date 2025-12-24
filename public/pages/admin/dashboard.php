<?php
session_start();

require_once __DIR__ . '/../../../src/config/Database.php';
require_once __DIR__ . '/../../../src/classes/Auth.php';
require_once __DIR__ . '/../../../src/classes/User.php';
require_once __DIR__ . '/../../../src/classes/Book.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

// Vérifier si admin
if (!$auth->isAdmin()) {
    header('Location:../../index.php');
    exit;
}

$user = $auth->getCurrentUser();
$bookClass = new Book($pdo);
$userClass = new User($pdo);

$books = $bookClass->getAll(50, 0);
$categories = $bookClass->getCategories();
$users = $userClass->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - LibreBooks</title>
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
                <a href="dashboard.php" class="nav-link active">Tableau de bord</a>
                <a href="manage-books.php" class="nav-link">Gérer les livres</a>
                <a href="add-book.php" class="nav-link">Ajouter un livre</a>
                <span class="nav-link">Admin: <?php echo htmlspecialchars($user['username']); ?></span>
                <a href="../login.php?action=logout" class="nav-link logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Tableau de bord -->
    <main class="admin-container">
        <h1>Tableau de bord administrateur</h1>
        
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Livres</h3>
                <p class="stat-number"><?php echo count($books); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Catégories</h3>
                <p class="stat-number"><?php echo count($categories); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Utilisateurs</h3>
                <p class="stat-number"><?php echo count($users); ?></p>
            </div>
        </div>
        
        <!-- Gestion des utilisateurs -->
        <section class="admin-section">
            <h2>Gestion des utilisateurs</h2>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscrit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="role-badge <?php echo $u['role']; ?>">
                                    <?php echo ucfirst($u['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-small btn-warning" onclick="toggleRole(<?php echo $u['id']; ?>, '<?php echo $u['role']; ?>')">
                                    Changer rôle
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        
        <!-- Livres récents -->
        <section class="admin-section">
            <h2>Livres récents</h2>
            
            <a href="add-book.php" class="btn btn-primary">+ Ajouter un livre</a>
            
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
                    <?php foreach (array_slice($books, 0, 10) as $b): ?>
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
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 LibreBooks Admin. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        function toggleRole(userId, currentRole) {
            const newRole = currentRole === 'user' ? 'admin' : 'user';
            
            if (confirm(`Changer le rôle en ${newRole}?`)) {
                fetch('/src/api/update-user-role.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `user_id=${userId}&role=${newRole}`
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

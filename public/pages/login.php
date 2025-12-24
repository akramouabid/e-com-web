<?php
session_start();

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Auth.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

$error = '';
$success = '';

// Gestion de la déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth->logout();
    header('Location: ../index.php');
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = $auth->login($username, $password);
    
    if ($result['success']) {
        header('Location: ../index.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - LibreBooks</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation simple -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="../index.php" class="logo">📚 LibreBooks</a>
            </div>
            <div class="nav-menu">
                <a href="../index.php" class="nav-link">Accueil</a>
                <a href="register.php" class="nav-link">Inscription</a>
            </div>
        </div>
    </nav>

    <!-- Formulaire de connexion -->
    <main class="container">
        <section class="auth-section">
            <div class="auth-form">
                <h1>Connexion</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required class="form-control">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Connexion</button>
                </form>
                
                <p class="auth-link">
                    Pas encore de compte ? <a href="register.php">S'inscrire</a>
                </p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 LibreBooks. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>

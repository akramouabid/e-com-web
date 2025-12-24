<?php
session_start();

require_once __DIR__ . '/../../src/config/Database.php';
require_once __DIR__ . '/../../src/classes/Auth.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

$error = '';
$success = '';

// Si déjà connecté, rediriger
if ($auth->isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        $result = $auth->register($username, $email, $password);
        
        if ($result['success']) {
            $success = 'Inscription réussie! Vous pouvez maintenant vous connecter.';
            $_POST = []; // Effacer le formulaire
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - LibreBooks</title>
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
                <a href="login.php" class="nav-link">Connexion</a>
            </div>
        </div>
    </nav>

    <!-- Formulaire d'inscription -->
    <main class="container">
        <section class="auth-section">
            <div class="auth-form">
                <h1>Inscription</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" required class="form-control"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required class="form-control"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirm" name="password_confirm" required class="form-control">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
                </form>
                
                <p class="auth-link">
                    Déjà inscrit? <a href="login.php">Se connecter</a>
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

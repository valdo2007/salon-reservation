<?php
session_start();
require_once '../includes/db.php';

// Si déjà connecté → redirige vers le dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erreur = 'Identifiants incorrects.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Coupe & Style</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-page">
    <div class="login-card">

        <!-- Logo -->
        <div style="display:flex; align-items:center; justify-content:center; gap:0.75rem; margin-bottom:2rem;">
            <div class="logo-circle">C&S</div>
            <span style="font-family:var(--font-serif); font-size:1.25rem; color:var(--foreground);">
                Coupe & Style
            </span>
        </div>

        <h2>Espace Admin</h2>
        <p>Connectez-vous pour gérer les réservations</p>

        <?php if ($erreur) : ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Identifiant</label>
                <input type="text"
                       name="username"
                       class="form-input"
                       placeholder="admin"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required>
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password"
                       name="password"
                       class="form-input"
                       placeholder="••••••••"
                       required>
            </div>
            <button type="submit" class="btn-primary btn-primary-lg"
                    style="width:100%; margin-top:0.5rem;">
                Se connecter
            </button>
        </form>

    </div>
</div>

</body>
</html>
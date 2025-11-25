<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/includes/db.php';

$errors = [];

// On récupère éventuellement le code de classe passé depuis la home
$class_code = trim($_GET['class_code'] ?? $_POST['class_code'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = "Merci de renseigner votre e-mail et votre mot de passe.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, role, first_name, last_name, email, password_hash FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = "Identifiants incorrects.";
        } else {
            // Connexion OK : on met les infos en session
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_role']  = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name']  = $user['last_name'];

            // Redirection vers le tableau de bord
            $redirect = 'dashboard.php';
            if ($class_code !== '') {
                $redirect .= '?class_code=' . urlencode($class_code);
            }

            header("Location: $redirect");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - ClassConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="gradient-bg">
<header class="topbar">
    <div class="topbar-inner">
        <div class="logo">
            <span class="logo-icon">📚</span>
            <span class="logo-text"><span>Class</span>Connect</span>
        </div>
        <nav class="nav-links">
            <a href="index.php">Accueil</a>
        </nav>
        <div class="nav-actions">
            <a href="register.php" class="btn btn-pink">Créer un compte</a>
        </div>
    </div>
</header>

<main class="main-container">
    <section class="card">
        <h1 style="text-align:center; margin-bottom: 8px;">Se connecter</h1>
        <p class="subtitle" style="text-align:center; margin-bottom: 20px;">
            Entre ton e-mail et ton mot de passe pour accéder à ta classe.
        </p>

        <?php if (!empty($errors)) : ?>
            <div style="background:#ffe3e3;color:#a30000;padding:10px 14px;border-radius:12px;margin-bottom:16px;font-size:0.9rem;">
                <strong>Oups...</strong><br>
                <?php foreach ($errors as $err) : ?>
                    • <?= htmlspecialchars($err) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="join-form" style="max-width:400px;margin:0 auto;">
            <?php if ($class_code !== '') : ?>
                <input type="hidden" name="class_code" value="<?= htmlspecialchars($class_code) ?>">
                <p class="small-text" style="text-align:center;margin-bottom:10px;">
                    Code de classe détecté : <strong><?= htmlspecialchars($class_code) ?></strong>
                </p>
            <?php endif; ?>

            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($email ?? '') ?>" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-pink full-width" style="margin-top:12px;">
                Se connecter
            </button>
        </form>

        <p class="helper-text" style="margin-top:12px;">
            Pas encore de compte ?
            <a href="register.php">Créer un compte</a>.
        </p>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect.</p>
</footer>
</body>
</html>

<?php
session_start();
require __DIR__ . '/includes/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $password2    = $_POST['password2'] ?? '';
    $role         = $_POST['role'] ?? '';
    $child_name   = trim($_POST['child_name'] ?? '');

    // Validations
    if ($first_name === '' || $last_name === '' || $email === '' || $password === '' || $password2 === '') {
        $errors[] = "Tous les champs obligatoires doivent être remplis.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse e-mail n'est pas valide.";
    }

    if (!in_array($role, ['teacher', 'parent'], true)) {
        $errors[] = "Le rôle sélectionné est invalide.";
    }

    if ($password !== $password2) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Si pas d'erreur jusque-là, on vérifie l'email en base
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = "Un compte existe déjà avec cette adresse e-mail.";
        }
    }

    // Si toujours pas d'erreur, on insère
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (role, first_name, last_name, email, password_hash, child_name)
            VALUES (:role, :first_name, :last_name, :email, :password_hash, :child_name)
        ");

        $stmt->execute([
            ':role'          => $role,
            ':first_name'    => $first_name,
            ':last_name'     => $last_name,
            ':email'         => $email,
            ':password_hash' => $password_hash,
            ':child_name'    => $role === 'parent' ? ($child_name ?: null) : null
        ]);

        $success = true;

    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - ClassConnect</title>
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
            <a href="login.php" class="btn btn-outline">Se connecter</a>
        </div>
    </div>
</header>

<main class="main-container">
    <section class="card">
        <h1 style="text-align:center; margin-bottom: 8px;">Créer un compte</h1>
        <p class="subtitle" style="text-align:center; margin-bottom: 20px;">
            Professeur ou parent, crée ton accès à ClassConnect.
        </p>

        <?php if (!empty($errors)) : ?>
            <div style="background:#ffe3e3;color:#a30000;padding:10px 14px;border-radius:12px;margin-bottom:16px;font-size:0.9rem;">
                <strong>Oups...</strong><br>
                <?php foreach ($errors as $err) : ?>
                    • <?= htmlspecialchars($err) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success) : ?>
            <div style="background:#e0ffe8;color:#09622a;padding:10px 14px;border-radius:12px;margin-bottom:16px;font-size:0.9rem;">
                Ton compte a été créé avec succès 🎉<br>
                Tu peux maintenant <a href="login.php">te connecter</a>.
            </div>
        <?php endif; ?>

        <form method="post" class="join-form" style="max-width:400px;margin:0 auto;">
            <label for="role">Je suis :</label>
            <select id="role" name="role" style="margin-bottom:10px;padding:8px 12px;border-radius:999px;border:none;background:#f3f6ff;box-shadow:inset 0 0 0 1px rgba(180,200,255,0.6);">
                <option value="teacher" <?= isset($role) && $role === 'teacher' ? 'selected' : '' ?>>Professeur</option>
                <option value="parent" <?= isset($role) && $role === 'parent' ? 'selected' : '' ?>>Parent</option>
            </select>

            <label for="first_name">Prénom</label>
            <input type="text" id="first_name" name="first_name"
                   value="<?= htmlspecialchars($first_name ?? '') ?>" required>

            <label for="last_name">Nom</label>
            <input type="text" id="last_name" name="last_name"
                   value="<?= htmlspecialchars($last_name ?? '') ?>" required>

            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($email ?? '') ?>" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <label for="password2">Confirmer le mot de passe</label>
            <input type="password" id="password2" name="password2" required>

            <label for="child_name">Prénom de l’enfant (si parent)</label>
            <input type="text" id="child_name" name="child_name"
                   value="<?= htmlspecialchars($child_name ?? '') ?>">

            <button type="submit" class="btn btn-pink full-width" style="margin-top:12px;">
                Créer mon compte
            </button>
        </form>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect.</p>
</footer>
</body>
</html>

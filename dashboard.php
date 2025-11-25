<?php
session_start();
require __DIR__ . '/includes/db.php';

// Vérification connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// On récupère les infos du user
$user_id   = $_SESSION['user_id'];
$role      = $_SESSION['user_role'];
$firstName = $_SESSION['first_name'];
$lastName  = $_SESSION['last_name'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - ClassConnect</title>
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
            <a href="dashboard.php" class="active">Dashboard</a>
        </nav>
        <div class="nav-actions">
            <a href="logout.php" class="btn btn-outline">Déconnexion</a>
        </div>
    </div>
</header>

<main class="main-container">
    <section class="card">
        <h1 style="text-align:center;">Bonjour <?= htmlspecialchars($firstName) ?> 👋</h1>
        <p class="subtitle" style="text-align:center;">
            Vous êtes connecté en tant que <strong><?= $role === 'teacher' ? 'Professeur' : 'Parent' ?></strong>
        </p>
    </section>

    <?php if ($role === 'teacher'): ?>
    <!-- DASHBOARD PROF -->
    <section class="card">
        <h2>Vos classes</h2>
        <p>Ici s’afficheront vos classes.</p>
        <a href="create_class.php" class="btn btn-pink" style="margin-top:10px;">Créer une nouvelle classe</a>
    </section>

    <?php else: ?>
    <!-- DASHBOARD PARENT -->
    <section class="card">
        <h2>Votre classe</h2>
        <p>Ici s’affichera la classe que vous avez rejointe.</p>

        <form method="post" action="join_class.php" class="join-form" style="max-width:300px;">
            <input type="text" name="class_code" placeholder="Code de classe" required>
            <button class="btn btn-pink full-width" style="margin-top:8px;">Rejoindre</button>
        </form>
    </section>
    <?php endif; ?>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect.</p>
</footer>

</body>
</html>

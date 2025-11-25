<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Aide - ClassConnect</title>
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
            <a href="apropos.php">À propos</a>
            <a href="faq.php" class="active">Aide</a>
        </nav>
        <div class="nav-actions">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-pink">Créer un compte</a>
                <a href="login.php" class="btn btn-outline">Se connecter</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="main-container">
    <section class="card">
        <h2>Aide & questions fréquentes</h2>

        <div class="faq-list">

            <div class="faq-item">
                <h3>Je suis parent, comment commencer ?</h3>
                <p>
                    • Crée un compte → Choisis <strong>Parent</strong>.<br>
                    • Demande à l’enseignant le <strong>code de la classe</strong>.<br>
                    • Connecte-toi → clique sur <strong>Rejoindre une classe</strong>.
                </p>
            </div>

            <div class="faq-item">
                <h3>Je suis enseignant, comment créer ma classe ?</h3>
                <p>
                    • Crée un compte → Choisis <strong>Professeur</strong>.<br>
                    • Dashboard → <strong>Créer une classe</strong>.<br>
                    • Partage le <strong>code de classe</strong> aux parents.
                </p>
            </div>

            <div class="faq-item">
                <h3>Je n’arrive pas à me connecter</h3>
                <p>
                    Vérifie ton courriel et ton mot de passe.  
                    (Dans ce prototype, il n’y a pas encore de récupération de mot de passe.)
                </p>
            </div>

            <div class="faq-item">
                <h3>Ce site est-il officiel ?</h3>
                <p>
                    Non. ClassConnect est un <strong>projet universitaire UQAC</strong> réalisé dans le cadre d’un cours de développement web.
                </p>
            </div>

        </div>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect – Projet étudiant UQAC.</p>
</footer>

</body>
</html>

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>À propos - ClassConnect</title>
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
            <a href="apropos.php" class="active">À propos</a>
            <a href="faq.php">Aide</a>
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
    <section class="card how-it-works">
        <h2>À propos de ClassConnect</h2>

        <div class="about-layout">
            <div class="about-text">
                <p>
                    ClassConnect est une plateforme simple destinée à améliorer la
                    <strong>communication entre enseignants, parents et élèves du primaire</strong>.
                </p>
                <p>
                    Ce projet a été réalisé dans le cadre d’un travail universitaire à l’UQAC
                    (Université du Québec à Chicoutimi), au sein d’un cours axé sur le développement
                    web et la création d’applications éducatives.
                </p>
                <p>
                    Notre objectif était d’imaginer un outil scolaire :
                </p>
                <ul class="about-list">
                    <li>plus moderne,</li>
                    <li>plus intuitif,</li>
                    <li>plus adapté au jeune public,</li>
                    <li>et bien plus simple que les plateformes classiques.</li>
                </ul>
                <p>
                    Grâce à un système de <strong>classe à code unique</strong>, les familles
                    peuvent rejoindre un espace sécurisé en quelques secondes, sans besoin de compte complexe.
                </p>
            </div>

            <div class="about-side">
                <div class="about-card">
                    <h3>Contexte du projet</h3>
                    <p>
                        Développé par des étudiants dans un objectif éducatif, ClassConnect représente une
                        <strong>preuve de concept</strong> démontrant comment un outil minimaliste peut faciliter la
                        communication école ↔ famille.
                    </p>
                    <div class="about-tags">
                        <span class="pill">UQAC</span>
                        <span class="pill">Projet universitaire</span>
                        <span class="pill">Outil éducatif</span>
                    </div>
                </div>

                <div class="about-card">
                    <h3>Pourquoi ClassConnect ?</h3>
                    <p>
                        Parce que les plateformes existantes sont souvent trop chargées,
                        trop professionnelles ou trop lourdes pour les jeunes élèves.
                    </p>
                    <p>
                        Avec ClassConnect, tout est simplifié pour être compris
                        <strong>du premier coup</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect – Projet étudiant UQAC.</p>
</footer>

</body>
</html>

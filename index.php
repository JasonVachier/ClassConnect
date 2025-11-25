<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ClassConnect</title>
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
            <a href="index.php" class="active">Accueil</a>
            <a href="#apropos">À propos</a>
            <a href="#aide">Aide</a>
        </nav>
        <div class="nav-actions">
            <a href="register.php" class="btn btn-pink">Créer un compte</a>
            <a href="login.php" class="btn btn-outline">Se connecter</a>
        </div>
    </div>
</header>

<main class="main-container">
    <section class="card hero">
        <h1>Bienvenue sur <span class="highlight">ClassConnect</span> !</h1>
        <p class="subtitle">
            Le site amusant pour apprendre et partager avec ta classe.
        </p>

        <div class="hero-main">
            <div class="hero-block">
                <div class="hero-logo-card">ClassConnect</div>
            </div>

            <div class="hero-join">
                <h2>Entre le code de ta classe</h2>
                <form action="login.php" method="get" class="join-form">
                    <label for="class_code" class="sr-only">Code de classe</label>
                    <input
                        type="text"
                        id="class_code"
                        name="class_code"
                        placeholder="Ex : ABC123"
                        maxlength="10"
                        required
                    >
                    <button type="submit" class="btn btn-pink full-width">
                        Rejoindre
                    </button>
                </form>
                <p class="helper-text">
                    Tu n’as pas encore de compte ?
                    <a href="register.php">Crée ton compte en 1 minute</a>.
                </p>
            </div>
        </div>
    </section>

    <section class="card how-it-works" id="apropos">
        <h2>Comment ça marche ?</h2>
        <div class="steps">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Créer un compte</h3>
                <p>Inscris-toi avec ton prénom et ton rôle (parent ou prof) en quelques clics.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Entrer le code</h3>
                <p>Demande le code à ton enseignant et rejoins ton espace de classe.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Se connecter</h3>
                <p>Accède aux annonces, devoirs et messages de ta classe quand tu veux.</p>
            </div>
        </div>
    </section>

    <section class="card" id="aide">
        <p class="small-text">
            Besoin d’aide ? Parle-en à ton enseignant ou écris-nous depuis la page de contact (à venir).
        </p>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect – Un site conçu pour les enfants par des passionnés d’éducation.</p>
</footer>
</body>
</html>

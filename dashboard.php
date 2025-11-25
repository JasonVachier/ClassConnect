<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$role      = $_SESSION['user_role'];
$firstName = $_SESSION['first_name'];
$lastName  = $_SESSION['last_name'];

// Messages de succès potentiels
$classCreated   = isset($_GET['class_created']);
$joined         = isset($_GET['joined']);
$alreadyInClass = isset($_GET['already_in_class']);

// Récupération des classes selon le rôle
if ($role === 'teacher') {
    $stmt = $pdo->prepare("
        SELECT id, name, grade_level, join_code, created_at
        FROM classes
        WHERE teacher_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);
    $classes = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("
        SELECT c.id, c.name, c.grade_level, c.join_code, c.created_at
        FROM classes c
        JOIN class_members cm ON cm.class_id = c.id
        WHERE cm.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $classes = $stmt->fetchAll();
}
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

        <?php if ($classCreated): ?>
            <div style="background:#e0ffe8;color:#09622a;padding:10px 14px;border-radius:12px;margin:16px auto 0;max-width:500px;font-size:0.9rem;">
                Classe créée avec succès 🎉<br>
                Le code de la classe est visible dans la liste ci-dessous.
            </div>
        <?php endif; ?>

        <?php if ($joined): ?>
            <div style="background:#e0ffe8;color:#09622a;padding:10px 14px;border-radius:12px;margin:16px auto 0;max-width:500px;font-size:0.9rem;">
                Vous avez rejoint la classe avec succès 🎉
            </div>
        <?php endif; ?>

        <?php if ($alreadyInClass): ?>
            <div style="background:#fff6d6;color:#8a6200;padding:10px 14px;border-radius:12px;margin:16px auto 0;max-width:500px;font-size:0.9rem;">
                Vous êtes déjà membre de cette classe 🙂
            </div>
        <?php endif; ?>
    </section>

    <?php if ($role === 'teacher'): ?>
        <!-- Dashboard professeur -->
        <section class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <h2>Vos classes</h2>
                <a href="create_class.php" class="btn btn-pink">Créer une nouvelle classe</a>
            </div>

            <?php if (empty($classes)): ?>
                <p>Vous n’avez pas encore créé de classe.</p>
            <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:10px;margin-top:10px;">
                    <?php foreach ($classes as $class): ?>
                        <div style="background:#f3f6ff;border-radius:16px;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-weight:600;">
                                    <?= htmlspecialchars($class['name']) ?>
                                    <?php if (!empty($class['grade_level'])): ?>
                                        <span style="font-size:0.85rem;color:#666;">(<?= htmlspecialchars($class['grade_level']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:0.85rem;color:#555;margin-top:4px;">
                                    Code de la classe :
                                    <strong><?= htmlspecialchars($class['join_code']) ?></strong>
                                </div>
                            </div>
                            <div>
                                <a href="class.php?id=<?= (int)$class['id'] ?>" class="btn btn-outline">Ouvrir</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    <?php else: ?>
        <!-- Dashboard parent -->
        <section class="card">
            <h2>Votre / vos classes</h2>

            <?php if (empty($classes)): ?>
                <p>Vous n’avez pas encore rejoint de classe.</p>
                <a href="join_class.php" class="btn btn-pink" style="margin-top:10px;">Rejoindre une classe</a>
            <?php else: ?>
                <p>Voici les classes que vous avez rejointes :</p>
                <div style="display:flex;flex-direction:column;gap:10px;margin-top:10px;">
                    <?php foreach ($classes as $class): ?>
                        <div style="background:#f3f6ff;border-radius:16px;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-weight:600;">
                                    <?= htmlspecialchars($class['name']) ?>
                                    <?php if (!empty($class['grade_level'])): ?>
                                        <span style="font-size:0.85rem;color:#666;">(<?= htmlspecialchars($class['grade_level']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:0.85rem;color:#555;margin-top:4px;">
                                    Code de la classe :
                                    <strong><?= htmlspecialchars($class['join_code']) ?></strong>
                                </div>
                            </div>
                            <div>
                                <a href="class.php?id=<?= (int)$class['id'] ?>" class="btn btn-outline">Ouvrir</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="join_class.php" class="btn btn-pink" style="margin-top:16px;">Rejoindre une autre classe</a>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect.</p>
</footer>

</body>
</html>

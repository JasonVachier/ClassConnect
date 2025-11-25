<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/includes/db.php';

// Vérifier qu'on est connecté + professeur
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['user_role'] !== 'teacher') {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Fonction pour générer un code de classe
function generateJoinCode($length = 6): string {
    $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789'; // pas de O/0 pour éviter confusion
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $grade_level = trim($_POST['grade_level'] ?? '');

    if ($name === '') {
        $errors[] = "Le nom de la classe est obligatoire.";
    }

    if (empty($errors)) {
        // Générer un code unique
        $join_code = null;
        $tries = 0;

        do {
            $join_code = generateJoinCode(6);
            $stmt = $pdo->prepare("SELECT id FROM classes WHERE join_code = ?");
            $stmt->execute([$join_code]);
            $exists = $stmt->fetch();
            $tries++;
        } while ($exists && $tries < 10);

        if ($exists) {
            $errors[] = "Impossible de générer un code unique, réessayez.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO classes (teacher_id, name, grade_level, join_code)
                VALUES (:teacher_id, :name, :grade_level, :join_code)
            ");
            $stmt->execute([
                ':teacher_id' => $user_id,
                ':name'       => $name,
                ':grade_level'=> $grade_level ?: null,
                ':join_code'  => $join_code
            ]);

            // Redirection vers le dashboard avec message de succès
            header("Location: dashboard.php?class_created=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une classe - ClassConnect</title>
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
            <a href="dashboard.php">Dashboard</a>
        </nav>
        <div class="nav-actions">
            <a href="logout.php" class="btn btn-outline">Déconnexion</a>
        </div>
    </div>
</header>

<main class="main-container">
    <section class="card">
        <h1 style="text-align:center;margin-bottom:10px;">Créer une nouvelle classe</h1>
        <p class="subtitle" style="text-align:center;margin-bottom:20px;">
            Donne un nom à ta classe et, si tu veux, le niveau (ex : 2e année).
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
            <label for="name">Nom de la classe</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($name ?? '') ?>" required>

            <label for="grade_level">Niveau (optionnel)</label>
            <input type="text" id="grade_level" name="grade_level"
                   placeholder="Ex : 2e année"
                   value="<?= htmlspecialchars($grade_level ?? '') ?>">

            <button type="submit" class="btn btn-pink full-width" style="margin-top:12px;">
                Créer la classe
            </button>
        </form>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect.</p>
</footer>
</body>
</html>

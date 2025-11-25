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

if ($_SESSION['user_role'] !== 'parent') {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_code = strtoupper(trim($_POST['class_code'] ?? ''));

    if ($class_code === '') {
        $errors[] = "Merci de renseigner un code de classe.";
    }

    if (empty($errors)) {
        // Chercher la classe correspondante
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE join_code = ? LIMIT 1");
        $stmt->execute([$class_code]);
        $class = $stmt->fetch();

        if (!$class) {
            $errors[] = "Aucune classe trouvée avec ce code.";
        } else {
            $class_id = $class['id'];

            // Vérifier si le parent est déjà membre
            $stmt = $pdo->prepare("SELECT id FROM class_members WHERE class_id = ? AND user_id = ? LIMIT 1");
            $stmt->execute([$class_id, $user_id]);
            $membership = $stmt->fetch();

            if ($membership) {
                // Déjà membre : on redirige avec un message
                header("Location: dashboard.php?already_in_class=1");
                exit;
            } else {
                // Ajouter le parent à la classe
                $stmt = $pdo->prepare("
                    INSERT INTO class_members (class_id, user_id, role_in_class)
                    VALUES (?, ?, 'parent')
                ");
                $stmt->execute([$class_id, $user_id]);

                header("Location: dashboard.php?joined=1");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rejoindre une classe - ClassConnect</title>
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
        <h1 style="text-align:center;margin-bottom:10px;">Rejoindre une classe</h1>
        <p class="subtitle" style="text-align:center;margin-bottom:20px;">
            Entre le code donné par l’enseignant pour rejoindre la classe.
        </p>

        <?php if (!empty($errors)) : ?>
            <div style="background:#ffe3e3;color:#a30000;padding:10px 14px;border-radius:12px;margin-bottom:16px;font-size:0.9rem;">
                <strong>Oups...</strong><br>
                <?php foreach ($errors as $err) : ?>
                    • <?= htmlspecialchars($err) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="join-form" style="max-width:300px;margin:0 auto;">
            <label for="class_code">Code de classe</label>
            <input type="text" id="class_code" name="class_code"
                   placeholder="Ex : ABC123"
                   value="<?= htmlspecialchars($class_code ?? '') ?>"
                   required>

            <button type="submit" class="btn btn-pink full-width" style="margin-top:12px;">
                Rejoindre la classe
            </button>
        </form>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect.</p>
</footer>
</body>
</html>

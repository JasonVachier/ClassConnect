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

// Récupérer l'id de la classe
$class_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($class_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Vérifier que l'utilisateur a le droit de voir cette classe
if ($role === 'teacher') {
    // Doit être le prof de cette classe
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $user_id]);
    $class = $stmt->fetch();
} else {
    // Doit être membre de la classe (parent)
    $stmt = $pdo->prepare("
        SELECT c.*
        FROM classes c
        JOIN class_members cm ON cm.class_id = c.id
        WHERE c.id = ? AND cm.user_id = ?
    ");
    $stmt->execute([$class_id, $user_id]);
    $class = $stmt->fetch();
}

if (!$class) {
    // Pas autorisé ou classe inexistante
    header("Location: dashboard.php");
    exit;
}

$errors = [];

// Gestion des formulaires (annonce / message)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Création d'annonce (prof uniquement)
    if ($action === 'announcement' && $role === 'teacher') {
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            $errors[] = "Le titre et le contenu de l’annonce sont obligatoires.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO announcements (class_id, author_id, title, content)
                VALUES (:class_id, :author_id, :title, :content)
            ");
            $stmt->execute([
                ':class_id' => $class_id,
                ':author_id'=> $user_id,
                ':title'    => $title,
                ':content'  => $content
            ]);

            // Refresh pour éviter le repost du formulaire
            header("Location: class.php?id=" . $class_id . "&ann=1");
            exit;
        }
    }

    // Envoi de message (prof ou parent)
    if ($action === 'message') {
        $content = trim($_POST['content'] ?? '');

        if ($content === '') {
            $errors[] = "Le message ne peut pas être vide.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO messages (class_id, sender_id, content)
                VALUES (:class_id, :sender_id, :content)
            ");
            $stmt->execute([
                ':class_id'  => $class_id,
                ':sender_id' => $user_id,
                ':content'   => $content
            ]);

            header("Location: class.php?id=" . $class_id . "&msg=1");
            exit;
        }
    }
}

// Récupérer les annonces
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.content, a.created_at,
           u.first_name, u.last_name
    FROM announcements a
    LEFT JOIN users u ON u.id = a.author_id
    WHERE a.class_id = ?
    ORDER BY a.created_at DESC
");
$stmt->execute([$class_id]);
$announcements = $stmt->fetchAll();

// Récupérer les messages
$stmt = $pdo->prepare("
    SELECT m.id, m.content, m.created_at,
           u.first_name, u.last_name, u.role
    FROM messages m
    LEFT JOIN users u ON u.id = m.sender_id
    WHERE m.class_id = ?
    ORDER BY m.created_at ASC
");
$stmt->execute([$class_id]);
$messages = $stmt->fetchAll();

$annCreated = isset($_GET['ann']);
$msgSent    = isset($_GET['msg']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Classe <?= htmlspecialchars($class['name']) ?> - ClassConnect</title>
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
            <a href="class.php?id=<?= (int)$class_id ?>" class="active">
                Classe <?= htmlspecialchars($class['name']) ?>
            </a>
        </nav>
        <div class="nav-actions">
            <a href="logout.php" class="btn btn-outline">Déconnexion</a>
        </div>
    </div>
</header>

<main class="main-container">
    <section class="card">
        <h1 style="text-align:center;margin-bottom:4px;">
            Classe <?= htmlspecialchars($class['name']) ?>
        </h1>
        <?php if (!empty($class['grade_level'])): ?>
            <p class="subtitle" style="text-align:center;margin-bottom:4px;">
                Niveau : <?= htmlspecialchars($class['grade_level']) ?>
            </p>
        <?php endif; ?>
        <p class="small-text" style="text-align:center;">
            Code de classe : <strong><?= htmlspecialchars($class['join_code']) ?></strong>
        </p>

        <?php if ($annCreated): ?>
            <div style="background:#e0ffe8;color:#09622a;padding:8px 12px;border-radius:12px;margin:12px auto 0;max-width:480px;font-size:0.9rem;">
                Annonce publiée avec succès 🎉
            </div>
        <?php endif; ?>

        <?php if ($msgSent): ?>
            <div style="background:#e0ffe8;color:#09622a;padding:8px 12px;border-radius:12px;margin:12px auto 0;max-width:480px;font-size:0.9rem;">
                Message envoyé ✅
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)) : ?>
            <div style="background:#ffe3e3;color:#a30000;padding:10px 14px;border-radius:12px;margin:16px auto 0;max-width:500px;font-size:0.9rem;">
                <strong>Oups...</strong><br>
                <?php foreach ($errors as $err) : ?>
                    • <?= htmlspecialchars($err) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($role === 'teacher'): ?>
        <!-- Formulaire pour créer une annonce (prof) -->
        <section class="card">
            <h2>Publier une annonce</h2>
            <form method="post" class="join-form" style="max-width:500px;">
                <input type="hidden" name="action" value="announcement">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       required>

                <label for="content">Message</label>
                <textarea id="content" name="content" rows="4"
                          style="border-radius:16px;border:none;padding:10px 14px;background:#f3f6ff;box-shadow:inset 0 0 0 1px rgba(180,200,255,0.6);resize:vertical;"
                          required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>

                <button type="submit" class="btn btn-pink full-width" style="margin-top:12px;">
                    Publier l’annonce
                </button>
            </form>
        </section>
    <?php endif; ?>

    <!-- Liste des annonces -->
    <section class="card">
        <h2>Annonces de la classe</h2>
        <?php if (empty($announcements)): ?>
            <p>Aucune annonce pour le moment.</p>
        <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:12px;margin-top:10px;">
                <?php foreach ($announcements as $ann): ?>
                    <article style="background:#f3f6ff;border-radius:16px;padding:10px 12px;">
                        <h3 style="font-size:1rem;margin-bottom:4px;">
                            <?= htmlspecialchars($ann['title']) ?>
                        </h3>
                        <p style="font-size:0.9rem;margin-bottom:6px;">
                            <?= nl2br(htmlspecialchars($ann['content'])) ?>
                        </p>
                        <p class="small-text" style="font-size:0.75rem;color:#666;">
                            Publié par
                            <?= htmlspecialchars(($ann['first_name'] ?? 'Inconnu') . ' ' . ($ann['last_name'] ?? '')) ?>
                            le <?= htmlspecialchars($ann['created_at']) ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Messagerie -->
    <section class="card">
        <h2>Messages</h2>

        <div style="max-height:250px;overflow-y:auto;border-radius:16px;padding:10px;background:#f9fbff;margin-bottom:12px;">
            <?php if (empty($messages)): ?>
                <p class="small-text">Aucun message pour le moment. Soyez le premier à écrire 🙂</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div style="margin-bottom:8px;">
                        <div style="font-size:0.8rem;color:#555;">
                            <strong>
                                <?= htmlspecialchars(($msg['first_name'] ?? 'Utilisateur') . ' ' . ($msg['last_name'] ?? '')) ?>
                            </strong>
                            (<?= $msg['role'] === 'teacher' ? 'Professeur' : 'Parent' ?>)
                            — <span style="opacity:0.7;"><?= htmlspecialchars($msg['created_at']) ?></span>
                        </div>
                        <div style="font-size:0.9rem;">
                            <?= nl2br(htmlspecialchars($msg['content'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form method="post" class="join-form" style="max-width:500px;">
            <input type="hidden" name="action" value="message">
            <label for="msg_content">Écrire un message</label>
            <textarea id="msg_content" name="content" rows="3"
                      style="border-radius:16px;border:none;padding:10px 14px;background:#f3f6ff;box-shadow:inset 0 0 0 1px rgba(180,200,255,0.6);resize:vertical;"
                      required></textarea>
            <button type="submit" class="btn btn-pink full-width" style="margin-top:10px;">
                Envoyer
            </button>
        </form>
    </section>
</main>

<footer class="footer">
    <p>© 2025 ClassConnect.</p>
</footer>

</body>
</html>

<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
    header('Location: ../auth/seconnecter.php');
    exit;
}

$formateur_id = $_SESSION['user_id'];

// Supprimer une notification si une requête POST est reçue avec notification_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = intval($_POST['notification_id']);

    // Sécuriser : vérifier que la notification appartient bien au formateur
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE id = ? AND receiver_id = ?");
    $stmtCheck->execute([$notification_id, $formateur_id]);
    if ($stmtCheck->fetchColumn() > 0) {
        $stmtDelete = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
        $stmtDelete->execute([$notification_id]);
    }
    // Rediriger pour éviter resoumission du formulaire
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE receiver_id = ? ORDER BY created_at DESC");
$stmt->execute([$formateur_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="style.css">

</head>
<style>
    :root {
    --bg-body: #f9fafb;
    --text-main: #111827;
    --text-muted: #6b7280;
    --badge-bg-blue: #dbeafe;
    --badge-text-blue: #1d4ed8;
    --text-separator: #9ca3af;
    --avatar-bg-purple: #8b5cf6;

    --card-bg: white;
    --shadow-soft: rgba(0, 0, 0, 0.05);
    --border-card: #e5e7eb;

    --icon-bg-blue: #eff6ff;
    --icon-text-blue: #2563eb;
    --icon-bg-green: #f0fdf4;
    --icon-text-green: #16a34a;
    --icon-bg-purple: #faf5ff;
    --icon-text-purple: #9333ea;
    --icon-bg-gray: #f9fafb;
    --icon-text-gray: #6b7280;
    --icon-bg-orange: #ffa726;
    --icon-bg-red: #ef5350;

    --section-bg: #eff6ff;
    --section-border: #bfdbfe;
    --section-title: #1e3a8a;
    --section-text: #1d4ed8;
}

body {
    background-color: var(--bg-body);
    color: var(--text-main);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    /* max-width: 700px;
    margin: 40px auto;
    padding: 0 20px; */
}

h1 {
    color: var(--section-title);
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
}

ul {
    list-style: none;
    padding: 0;
    margin: 0;
    width: 45%;
}

li {
    background-color: var(--card-bg);
    border: 1px solid var(--border-card);
    border-radius: 10px;
    padding: 20px 25px;
    margin-bottom: 15px;
    box-shadow: 0 2px 6px var(--shadow-soft);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    cursor: default;
}

li:hover {
    background-color: var(--section-bg);
    box-shadow: 0 4px 12px var(--shadow-soft);
}

small {
    display: block;
    margin-top: 10px;
    color: var(--text-muted);
    font-size: 0.9rem;
}

@media (max-width: 480px) {
    body {
        padding: 0 10px;
        margin: 20px auto;
    }
    li {
        padding: 15px 20px;
    }
    h1 {
        font-size: 1.6rem;
    }
}
button.effacer-btn {
    background-color: #ef5350;
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    float: right;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
}

button.effacer-btn:hover {
    background-color: #d32f2f;
}

</style>
<body>
   <?php require_once '../include/hedear.php'; ?>

    <h1>Mes Notifications</h1>
    <ul>
        <?php foreach ($notifications as $notif): ?>
            <li>
                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment effacer cette notification ?');" style="display:inline;">
                    <input type="hidden" name="notification_id" value="<?= htmlspecialchars($notif['id']) ?>">
                    <button type="submit" class="effacer-btn" title="Effacer notification">&times;</button>
                </form>
                <?= htmlspecialchars($notif['message']) ?>
                <br><small><?= htmlspecialchars($notif['created_at']) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

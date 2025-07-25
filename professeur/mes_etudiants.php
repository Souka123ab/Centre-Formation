<?php
session_start();
require_once '../config/database.php'; // connexion PDO

// vérifier que l'utilisateur est connecté et est formateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
    header('Location: login.php');
    exit;
}

$formateur_id = $_SESSION['user_id'];

// Requête pour récupérer les étudiants (sans filtre ici)
$sql = "SELECT id, first_name, last_name, email 
        FROM users 
        WHERE role = 'etudiant'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes étudiants</title>
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

* {
    box-sizing: border-box;
}

body {
    background-color: var(--bg-body);
    color: var(--text-main);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
}

h1 {
    color: var(--section-title);
    margin-bottom: 1.5rem;
    text-align: center;
}

ul {
    list-style: none;
    padding: 0;
    max-width: 600px;
    margin: 0 auto;
}

li {
    background-color: var(--card-bg);
    border: 1px solid var(--border-card);
    border-radius: 8px;
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 6px var(--shadow-soft);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: box-shadow 0.3s ease;
}

li:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

li span.name {
    font-weight: 600;
}

li span.email {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-style: italic;
}

/* Responsive */
@media (max-width: 480px) {
    body {
        padding: 1rem;
    }

    li {
        flex-direction: column;
        align-items: flex-start;
    }

    li span.email {
        margin-top: 0.3rem;
    }
}

</style>
<body>
    <?php require_once '../include/hedear.php'; ?>
    <h1>Liste des étudiants inscrits à vos formations</h1>
    <ul>
        <?php foreach ($etudiants as $etudiant): ?>
            <li><?= htmlspecialchars($etudiant['first_name'] . ' ' . $etudiant['last_name']) ?> — <?= htmlspecialchars($etudiant['email']) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

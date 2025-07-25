<?php
session_start();
require_once '../config/database.php'; // adapte le chemin selon ton projet

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Correction ici : remplacer f.nom par f.title
$sql = "SELECT f.id, f.title, f.description, ft.date_terminee AS completed_at
        FROM formations_terminees ft
        INNER JOIN formations f ON f.id = ft.formation_id
        WHERE ft.user_id = :user_id
        ORDER BY ft.date_terminee DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Leçons Complétées</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            color: #111827;
            margin: 0;
        }
        h1 {
            text-align: center;
            color: #1e3a8a;
        }
        .lesson-list {
            max-width: 700px;
            margin: 2rem auto;
        }
        .lesson-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .lesson-title {
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
        }
        .lesson-desc {
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .lesson-date {
            font-size: 0.85rem;
            color: #2563eb;
        }
        .back-button {
    display: inline-block;
    margin: 1rem 2rem;
    padding: 0.5rem 1rem;
    background-color: #2563eb;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.back-button:hover {
    background-color: #1d4ed8;
    transform: scale(1.03);
}

    </style>
</head>
<body>
</body>
        <?php 
        require_once '../include/hedear.php';?>
        <a href="dashobrdE.php" class="back-button">← Retour au tableau de bord</a>
    <h1>Leçons Complétées</h1>
    <div class="lesson-list">
        <?php if (count($lessons) === 0): ?>
            <p>Vous n'avez encore terminé aucune leçon.</p>
        <?php else: ?>
            <?php foreach ($lessons as $lesson): ?>
                <div class="lesson-item">
                    <div class="lesson-title"><?= htmlspecialchars($lesson['title']) ?></div>
                    <div class="lesson-desc"><?= htmlspecialchars($lesson['description']) ?></div>
                    <div class="lesson-date">Terminée le <?= date('d/m/Y', strtotime($lesson['completed_at'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>

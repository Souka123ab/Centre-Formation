<?php
session_start();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "Utilisateur non connecté.";
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT cours.*, formations.title AS formation_title
        FROM progression
        INNER JOIN cours ON progression.cours_id = cours.id
        INNER JOIN formations ON cours.formation_id = formations.id
        WHERE progression.user_id = ? AND progression.statut = 'commencé'
    ");
    $stmt->execute([$user_id]);
    $coursCommences = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}

// Pour déboguer :
// echo "Mon ID: " . $user_id;
// echo "<pre>";
// print_r($coursCommences);
// echo "</pre>";


// $stmt = $pdo->prepare($sql);
// $stmt->execute(['user_id' => $user_id]);
// $coursCommences = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Mes Formations Commencées</title>
    <link rel="stylesheet" href="../css/mecours.css" />
  
</head>
<body>

<?php require_once '../include/hedear.php'; ?>
<a href="javascript:history.back()" class="back-btn">← Retour</a>

<div class="nav-grid">
    <?php if (!empty($coursCommences)): ?>
        <?php foreach ($coursCommences as $cours): ?>
            <a href="lesson-viewer.php?cours_id=<?= $cours['id'] ?>" class="nav-card">
                <div class="card-header">
                    <div class="icon-wrapper">
                        📘
                    </div>
                    <h3><?= htmlspecialchars($cours['formation_title']) ?></h3>
                </div>
                <p class="card-description">
                    <?= htmlspecialchars($cours['title']) ?><br>
                    <small>Accéder à la leçon commencée</small>
                </p>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="padding: 20px;">Vous n'avez encore commencé aucune formation.</p>
    <?php endif; ?>
</div>

</body>
</html>

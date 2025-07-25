<?php
$score = $_GET['score'] ?? null;
$quiz_id = $_GET['quiz_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Résultat du quiz</title>
</head>
<body>
    <h1>Résultat du Quiz</h1>
    <?php if ($score !== null): ?>
        <p>Vous avez obtenu un score de <strong><?= $score ?>%</strong></p>
    <?php else: ?>
        <p>Erreur : score non disponible.</p>
    <?php endif; ?>
    <a href="dashobrdE.php">Retour au tableau de bord</a>
</body>
</html>

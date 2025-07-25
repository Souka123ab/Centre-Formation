<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT id, title, description FROM formations");
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors du chargement des formations : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formations Disponibles</title>
    <link rel="stylesheet" href="../css/formations-disponibles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php require_once '../include/hedear.php'; ?>

<div class="container">
    <a href="javascript:history.back()" class="back-arrow">← Retour</a>
    <h1 class="page-title">Toutes les Formations</h1>

    <div class="cours-grid">
        <?php foreach ($formations as $formation): ?>
            <div class="cours-card">
                <h2 class="cours-title"><?= htmlspecialchars($formation['title']) ?></h2>
                <p class="cours-description"><?= nl2br(htmlspecialchars($formation['description'])) ?></p>
                <a href="formation-details.php?id=<?= $formation['id'] ?>" class="btn-start">Voir les cours</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>

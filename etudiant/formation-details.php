<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Formation non trouvée.");
}

$formation_id = $_GET['id'];

// Récupérer les infos de la formation
try {
    $stmtFormation = $pdo->prepare("SELECT * FROM formations WHERE id = ?");
    $stmtFormation->execute([$formation_id]);
    $formation = $stmtFormation->fetch(PDO::FETCH_ASSOC);

    if (!$formation) {
        die("Formation introuvable.");
    }

    // Récupérer les cours liés à cette formation
    $stmtCours = $pdo->prepare("SELECT * FROM cours WHERE formation_id = ?");
    $stmtCours->execute([$formation_id]);
    $coursList = $stmtCours->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($formation['title']) ?></title>
    <link rel="stylesheet" href="../css/formationdetail.css">
</head>
<body>
<?php require_once '../include/hedear.php'; ?>

<div class="retour-wrapper">
    <a href="javascript:history.back()" class="retour-link">← Retour</a>
</div>

<div class="container">
    <h1 class="page-title"><?= htmlspecialchars($formation['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($formation['description'])) ?></p>

    <h2 style="margin-top: 30px;">Cours de cette formation</h2>
    <div class="cours-grid">
        <?php if (count($coursList) > 0): ?>
            <?php foreach ($coursList as $cours): ?>
                <div class="cours-card">
                    <h3 class="cours-title"><?= htmlspecialchars($cours['title']) ?></h3>
                    <p class="cours-description"><?= nl2br(htmlspecialchars($cours['description'])) ?></p>
<a href="start-cours.php?cours_id=<?= $cours['id'] ?>" class="btn-start">Commencer le cours</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun cours n’est encore disponible pour cette formation.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
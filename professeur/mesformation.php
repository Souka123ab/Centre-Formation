<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php'; // Connection to the database
require_once '/xampp/htdocs/centre-formation/include/hedear.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/seconnecter.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Debug: Check database connection
try {
    $pdo->query("SELECT 1");
    // echo "Connexion à la base de données réussie.<br>";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage() . "<br>";
    exit; // Stop execution if connection fails
}

// Retrieve formations created by the user + their courses
$sql = "
    SELECT 
        cours.id AS cours_id,
        cours.title AS cours_title,
        cours.description AS cours_description,
        cours.video_url,
        cours.document_url,
        cours.created_at,
        formations.id AS formation_id,
        formations.title AS formation_title,
        formations.description AS formation_description,
        formations.niveau,
        formations.langue
    FROM formations
    LEFT JOIN cours ON cours.formation_id = formations.id
    WHERE formations.created_by = ?
";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Check if data is retrieved
if (empty($rows)) {
    // echo "No data found for user_id: $user_id. Check database.<br>"; // Uncomment for debugging
}

// Organize courses by formation
$formations = [];
foreach ($rows as $row) {
    $fid = $row['formation_id'];
    if (!isset($formations[$fid])) {
        $formations[$fid] = [
            'title' => $row['formation_title'] ?? 'No Title',
            'description' => $row['formation_description'] ?? 'No Description',
            'cours' => [],
        ];
    }
    if (!empty($row['cours_id'])) {
        $formations[$fid]['cours'][] = [
            'id' => $row['cours_id'],
            'title' => $row['cours_title'],
            'description' => $row['cours_description'],
            'video_url' => $row['video_url'],
            'document_url' => $row['document_url'],
            'created_at' => $row['created_at'],
        ];
    }
}

// Notification logic (corrected to avoid undefined $cours)
if (!empty($formations)) {
    foreach ($formations as $formation) {
        foreach ($formation['cours'] as $cours) {
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $cours['created_by']) {
                $etudiant_id = $_SESSION['user_id'];
                $formateur_id = $cours['created_by'] ?? $user_id; // Fallback to user_id if created_by is null

                // Récupérer le nom de l’étudiant
                $stmtUser = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $stmtUser->execute([$etudiant_id]);
                $etudiant = $stmtUser->fetch(PDO::FETCH_ASSOC);
                $nomEtudiant = $etudiant ? $etudiant['first_name'] . ' ' . $etudiant['last_name'] : 'Un étudiant';

                $message = "$nomEtudiant a consulté votre cours : " . ($cours['title'] ?? 'Untitled');

                $notif = $pdo->prepare("INSERT INTO notifications (sender_id, receiver_id, message) VALUES (?, ?, ?)");
                $notif->execute([$etudiant_id, $formateur_id, $message]);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>My Formations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mesformation.css">
</head>
<body>
<div class="lessons-card">
    <div class="lessons-header">My Formations & Courses</div>

    <?php if (empty($formations)): ?>
        <p style="padding: 20px;">Aucune formation ou cours trouvé. Vérifiez si des cours ont été créés pour vos formations.</p>
    <?php else: ?>
        <?php foreach ($formations as $formation): ?>
            <div class="formation-block">
                <h2><?= htmlspecialchars($formation['title']) ?></h2>
                <p><?= htmlspecialchars($formation['description']) ?></p>

                <?php if (empty($formation['cours'])): ?>
                    <p style="color: #888;">Aucun cours associé à cette formation.</p>
                <?php else: ?>
                    <ul class="lesson-list">
                        <?php foreach ($formation['cours'] as $cours): ?>
                            <li class="lesson-item">
                                <div class="lesson-icon-wrapper">
                                    <?php if (!empty($cours['video_url'])): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                                        </svg>
                                    <?php else: ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="lesson-content">
                                    <div class="lesson-title"><?= htmlspecialchars($cours['title']) ?></div>
                                    <div class="lesson-subtitle"><?= htmlspecialchars($cours['description']) ?></div>
                                    <div class="lesson-meta">
                                        <span class="lesson-tag <?= !empty($cours['video_url']) ? 'video' : 'document' ?>">
                                            <?= !empty($cours['video_url']) ? 'video' : 'document' ?>
                                        </span>
                                        <span><?= date('d/m/Y', strtotime($cours['created_at'])) ?></span>
                                    </div>
                                </div>
                                <div class="lesson-actions">
                                    <a href="modifier_cours.php?id=<?= $cours['id'] ?>" class="btn-modifier">Modifier</a>
                                    <a href="supprimer_cours.php?id=<?= $cours['id'] ?>" class="btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">Supprimer</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>console.log("My formations loaded.");</script>
</body>
</html>
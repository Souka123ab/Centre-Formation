<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/seconnecter.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['cours_id']) || !is_numeric($_GET['cours_id'])) {
    die("Cours introuvable.");
}

$cours_id = intval($_GET['cours_id']);

// Traitement du formulaire "Terminer la formation"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminer_formation']) && isset($_POST['formation_id'])) {
    $formation_id = intval($_POST['formation_id']);
    // Vérifier si déjà terminée
    $stmtCheck = $pdo->prepare("SELECT id FROM formations_terminees WHERE user_id = ? AND formation_id = ?");
    $stmtCheck->execute([$user_id, $formation_id]);
    if (!$stmtCheck->fetch()) {
        // Insérer la fin de formation
        $stmtInsert = $pdo->prepare("INSERT INTO formations_terminees (user_id, formation_id) VALUES (?, ?)");
        $stmtInsert->execute([$user_id, $formation_id]);
    }
    // Redirection pour éviter le double envoi
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Charger le cours + formation
$stmt = $pdo->prepare("
    SELECT c.*, f.title AS formation_title, f.id AS formation_id 
    FROM cours c 
    JOIN formations f ON c.formation_id = f.id 
    WHERE c.id = ?
");
$stmt->execute([$cours_id]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    die("Cours non trouvé ou formation non trouvée.");
}

// Fonction pour extraire l’ID YouTube à partir de l’URL
function extractYoutubeId($url) {
    preg_match('/(?:v=|\/embed\/|\.be\/)([^\&\?\/]+)/', $url, $matches);
    return $matches[1] ?? '';
}

// Notifier le formateur si l’utilisateur est étudiant
if ($user_id !== $cours['created_by']) {
    $etudiant_id = $user_id;
    $formateur_id = $cours['created_by'];

    $stmtUser = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmtUser->execute([$etudiant_id]);
    $etudiant = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $nomEtudiant = $etudiant ? $etudiant['first_name'] . ' ' . $etudiant['last_name'] : 'Un étudiant';

    $message = "$nomEtudiant a consulté votre cours : " . $cours['title'];

    // Enregistrer une seule fois la notification
    $notif = $pdo->prepare("INSERT INTO notifications (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $notif->execute([$etudiant_id, $formateur_id, $message]);

    // Enregistrer une seule fois la leçon consultée
    $check = $pdo->prepare("SELECT id FROM lecons_consultees WHERE user_id = ? AND cours_id = ?");
    $check->execute([$etudiant_id, $cours_id]);
    if (!$check->fetch()) {
        $insert = $pdo->prepare("INSERT INTO lecons_consultees (user_id, cours_id) VALUES (?, ?)");
        $insert->execute([$etudiant_id, $cours_id]);
    }
}

// Vérifier si la formation est déjà terminée par cet utilisateur
$stmtCheckTerminee = $pdo->prepare("SELECT id FROM formations_terminees WHERE user_id = ? AND formation_id = ?");
$stmtCheckTerminee->execute([$user_id, $cours['formation_id']]);
$dejaTerminee = $stmtCheckTerminee->fetch();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($cours['title']) ?></title>
    <link rel="stylesheet" href="../css/lessonvew.css">
    <style>
        .back-btn, .return-btn {
            display: inline-block;
            background-color: #2f80ed;
            color: white;
            padding: 10px 16px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
        }
        iframe, video, embed {
            width: 100%;
            max-width: 800px;
            height: 400px;
            margin-top: 20px;
            border: none;
        }
        .lesson-container {
            padding: 40px;
            max-width: 900px;
            margin: auto;
        }
        .btn-terminer {
            background-color: #27ae60; 
            color: white; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            margin-top: 30px;
            font-size: 16px;
        }
    </style>
</head>
<body>
<?php require_once '../include/hedear.php'; ?>

<div class="lesson-container">

    <a href="javascript:history.back()" class="back-btn">← Retour</a>

    <h1><?= htmlspecialchars($cours['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($cours['description'])) ?></p>

    <?php if (!empty($cours['youtube_url'])): ?>
        <iframe src="https://www.youtube.com/embed/<?= extractYoutubeId($cours['youtube_url']) ?>" allowfullscreen></iframe>
    <?php elseif (!empty($cours['video_url'])): ?>
        <video controls>
            <source src="../<?= htmlspecialchars($cours['video_url']) ?>" type="video/mp4">
            Votre navigateur ne supporte pas la lecture de vidéo.
        </video>
    <?php elseif (!empty($cours['document_url'])): ?>
        <embed src="../<?= htmlspecialchars($cours['document_url']) ?>" type="application/pdf">
        <a class="back-btn" href="../<?= htmlspecialchars($cours['document_url']) ?>" download>Télécharger le PDF</a>
    <?php else: ?>
        <p style="color: red;">Aucun contenu disponible pour ce cours.</p>
    <?php endif; ?>

    <!-- Bouton Terminer la formation -->
    <?php if (!$dejaTerminee): ?>
        <form method="post">
            <input type="hidden" name="formation_id" value="<?= $cours['formation_id'] ?>">
            <button type="submit" name="terminer_formation" class="btn-terminer">Terminer la formation</button>
        </form>
    <?php else: ?>
        <p style="color: green; margin-top: 30px;">Vous avez déjà terminé cette formation.</p>
    <?php endif; ?>

    <!-- Bouton retour à la formation -->
    <a href="formation-details.php?id=<?= $cours['formation_id'] ?>" class="return-btn">← Retour à la formation</a>

</div>

</body>
</html>

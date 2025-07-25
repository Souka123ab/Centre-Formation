<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '/xampp/htdocs/centre-formation/include/hedear.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/seconnecter.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get course ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erreur : ID du cours non valide.");
}
$course_id = $_GET['id'];

// Fetch course details
$stmt = $pdo->prepare("SELECT * FROM cours WHERE id = ? AND created_by = ?");
$stmt->execute([$course_id, $user_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Erreur : Cours non trouvé ou vous n'avez pas la permission de le modifier.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? $course['title'];
    $description = $_POST['description'] ?? $course['description'];
    $youtube_url = $_POST['youtube_url'] ?? $course['youtube_url'];

    $video_url = $course['video_url'];
    $document_url = $course['document_url'];

    // Handle video upload
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $video_name = time() . '_' . basename($_FILES['video_file']['name']);
        $target_dir = "../uploads/videos/";
        $target_file = $target_dir . $video_name;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
            $video_url = "uploads/videos/$video_name";
        }
    }

    // Handle PDF upload
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $pdf_name = time() . '_' . basename($_FILES['pdf_file']['name']);
        $target_dir = "../uploads/pdfs/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_dir . $pdf_name);
        $document_url = "uploads/pdfs/$pdf_name";
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE cours SET title = ?, description = ?, video_url = ?, document_url = ?, youtube_url = ? WHERE id = ? AND created_by = ?");
    $stmt->execute([$title, $description, $video_url, $document_url, $youtube_url, $course_id, $user_id]);

    header("Location: mesformation.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une leçon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/mesformation.css">
    <link rel="stylesheet" href="../css/modifier-coures.css">
</head>
<body>
    <?php require_once '/xampp/htdocs/centre-formation/include/hedear.php'; ?>
    <a href="mesformation.php" class="header-link">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
        </svg>
        Retour à mes formations
    </a>
    <div class="container">
        <div class="card">
            <div class="form-header">
                <h1>Modifier une leçon</h1>
                <p>Modifiez les détails de votre leçon</p>
            </div>
            <form method="POST" action="modifier_cours.php?id=<?= $course_id ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre de la leçon*</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($course['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Décrivez le contenu..."><?= htmlspecialchars($course['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="video-file">Fichier vidéo (optionnel)</label>
                    <input type="file" name="video_file" id="video-file" accept="video/*">
                    <?php if ($course['video_url']): ?>
                        <p>Vidéo actuelle: <a href="<?= htmlspecialchars($course['video_url']) ?>" target="_blank"><?= htmlspecialchars($course['video_url']) ?></a></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="pdf-file">Fichier PDF (optionnel)</label>
                    <input type="file" name="pdf_file" id="pdf-file" accept="application/pdf">
                    <?php if ($course['document_url']): ?>
                        <p>PDF actuel: <a href="<?= htmlspecialchars($course['document_url']) ?>" target="_blank"><?= htmlspecialchars($course['document_url']) ?></a></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="youtube_url">Lien YouTube (optionnel)</label>
                    <input type="url" id="youtube_url" name="youtube_url" value="<?= htmlspecialchars($course['youtube_url']) ?>" placeholder="https://youtube.com/...">
                </div>
                <div class="form-actions">
                    <button type="submit" class="primary-button">Enregistrer les modifications</button>
                    <a href="mesformation.php" class="secondary-button">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
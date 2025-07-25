<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $formation_id = $_POST['formation_id'] ?? '';
    $created_by = $_SESSION['user_id'] ?? 1;
    $youtube_url = $_POST['youtube_url'] ?? '';
    $duration = $_POST['duration'] ?? 30; // Default to 30 minutes if not set

    $video_url = '';
    $document_url = '';

    // Debug: Check POST data
    var_dump($_POST);

    // Validate formation_id
    if (empty($formation_id) || !is_numeric($formation_id)) {
        die("Erreur : L'ID de la formation est requis et doit être un nombre valide. Valeur reçue : " . htmlspecialchars($formation_id));
    }

    // Check if formation_id exists in formations table
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM formations WHERE id = ?");
    $stmt_check->execute([$formation_id]);
    if ($stmt_check->fetchColumn() == 0) {
        die("Erreur : L'ID de la formation $formation_id n'existe pas.");
    }

    // Upload de la vidéo with size check
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['video_file']['size'] > 100000000) { // 100MB limit
            die("Erreur : La taille du fichier dépasse la limite autorisée (100M).");
        }
        $video_name = time() . '_' . basename($_FILES['video_file']['name']);
        $target_dir = "../uploads/videos/";
        $target_file = $target_dir . $video_name;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
            $video_url = "uploads/videos/$video_name";
        } else {
            echo "Erreur lors du déplacement du fichier vidéo : " . $_FILES['video_file']['error'];
            exit;
        }
    } elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "Erreur d'upload vidéo : " . $_FILES['video_file']['error'];
        exit;
    }

    // Upload du PDF
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['pdf_file']['size'] > 100000000) { // 100MB limit
            die("Erreur : La taille du fichier dépasse la limite autorisée (100M).");
        }
        $pdf_name = time() . '_' . basename($_FILES['pdf_file']['name']);
        $target_dir = "../uploads/pdfs/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_dir . $pdf_name);
        $document_url = "uploads/pdfs/$pdf_name";
    }

    // Insertion base de données
    $stmt = $pdo->prepare("INSERT INTO cours (title, description, video_url, document_url, youtube_url, formation_id, created_by, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $video_url, $document_url, $youtube_url, $formation_id, $created_by, $duration]);

    header("Location: dasbordP.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une nouvelle leçon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/creatlesson.css">
</head>
<body>
    <?php
    require_once '/xampp/htdocs/centre-formation/include/hedear.php';
    ?>
    <a href="../professeur/dasbordP.php" class="header-link">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
        </svg>
        Retour au tableau de bord
    </a>
    <div class="container">
        <div class="card">
            <div class="form-header">
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"></path>
                    </svg>
                </div>
                <h1>Créer une nouvelle leçon</h1>
                <p>Ajoutez du contenu pédagogique à vos formations</p>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Type de contenu</label>
                    <div class="content-type-selection">
                        <button type="button" class="content-type-button active" data-type="video">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"></path>
                            </svg>
                            Vidéo
                        </button>
                        <button type="button" class="content-type-button" data-type="pdf">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25m-9-9h.375c.621 0 1.125.504 1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125H15.75m-4.5 0h3.75m-3.75 0V1.5m0 1.5H12"></path>
                            </svg>
                            PDF
                        </button>
                        <button type="button" class="content-type-button" data-type="youtube">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"></path>
                            </svg>
                            YouTube
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formation_id">Formation</label>
                    <select name="formation_id" id="formation_id" required>
                        <option value="">-- Sélectionnez une formation --</option>
                        <?php
                        require_once '../config/database.php';
                        $stmt = $pdo->query("SELECT id, title FROM formations");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['title']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group grid">
                    <div>
                        <label for="lesson-title">Titre de la leçon*</label>
                        <input type="text" id="lesson-title" name="title" required>
                    </div>
                </div>

                <div class="form-group description-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Décrivez le contenu et les objectifs de cette leçon..."></textarea>
                    <svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14.25v4.75a2 2 0 01-2 2H5.25a2 2 0 01-2-2V7.5a2 2 0 012-2h4.75"></path>
                    </svg>
                </div>

                <!-- Pour vidéo -->
                <div class="form-group" id="video-upload" style="display: none;">
                    <label for="video-file">Fichier vidéo</label>
                    <div class="file-upload-area">
                        <button type="button" class="upload-button">Choisir un fichier</button>
                        <input type="file" name="video_file" id="video-file" accept="video/*" style="display: none;">
                    </div>
                </div>

                <!-- Pour PDF -->
                <div class="form-group" id="pdf-upload" style="display: none;">
                    <label for="pdf-file">Fichier PDF</label>
                    <input type="file" name="pdf_file" id="pdf-file" accept="application/pdf">
                </div>

                <!-- Pour YouTube -->
                <div class="form-group" id="youtube-input" style="display: none;">
                    <label for="youtube-url">Lien YouTube</label>
                    <input type="url" name="youtube_url" id="youtube-url" placeholder="https://youtube.com/..." />
                </div>

                <div class="form-group" id="duration-group">
                    <label for="estimated-duration">Durée estimée (minutes)</label>
                    <input type="number" id="estimated-duration" name="duration" value="30">
                </div>

                <div class="form-actions">
                    <button type="submit" class="primary-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" style="width: 18px; height: 18px; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"></path>
                        </svg>
                        Créer la leçon
                    </button>
                    <button type="button" class="secondary-button">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    <script src="creatlesson.js"></script>
</body>
</html>
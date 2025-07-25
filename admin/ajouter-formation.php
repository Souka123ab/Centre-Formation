<?php
session_start();
require_once '../config/database.php';

// Vérifier que l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login-admin.php');
    exit;
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $niveau = trim($_POST['niveau']);
    $langue = trim($_POST['langue']);
    $created_by = $_SESSION['admin_id'];  // <-- ici la bonne clé session

    if ($title && $description && $niveau && $langue) {
        $stmt = $pdo->prepare("INSERT INTO formations (title, description, niveau, langue, created_by) 
                               VALUES (?, ?, ?, ?, ?)");
        $success = $stmt->execute([$title, $description, $niveau, $langue, $created_by]);

        $message = $success ? "✅ Formation ajoutée avec succès." : "❌ Erreur lors de l'ajout de la formation.";
    } else {
        $message = "⚠️ Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Formation</title>
    <link rel="stylesheet" href="../css/form.css">
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
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--bg-body);
    color: var(--text-main);
    line-height: 1.6;
}

.container {
    max-width: 600px;
    margin: 50px auto;
    background-color: var(--card-bg);
    padding: 30px 40px;
    box-shadow: 0 4px 12px var(--shadow-soft);
    border: 1px solid var(--border-card);
    border-radius: 10px;
}

h1 {
    text-align: center;
    color: var(--section-title);
    margin-bottom: 30px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-main);
}

input[type="text"],
select,
textarea {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 20px;
    border: 1px solid var(--border-card);
    border-radius: 6px;
    font-size: 1rem;
    color: var(--text-main);
    background-color: #fff;
    transition: border-color 0.3s ease;
}

input[type="text"]:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: var(--icon-text-purple);
    box-shadow: 0 0 5px var(--icon-text-purple);
}

button {
    width: 100%;
    padding: 15px;
    background-color: var(--icon-text-purple);
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #7c3aed; /* un violet un peu plus foncé */
}

.alert {
    padding: 15px 20px;
    margin-bottom: 25px;
    border-radius: 6px;
    font-weight: 600;
    color: white;
    background-color: var(--icon-bg-red);
    border: 1px solid #d32f2f;
    text-align: center;
}

.alert:empty {
    display: none;
}

/* Pour textarea taille minimum */
textarea {
    min-height: 100px;
    resize: vertical;
}

</style>
<body>
    <?php require_once '../include/hedear.php'; ?>
 <a href="javascript:history.back()" class="back-arrow">← Retour</a>
    <div class="container">
        <h1>Ajouter une nouvelle formation</h1>

        <?php if ($message): ?>
            <div class="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="title">Titre :</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description :</label>
            <textarea name="description" id="description" rows="4" required></textarea>

            <label for="niveau">Niveau :</label>
            <select name="niveau" id="niveau" required>
                <option value="">-- Sélectionnez --</option>
                <option value="Débutant">Débutant</option>
                <option value="Intermédiaire">Intermédiaire</option>
                <option value="Avancé">Avancé</option>
            </select>

            <label for="langue">Langue :</label>
            <input type="text" name="langue" id="langue" required placeholder="ex : Français, Anglais...">

            <button type="submit">Ajouter la formation</button>
        </form>
    </div>
</body>
</html>

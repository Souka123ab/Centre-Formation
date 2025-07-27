<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

function emptyToNull($value) {
    return trim($value) === '' ? null : $value;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = emptyToNull($_POST['specialization'] ?? null);
    $experience = emptyToNull($_POST['experience'] ?? null);
    $education = emptyToNull($_POST['education'] ?? null);
    $certifications = emptyToNull($_POST['certifications'] ?? null);
    $previous_experience = emptyToNull($_POST['previous_experience'] ?? null);
    $presentation = emptyToNull($_POST['presentation'] ?? null);

    $sql = "UPDATE users SET specialization = ?, experience = ?, education = ?, certifications = ?, previous_experience = ?, presentation = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$specialization, $experience, $education, $certifications, $previous_experience, $presentation, $user_id]);

    header("Location: profil.php");
    exit();
}

// Récupérer les données pour pré-remplir le formulaire
$sql = "SELECT specialization, experience, education, certifications, previous_experience, presentation FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <style>
        :root {
            --bg-body: #f2f5ff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --card-bg: white;
            --btn-bg: #2563eb;
            --btn-bg-hover: #1e40af;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-body);
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: var(--text-main);
        }

        form label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: var(--text-main);
        }

        form input[type="text"],
        form input[type="number"],
        form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            display: inline-block;
            background-color: var(--btn-bg);
            color: #fff;
            padding: 10px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: var(--btn-bg-hover);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: var(--text-muted);
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Modifier le Profil</h2>
    <form method="post">
        <label for="specialization">Spécialisation</label>
        <input type="text" name="specialization" id="specialization" value="<?= htmlspecialchars($user['specialization'] ?? '') ?>">

        <label for="experience">Expérience (années)</label>
        <input type="number" name="experience" id="experience" value="<?= htmlspecialchars($user['experience'] ?? '') ?>">

        <label for="education">Éducation</label>
        <input type="text" name="education" id="education" value="<?= htmlspecialchars($user['education'] ?? '') ?>">

        <label for="certifications">Certifications</label>
        <input type="text" name="certifications" id="certifications" value="<?= htmlspecialchars($user['certifications'] ?? '') ?>">

        <label for="previous_experience">Expérience précédente</label>
        <textarea name="previous_experience" id="previous_experience"><?= htmlspecialchars($user['previous_experience'] ?? '') ?></textarea>

        <label for="presentation">Présentation</label>
        <textarea name="presentation" id="presentation"><?= htmlspecialchars($user['presentation'] ?? '') ?></textarea>

        <button type="submit" class="btn-submit">Enregistrer</button>
    </form>
    <a href="profil.php" class="back-link">← Retour au profil</a>
</div>
</body>
</html>

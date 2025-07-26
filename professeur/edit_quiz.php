<?php
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Quiz ID invalide.");
}

$quiz_id = (int)$_GET['id'];
$error = '';
$success = '';

// Charger le quiz
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quiz) {
    die("Quiz introuvable.");
}

// Charger les questions + options
$questions_stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$questions_stmt->execute([$quiz_id]);
$questions = $questions_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($questions as &$question) {
    $options_stmt = $pdo->prepare("SELECT * FROM options WHERE question_id = ?");
    $options_stmt->execute([$question['id']]);
    $question['options'] = $options_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Vérifier et nettoyer les champs principaux
        $new_title = isset($_POST['quiz_title']) ? trim($_POST['quiz_title']) : '';
        $new_desc = isset($_POST['quiz_description']) ? trim($_POST['quiz_description']) : '';

        if ($new_title === '') {
            throw new Exception("Le titre du quiz est requis.");
        }

        // Mise à jour du quiz
        $update_quiz = $pdo->prepare("UPDATE quizzes SET title = ?, description = ? WHERE id = ?");
        $update_quiz->execute([$new_title, $new_desc, $quiz_id]);

        // Vérification des questions
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            foreach ($_POST['questions'] as $q_id => $q_data) {
                $question_text = isset($q_data['question']) ? trim($q_data['question']) : '';

                if ($question_text === '') {
                    throw new Exception("Le texte de la question ne peut pas être vide.");
                }

                $update_question = $pdo->prepare("UPDATE questions SET question_text = ? WHERE id = ?");
                $update_question->execute([$question_text, $q_id]);

                if (isset($q_data['options']) && is_array($q_data['options'])) {
                    foreach ($q_data['options'] as $opt_id => $opt_text) {
                        $is_correct = (isset($q_data['correct_option']) && $q_data['correct_option'] == $opt_id) ? 1 : 0;
                        $update_option = $pdo->prepare("UPDATE options SET option_text = ?, is_correct = ? WHERE id = ?");
                        $update_option->execute([trim($opt_text), $is_correct, $opt_id]);
                    }
                }
            }
        }

        $pdo->commit();
        $success = "Quiz mis à jour avec succès.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Quiz</title>
    <style>
:root {
    --bg-body: #f2f5ff;
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

body {
    font-family: 'Segoe UI', sans-serif;
    background-color: var(--bg-body);
    color: var(--text-main);
 
}

h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--section-title);
}

a {
    text-decoration: none;
    color: var(--section-text);
    font-weight: bold;
}

form {
    max-width: 900px;
    margin: auto;
    background-color: var(--card-bg);
    border: 1px solid var(--border-card);
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 6px var(--shadow-soft);
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
    color: var(--text-main);
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border-card);
    border-radius: 5px;
    margin-bottom: 15px;
    background-color: #fff;
    color: var(--text-main);
}

.question-block {
    margin-top: 30px;
    padding: 20px;
    background-color: var(--icon-bg-blue);
    border-left: 5px solid var(--icon-text-blue);
    border-radius: 6px;
}

.option-block {
    margin-top: 10px;
    padding-left: 10px;
}

.option-block input[type="radio"] {
    margin-right: 10px;
}

.success {
    color: var(--icon-text-green);
    margin-bottom: 20px;
}

.error {
    color: var(--icon-bg-red);
    margin-bottom: 20px;
}

.submit-btn {
    background-color: var(--icon-text-blue);
    color: white;
    padding: 10px 20px;
    border: none;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: var(--section-title);
}

    </style>
</head>
<body>
    <?php require_once '../include/hedear.php'; ?>
    <h1>Modifier Quiz</h1>
    <a href="view_quizzes.php">← Retour à la liste</a>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Titre du quiz</label>
        <input type="text" name="quiz_title" value="<?= htmlspecialchars($quiz['title']) ?>" required>

        <label>Description</label>
        <input type="text" name="quiz_description" value="<?= htmlspecialchars($quiz['description']) ?>">

        <?php foreach ($questions as $q): ?>
            <div class="question-block">
                <label>Question</label>
                <input type="text" name="questions[<?= $q['id'] ?>][question]" value="<?= htmlspecialchars($q['question_text']) ?>" required>

                <div class="option-block">
                    <?php foreach ($q['options'] as $opt): ?>
                        <label>
                            <input type="radio" name="questions[<?= $q['id'] ?>][correct_option]" value="<?= $opt['id'] ?>" <?= $opt['is_correct'] ? 'checked' : '' ?>>
                            <input type="text" name="questions[<?= $q['id'] ?>][options][<?= $opt['id'] ?>]" value="<?= htmlspecialchars($opt['option_text']) ?>" required>
                            <?= $opt['is_correct'] ? '<span style="color:green;">(Bonne réponse)</span>' : '' ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="submit-btn">Enregistrer</button>
    </form>

</body>
</html>

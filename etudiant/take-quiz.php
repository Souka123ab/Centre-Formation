<?php
session_start();
require_once '../config/database.php';

$quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$score = 0;
$total_questions = 0;

try {
    // Charger les infos du quiz
    $stmt = $pdo->prepare("SELECT title, description FROM quizzes WHERE id = ?");
    $stmt->execute([$quiz_id]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        die("Quiz non trouvé.");
    }

    // Charger les questions + options
    $stmt = $pdo->prepare("SELECT q.id AS question_id, q.question_text, o.option_text, o.is_correct 
                           FROM questions q 
                           LEFT JOIN options o ON q.id = o.question_id 
                           WHERE q.quiz_id = ? 
                           ORDER BY q.id, o.id");
    $stmt->execute([$quiz_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organiser les données par question
    $questions = [];
    foreach ($results as $row) {
        $qid = $row['question_id'];
        if (!isset($questions[$qid])) {
            $questions[$qid] = [
                'question_text' => $row['question_text'],
                'options' => [],
                'correct_answer' => null
            ];
        }
        $questions[$qid]['options'][] = $row['option_text'];
        if ($row['is_correct']) {
            $questions[$qid]['correct_answer'] = $row['option_text'];
        }
    }

    // Calcul du score si formulaire soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
        $answers = $_POST['answers'];
        foreach ($questions as $qid => $question) {
            $total_questions++;
            if (isset($answers[$qid]) && $answers[$qid] === $question['correct_answer']) {
                $score++;
            }
        }
    }

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <style>
        body {
            background-color: #f9fafb;
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        h2 { color: #1e3a8a; }
        p { color: #1d4ed8; }
        .question { margin-bottom: 20px; }
        .back-button, input[type="submit"], .finish-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #f3f4f6;
            color: #1f2937;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .back-button:hover, input[type="submit"]:hover, .finish-button:hover {
            background-color: #d1d5db;
        }

        .correct { color: green; }
        .incorrect { color: red; }
    </style>
</head>
<body>
    <?php include '../include/hedear.php'; ?>
    <div class="container">
        <a href="quizzes.php" class="back-button">← Retour</a>
        <h2><?= htmlspecialchars($quiz['title']) ?></h2>
        <p><?= htmlspecialchars($quiz['description']) ?></p>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <h3>Votre score : <?= $score ?> / <?= $total_questions ?></h3>
        <?php endif; ?>

        <?php if ($questions): ?>
            <form method="POST">
                <?php foreach ($questions as $qid => $q): ?>
                    <div class="question">
                        <p><?= $qid ?>. <?= htmlspecialchars($q['question_text']) ?></p>

                        <?php
                        $user_answer = $_POST['answers'][$qid] ?? '';
                        foreach ($q['options'] as $option):
                            $is_correct = $q['correct_answer'] === $option;
                            $is_selected = $user_answer === $option;
                            $style = '';

                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                if ($is_selected && $is_correct) $style = 'correct';
                                elseif ($is_selected && !$is_correct) $style = 'incorrect';
                                elseif ($is_correct) $style = 'correct';
                            }
                        ?>
                        <label class="<?= $style ?>">
                            <input type="radio" name="answers[<?= $qid ?>]" value="<?= htmlspecialchars($option) ?>"
                                <?= $is_selected ? 'checked' : '' ?> required>
                            <?= htmlspecialchars($option) ?>
                        </label><br>
                        <?php endforeach; ?>

                        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_answer !== $q['correct_answer']): ?>
                            <p class="incorrect">❌ Mauvaise réponse</p>
                            <p class="correct">✅ Réponse correcte : <?= htmlspecialchars($q['correct_answer']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <a href="quizzes-complet.php" class="finish-button">🎉 Terminer</a>
                <?php else: ?>
                    <input type="submit" value="Soumettre">
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p>Pas de questions disponibles pour ce quiz.</p>
        <?php endif; ?>
    </div>
</body>
</html>

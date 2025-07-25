<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de quiz invalide.");
}

$quiz_id = intval($_GET['id']);

// Récupérer le quiz (titre, description)
$stmt = $pdo->prepare("SELECT id, title FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quiz) {
    die("Quiz introuvable.");
}

// Récupérer les questions et leurs choix
$stmt = $pdo->prepare("SELECT q.id AS question_id, q.question_text, c.id AS choice_id, c.choice_text FROM questions q JOIN choices c ON q.id = c.question_id WHERE q.quiz_id = ? ORDER BY q.id, c.id");
$stmt->execute([$quiz_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les données par question
$questions = [];
foreach ($rows as $row) {
    $qid = $row['question_id'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'question_text' => $row['question_text'],
            'choices' => []
        ];
    }
    $questions[$qid]['choices'][$row['choice_id']] = $row['choice_text'];
}

// Si formulaire soumis, calculer le score
$score = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les réponses choisies
    $answers = $_POST['answers'] ?? [];
    $total = count($questions);
    $correct_count = 0;

    // Pour chaque question, vérifier la bonne réponse
    foreach ($questions as $qid => $qdata) {
        $selected_choice = $answers[$qid] ?? null;
        if ($selected_choice) {
            $stmt = $pdo->prepare("SELECT is_correct FROM choices WHERE id = ?");
            $stmt->execute([$selected_choice]);
            $is_correct = $stmt->fetchColumn();
            if ($is_correct) {
                $correct_count++;
            }
        }
    }
    $score = "$correct_count / $total";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Passer le Quiz : <?= htmlspecialchars($quiz['title']) ?></title>
</head>
<body>
    <h1>Quiz : <?= htmlspecialchars($quiz['title']) ?></h1>

    <?php if ($score !== null): ?>
        <h2>Résultat : <?= $score ?></h2>
        <a href="quizzes.php">Retour à la liste des quizzes</a>
        <hr>
    <?php else: ?>
        <form method="post">
            <?php foreach ($questions as $qid => $qdata): ?>
                <fieldset>
                    <legend><?= htmlspecialchars($qdata['question_text']) ?></legend>
                    <?php foreach ($qdata['choices'] as $cid => $choice_text): ?>
                        <label>
                            <input type="radio" name="answers[<?= $qid ?>]" value="<?= $cid ?>" required>
                            <?= htmlspecialchars($choice_text) ?>
                        </label><br>
                    <?php endforeach; ?>
                </fieldset>
                <br>
            <?php endforeach; ?>

           <a href="quiz-start.php">Commencer le quiz</a>
            <button type="submit">Soumettre</button>
        </form>
    <?php endif; ?>

</body>
</html>

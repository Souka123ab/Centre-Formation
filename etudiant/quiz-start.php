<?php
session_start();
require_once '../config/database.php';

echo '<pre>';
print_r($_GET);
echo '</pre>';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de quiz invalide.");
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'] ?? 1; // Tbadl b l-user ID l-7aqiqi

// Récupérer le quiz
$stmt = $pdo->prepare("SELECT id, title FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quiz) {
    die("Quiz introuvable.");
}

// Récupérer questions et choix
$stmt = $pdo->prepare("
    SELECT q.id AS question_id, q.question_text, c.id AS choice_id, c.choice_text, c.is_correct
    FROM questions q
    JOIN choices c ON q.id = c.question_id
    WHERE q.quiz_id = ?
    ORDER BY q.id, c.id
");
$stmt->execute([$quiz_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$questions = [];
foreach ($rows as $row) {
    $qid = $row['question_id'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'question_text' => $row['question_text'],
            'choices' => []
        ];
    }
    $questions[$qid]['choices'][$row['choice_id']] = [
        'text' => $row['choice_text'],
        'is_correct' => $row['is_correct']
    ];
}

$score = null;
$results = []; // Bach n7ot l-result ta3 kol sual
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? [];
    $total = count($questions);
    $correct_count = 0;

    foreach ($questions as $qid => $qdata) {
        $selected_choice = $answers[$qid] ?? null;
        $correct = false;
        if ($selected_choice && isset($qdata['choices'][$selected_choice])) {
            if ($qdata['choices'][$selected_choice]['is_correct']) {
                $correct_count++;
                $correct = true;
            }
        }
        $results[$qid] = $correct;
    }
    $score = ($correct_count / $total) * 100; // Score b pourcentage

    // T2bl l-score f user_quizzes
    $stmt = $pdo->prepare("INSERT INTO user_quizzes (user_id, quiz_id, completed, score) VALUES (?, ?, 1, ?) 
                           ON DUPLICATE KEY UPDATE completed = 1, score = ?");
    $stmt->execute([$user_id, $quiz_id, $score, $score]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Quiz : <?= htmlspecialchars($quiz['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        fieldset { margin-bottom: 1.5rem; }
        legend { font-weight: bold; }
        button { padding: 0.5rem 1rem; font-size: 1rem; cursor: pointer; background-color: #4CAF50; color: white; border: none; border-radius: 4px; }
        button:hover { background-color: #45a049; }
        .score { font-size: 1.2rem; font-weight: bold; color: green; margin-bottom: 1rem; }
        .result { margin-left: 1rem; font-style: italic; }
        .correct { color: green; }
        .incorrect { color: red; }
        a { display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background-color: #008CBA; color: white; text-decoration: none; border-radius: 4px; }
        a:hover { background-color: #007B9A; }
    </style>
</head>
<body>

<h1>Quiz : <?= htmlspecialchars($quiz['title']) ?></h1>

<?php if ($score !== null): ?>
    <div class="score">Votre score : <?= number_format($score, 2) ?>%</div>
    <?php foreach ($questions as $qid => $qdata): ?>
        <fieldset>
            <legend><?= htmlspecialchars($qdata['question_text']) ?></legend>
            <?php foreach ($qdata['choices'] as $cid => $choice): ?>
                <label>
                    <input type="radio" name="answers[<?= $qid ?>]" value="<?= $cid ?>" 
                           <?= isset($answers[$qid]) && $answers[$qid] == $cid ? 'checked' : '' ?> disabled>
                    <?= htmlspecialchars($choice['text']) ?>
                </label>
                <?php if (isset($answers[$qid]) && $answers[$qid] == $cid): ?>
                    <span class="result <?= $results[$qid] ? 'correct' : 'incorrect' ?>">
                        <?= $results[$qid] ? ' (Correct !)' : ' (Incorrect !)' ?>
                    </span>
                <?php endif; ?>
                <br>
            <?php endforeach; ?>
        </fieldset>
    <?php endforeach; ?>
    <a href="quizzes-complet.php">Voir les quizzes complétés</a>
<?php else: ?>
    <form method="post">
        <?php foreach ($questions as $qid => $qdata): ?>
            <fieldset>
                <legend><?= htmlspecialchars($qdata['question_text']) ?></legend>
                <?php foreach ($qdata['choices'] as $cid => $choice): ?>
                    <label>
                        <input type="radio" name="answers[<?= $qid ?>]" value="<?= $cid ?>" required>
                        <?= htmlspecialchars($choice['text']) ?>
                    </label><br>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>
        <button type="submit">Soumettre</button>
    </form>
<?php endif; ?>

</body>
</html>
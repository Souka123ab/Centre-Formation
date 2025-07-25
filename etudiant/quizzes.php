<?php
require_once '../config/database.php'; // ton fichier de connexion PDO

try {
    $stmt = $pdo->query("SELECT quizzes.id, quizzes.title, quizzes.description, quizzes.created_at, 
        COALESCE(CONCAT(users.first_name, ' ', users.last_name), 'Inconnu') AS author_name,
        (SELECT COUNT(*) FROM questions WHERE questions.quiz_id = quizzes.id) AS question_count
        FROM quizzes
        LEFT JOIN users ON users.id = quizzes.created_by
        ORDER BY quizzes.created_at DESC");

    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($quizzes) {
        // echo "<pre>";
        // print_r($quizzes);
        // echo "</pre>";
    } else {
        echo "Pas de quizzes disponibles. Vérifiez les tables ou les données.";
    }
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Quizzes</title>
    <link rel="stylesheet" href="../css/quizetu.css">
</head>
<style>
    .quiz-container { padding: 20px; }
    .quiz-heading { font-size: 24px; }
    .quiz-item { border: 1px solid #ccc; margin-bottom: 10px; padding: 10px; }
</style>
<body>
    <?php require_once '../include/hedear.php'; ?>
    <div class="quiz-container">
        <h2 class="quiz-heading">Available Quizzes</h2>
        <div class="quiz-list">
           <?php foreach ($quizzes as $quiz): ?>
    <div class="quiz-item">
        <div class="quiz-icon">
            <!-- Nouvelle icône représentant un quiz -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" 
                 stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-list">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                <rect width="8" height="4" x="8" y="2" rx="1"/>
                <path d="M12 11h4M12 16h4"/>
                <path d="M8 11h.01M8 16h.01"/>
            </svg>
        </div>
        <div class="quiz-details">
            <div class="quiz-title"><?= htmlspecialchars($quiz['title'] ?? 'Sans titre') ?></div>
            <div class="quiz-description"><?= htmlspecialchars($quiz['description'] ?? 'Pas de description') ?></div>
            <div class="quiz-meta">
                <span class="quiz-questions"><?= $quiz['question_count'] ?> questions</span>

                <span class="quiz-author">
                    <!-- Icône auteur changée -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M16 20a4 4 0 0 0-8 0"/>
                    </svg>
                    By <?= htmlspecialchars($quiz['author_name']) ?>
                </span>

                <span class="quiz-date">
                    <!-- Icône date changée -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" 
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check">
                        <path d="M8 2v2"/>
                        <path d="M16 2v2"/>
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <path d="m9 13 2 2 4-4"/>
                    </svg>
                    <?= date('d/m/Y', strtotime($quiz['created_at'])) ?>
                </span>
            </div>
        </div>
        <a href="take-quiz.php?id=<?= $quiz['id'] ?>" class="take-quiz-button">Take Quiz</a>
    </div>
<?php endforeach; ?>

        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const takeQuizButtons = document.querySelectorAll(".take-quiz-button");
            takeQuizButtons.forEach((button) => {
                button.addEventListener("click", (event) => {
                    const quizItem = event.target.closest(".quiz-item");
                    const quizTitle = quizItem.querySelector(".quiz-title").textContent;
                    // alert(`You clicked "Take Quiz" for: ${quizTitle}`);
                    console.log(`Quiz "${quizTitle}" button clicked!`);
                });
            });
        });
    </script>
</body>
</html>
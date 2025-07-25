<?php
// Include database connection file
session_start(); // Add this line
require_once '../config/database.php';

try {
    // Check if $pdo is set
    if (!isset($pdo)) {
        throw new Exception("Database connection not established.");
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/loginprofesseur.php');
        exit;
    }
    $user_id = $_SESSION['user_id'];

    // Fetch quizzes created by the logged-in user
    $quiz_stmt = $pdo->prepare("SELECT * FROM quizzes WHERE created_by = ? ORDER BY created_at DESC");
    $quiz_stmt->execute([$user_id]);
    $quizzes = $quiz_stmt->fetchAll();
} catch (\PDOException | Exception $e) {
    $error_message = "Error fetching quizzes: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quizzes</title>
    <link rel="stylesheet" href="../css/add-quize.css">
    <style>
        .quiz-list { margin: 20px 0; }
        .quiz-item { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .question-item { margin-left: 20px; margin-top: 10px; }
        .option-item { margin-left: 40px; color: #555; }
        .error-message { color: red; padding: 10px; }
        .modify-btn { background-color: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
        .modify-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <?php require_once '/xampp/htdocs/centre-formation/include/hedear.php'; ?>
    
    <div class="container">
        <header class="header">
            <h1>View Quizzes</h1>
            <a href="dasbordP.php" class="back-link">← Back to Dashboard</a>
        </header>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="quiz-list">
            <?php if (!empty($quizzes)): ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="quiz-item">
                        <h2><?php echo htmlspecialchars($quiz['title'] ?? 'Untitled Quiz'); ?></h2>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($quiz['description'] ?? 'No description'); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($quiz['created_at'] ?? 'N/A'); ?></p>
                        <button class="modify-btn" onclick="window.location.href='edit_quiz.php?id=<?php echo $quiz['id']; ?>'">Modify</button>
                        <form action="delete_quiz.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                            <button type="submit" class="modify-btn" style="background-color: red;">Delete</button>
                        </form>

                        <?php
                        // Fetch questions for this quiz
                        $question_stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
                        $question_stmt->execute([$quiz['id']]);
                        $questions = $question_stmt->fetchAll();
                        ?>

                        <?php if (!empty($questions)): ?>
                            <?php foreach ($questions as $question): ?>
                                <div class="question-item">
                                    <h3>Question: <?php echo htmlspecialchars($question['question_text']); ?></h3>
                                    
                                    <?php
                                    // Fetch options for this question
                                    $option_stmt = $pdo->prepare("SELECT * FROM options WHERE question_id = ? ORDER BY id ASC");
                                    $option_stmt->execute([$question['id']]);
                                    $options = $option_stmt->fetchAll();
                                    ?>

                                    <?php if (!empty($options)): ?>
                                        <?php foreach ($options as $option): ?>
                                            <div class="option-item">
                                                <span><?php echo htmlspecialchars($option['option_text']); ?></span>
                                                <?php if ($option['is_correct']): ?>
                                                    <span style="color: green;">(Correct)</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="option-item">No options available.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No questions available for this quiz.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No quizzes available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
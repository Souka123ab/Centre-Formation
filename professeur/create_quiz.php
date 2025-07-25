<?php
// Include database connection file (using PDO)
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction (PDO style)
    $pdo->beginTransaction();
    
    try {
        // Insert quiz details
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 1; // Default course_id
        $title = $pdo->quote($_POST['quiz_title']); // PDO equivalent of real_escape_string
        $description = $pdo->quote($_POST['quiz_description']);
        
        $quiz_sql = "INSERT INTO quizzes (course_id, title, description) VALUES (:course_id, :title, :description)";
        $quiz_stmt = $pdo->prepare($quiz_sql);
        $quiz_stmt->execute([
            ':course_id' => $course_id,
            ':title' => $title,
            ':description' => $description
        ]);
        $quiz_id = $pdo->lastInsertId();
        
        // Insert questions and options
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            foreach ($_POST['questions'] as $index => $question_text) {
                if (!empty($question_text)) {
                    $question_text = $pdo->quote(trim($question_text));
                    $question_sql = "INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)";
                    $question_stmt = $pdo->prepare($question_sql);
                    $question_stmt->execute([
                        ':quiz_id' => $quiz_id,
                        ':question_text' => $question_text
                    ]);
                    $question_id = $pdo->lastInsertId();

                    if (isset($_POST['options'][$index]) && isset($_POST['correct_answer'][$index])) {
                        $correct_answer = intval($_POST['correct_answer'][$index]);
                        foreach ($_POST['options'][$index] as $opt_index => $option_text) {
                            if (!empty($option_text)) {
                                $option_text = $pdo->quote(trim($option_text));
                                $is_correct = ($opt_index + 1 == $correct_answer) ? 1 : 0;
                                $option_sql = "INSERT INTO options (question_id, option_text, is_correct) VALUES (:question_id, :option_text, :is_correct)";
                                $option_stmt = $pdo->prepare($option_sql);
                                $option_stmt->execute([
                                    ':question_id' => $question_id,
                                    ':option_text' => $option_text,
                                    ':is_correct' => $is_correct
                                ]);
                            }
                        }
                    }
                }
            }
        }
        
        // Commit transaction
        $pdo->commit();
        header("Location: dasbordP.php?success=Quiz created successfully");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $error_message = "Error creating quiz: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz</title>
    <link rel="stylesheet" href="../css/add-quize.css">
    <style>
        .preview-section { margin-top: 20px; }
        .preview-quiz { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .preview-question { margin-left: 20px; margin-top: 10px; }
        .preview-option { margin-left: 40px; color: #555; }
        .preview-option.correct { color: green; }
    </style>
</head>
<body>
    <?php require_once '/xampp/htdocs/centre-formation/include/hedear.php'; ?>
    
    <div class="container">
        <header class="header">
            <h1>Create New Quiz</h1>
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </header>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="create_quiz.php" id="quiz-form">
            <div class="card">
                <div class="card-header">
                    <span class="icon">❓</span>
                    <h2>Quiz Details</h2>
                </div>
                <p class="description-text">Create an interactive quiz for your students with multiple choice questions.</p>
                <div class="form-group-row">
                    <div class="form-group">
                        <label for="quiz-title">Quiz Title</label>
                        <input type="text" id="quiz-title" name="quiz_title" placeholder="Enter quiz title" required>
                    </div>
                    <div class="form-group description-group">
                        <label for="quiz-description">Description</label>
                        <textarea id="quiz-description" name="quiz_description" placeholder="Describe what this quiz covers"></textarea>
                        <span class="resize-handle"></span>
                    </div>
                </div>
            </div>

            <div class="card questions-section">
                <div class="card-header">
                    <h2>Questions</h2>
                    <button type="button" class="add-question-btn">+ Add Question</button>
                </div>
                <div id="questions-container">
                    <!-- Initial block will be added by JavaScript -->
                </div>
            </div>

            <div class="footer-buttons">
                <button type="button" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary"><span class="icon-left">❓</span> Create Quiz</button>
            </div>
        </form>

        <div class="preview-section">
            <h2>Preview</h2>
            <div id="quiz-preview" class="preview-quiz">
                <!-- Preview will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script src="add-quiz.js"></script>
    <script>
        let questionCount = 0;

        function createQuestionBlock(number) {
            const div = document.createElement('div');
            div.classList.add('question-block');
            div.dataset.questionNumber = number;

            div.innerHTML = `
                <h3>Question <span class="question-number">${number + 1}</span></h3>
                <div class="form-group">
                    <label for="question-text-${number}">Question</label>
                    <input type="text" id="question-text-${number}" name="questions[]" placeholder="Enter your question" required>
                </div>
                <div class="answer-options">
                    <h4>Answer Options</h4>
                    ${[1, 2, 3, 4].map(i => `
                        <div class="option-group">
                            <input type="radio" id="option-${number}-${i}" name="correct_answer[${number}]" value="${i}" ${i === 1 ? 'checked' : ''}>
                            <label for="option-${number}-${i}">Option ${i}</label>
                            <input type="text" class="option-input" data-index="${i - 1}" name="options[${number}][]" placeholder="Option ${i}" required>
                        </div>
                    `).join('')}
                    <p class="hint">Select the radio button next to the correct answer</p>
                </div>
            `;
            return div;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('questions-container');
            const preview = document.getElementById('quiz-preview');
            
            // Add the first question block only if the container is empty
            if (container.children.length === 0) {
                container.appendChild(createQuestionBlock(questionCount));
                questionCount++;
            }

            const addButton = document.querySelector('.add-question-btn');
            if (addButton) {
                addButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    container.appendChild(createQuestionBlock(questionCount));
                    questionCount++;
                    updatePreview();
                });
            } else {
                console.error('Add Question button not found. Check the class name.');
            }

            // Update preview on input change
            document.addEventListener('input', updatePreview);
            document.addEventListener('change', updatePreview); // For radio buttons
        });

        function updatePreview() {
            const preview = document.getElementById('quiz-preview');
            preview.innerHTML = ''; // Clear previous preview

            const title = document.getElementById('quiz-title')?.value || 'Untitled Quiz';
            const description = document.getElementById('quiz-description')?.value || 'No description';
            const createdAt = new Date().toLocaleString(); // Simulate creation date

            const previewQuiz = document.createElement('div');
            previewQuiz.classList.add('preview-quiz');
            previewQuiz.innerHTML = `
                <h2>${htmlspecialchars(title)}</h2>
                <p><strong>Description:</strong> ${htmlspecialchars(description)}</p>
                <p><strong>Created At:</strong> ${htmlspecialchars(createdAt)}</p>
            `;
            preview.appendChild(previewQuiz);

            const questionBlocks = document.querySelectorAll('.question-block');
            questionBlocks.forEach((block, index) => {
                const questionText = block.querySelector(`#question-text-${index}`)?.value || `Question ${index + 1} (Untitled)`;
                const correctAnswer = parseInt(block.querySelector(`input[name="correct_answer[${index}]"]:checked`)?.value) || 1;
                const options = Array.from(block.querySelectorAll('.option-input')).map(input => input.value || `Option ${input.dataset.index + 1}`);

                const previewQuestion = document.createElement('div');
                previewQuestion.classList.add('preview-question');
                previewQuestion.innerHTML = `<h3>Question: ${htmlspecialchars(questionText)}</h3>`;

                options.forEach((option, optIndex) => {
                    const previewOption = document.createElement('div');
                    previewOption.classList.add('preview-option');
                    if (optIndex + 1 === correctAnswer) previewOption.classList.add('correct');
                    previewOption.textContent = htmlspecialchars(option) + (optIndex + 1 === correctAnswer ? ' (Correct)' : '');
                    previewQuestion.appendChild(previewOption);
                });

                previewQuiz.appendChild(previewQuestion);
            });
        }

        function htmlspecialchars(str) {
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</body>
</html>
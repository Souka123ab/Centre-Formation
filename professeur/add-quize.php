<?php
require_once '../config/database.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert quiz details
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 1; // Default course_id
        $title = $conn->real_escape_string($_POST['quiz_title']);
        $description = $conn->real_escape_string($_POST['quiz_description']);
        
        $quiz_sql = "INSERT INTO quizzes (course_id, title, description) VALUES (?, ?, ?)";
        $quiz_stmt = $conn->prepare($quiz_sql);
        $quiz_stmt->bind_param("iss", $course_id, $title, $description);
        $quiz_stmt->execute();
        $quiz_id = $conn->insert_id;
        
        // Insert questions and options
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $index => $question_text) {
                // Insert question
                $question_text = $conn->real_escape_string($question_text);
                $question_sql = "INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)";
                $question_stmt = $conn->prepare($question_sql);
                $question_stmt->bind_param("is", $quiz_id, $question_text);
                $question_stmt->execute();
                $question_id = $conn->insert_id;
                
                // Insert options
                if (isset($_POST['options'][$index]) && isset($_POST['correct_answer'][$index])) {
                    $correct_answer = intval($_POST['correct_answer'][$index]);
                    foreach ($_POST['options'][$index] as $opt_index => $option_text) {
                        $option_text = $conn->real_escape_string($option_text);
                        $is_correct = ($opt_index + 1 == $correct_answer) ? 1 : 0;
                        $option_sql = "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
                        $option_stmt = $conn->prepare($option_sql);
                        $option_stmt->bind_param("isi", $question_id, $option_text, $is_correct);
                        $option_stmt->execute();
                    }
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        header("Location: dashboard.php?success=Quiz created successfully");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = "Error creating quiz: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Create New Quiz</title>
    <link rel="stylesheet" href="../css/add-quize.css">
    <style>
       .delete-question {
    background: none;
    border: none;
    color: red;
    font-size: 18px;
    cursor: pointer;
    margin-left: 10px;
    vertical-align: middle;
}

.delete-question:hover {
    color: darkred;
}

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

        <form method="POST" action="create_quiz.php">
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
    </div>

    <script src="add-quiz.js"></script>
  <script>
    let questionCount = 0;

    function createQuestionBlock(number) {
        const div = document.createElement('div');
        div.classList.add('question-block');
        div.dataset.questionNumber = number;

        div.innerHTML = `
            <h3>Question <span class="question-number">${number + 1}</span>
                <button type="button" class="delete-question" data-number="${number}" title="Delete this question">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </h3>
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
            });
        } else {
            console.error('Add Question button not found. Check the class name.');
        }

        // Add delete functionality
        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-question') || e.target.closest('.delete-question')) {
                const button = e.target.classList.contains('delete-question') ? e.target : e.target.closest('.delete-question');
                const number = parseInt(button.dataset.number);
                const block = document.querySelector(`.question-block[data-question-number="${number}"]`);
                if (block) {
                    block.remove();
                    // Re-index remaining questions
                    const blocks = container.querySelectorAll('.question-block');
                    blocks.forEach((b, i) => {
                        b.dataset.questionNumber = i;
                        b.querySelector('.question-number').textContent = i + 1;
                        const deleteButton = b.querySelector('.delete-question');
                        if (deleteButton) {
                            deleteButton.dataset.number = i;
                        }
                        const inputs = b.querySelectorAll('input');
                        inputs.forEach(input => {
                            const name = input.name.replace(/\[\d+\]/, `[${i}]`);
                            input.name = name;
                            if (input.type === 'radio') {
                                input.id = input.id.replace(/-\d+-/, `-${i}-`);
                                input.setAttribute('name', `correct_answer[${i}]`);
                            }
                        });
                    });
                    questionCount = blocks.length;
                }
            }
        });
    });
</script>
</body>
</html>
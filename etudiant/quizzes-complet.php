<?php
session_start();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("Vous devez être connecté pour voir vos résultats."); // Or in Darija: "خاصك تكون متصل باش تشوف النتائج ديالك"
}

try {
    $stmt = $pdo->prepare("
        SELECT q.id AS quiz_id, q.title, uq.score, uq.completed_at
        FROM user_quizzes uq
        JOIN quizzes q ON uq.quiz_id = q.id
        WHERE uq.user_id = ? AND uq.completed = 1
        ORDER BY uq.completed_at DESC
    ");
    $stmt->execute([$user_id]);
    $completed_quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard."); // Or in Darija: "مشكل فالربط مع قاعدة البيانات، جرب مرة أخرى"
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mes quizzes complétés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            background-color: #f4f6f8;
            direction: ltr; /* Change to 'rtl' if using Darija */
        }

        h1 {
            color: #c0392b; /* Moroccan red */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background-color: #c0392b; /* Moroccan red */
            color: white;
        }

        .score {
            font-weight: bold;
            color: #27ae60; /* Moroccan green */
        }

        a.btn {
            padding: 6px 12px;
            background-color: #c0392b;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        a.btn:hover {
            background-color: #e74c3c;
        }

        /* Mobile responsiveness */
        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            th {
                display: none;
            }
            td {
                text-align: right;
                position: relative;
                padding-left: 50%;
            }
            td:before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>

<h1>📘 Mes quizzes complétés</h1> <!-- Or: الكويزات ديالي لي كملتهم -->

<?php if ($completed_quizzes): ?>
    <table>
        <thead>
            <tr>
                <th>Titre du quiz</th> <!-- Or: عنوان الكويز -->
                <th>Note obtenue</th> <!-- Or: النقطة لي حصلتي عليها -->
                <th>Date de complétion</th> <!-- Or: تاريخ التكميل -->
                <th>Action</th> <!-- Or: العملية -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($completed_quizzes as $quiz): ?>
                <tr>
                    <td data-label="Titre du quiz"><?= htmlspecialchars($quiz['title']) ?></td>
                    <td data-label="Note obtenue" class="score"><?= number_format($quiz['score'], 2) ?>%</td>
                    <td data-label="Date de complétion"><?= date('d/m/Y H:i', strtotime($quiz['completed_at'])) ?></td>
                    <td data-label="Action">
                        <a href="quiz-review.php?id=<?= $quiz['quiz_id'] ?>" class="btn">📖 Revoir</a> <!-- Or: راجع -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Vous n’avez encore complété aucun quiz.</p> <!-- Or: مزال ما كملتي حتى كويز -->
<?php endif; ?>

</body>
</html>
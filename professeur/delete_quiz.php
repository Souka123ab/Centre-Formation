<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = $_POST['quiz_id'] ?? null;

    if ($quiz_id && is_numeric($quiz_id)) {
        try {
            $pdo->beginTransaction();

            // Supprimer les options liées aux questions de ce quiz
            $stmt = $pdo->prepare("SELECT id FROM questions WHERE quiz_id = ?");
            $stmt->execute([$quiz_id]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($questions as $q) {
                $pdo->prepare("DELETE FROM options WHERE question_id = ?")->execute([$q['id']]);
            }

            // Supprimer les questions du quiz
            $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?")->execute([$quiz_id]);

            // Supprimer le quiz lui-même
            $pdo->prepare("DELETE FROM quizzes WHERE id = ?")->execute([$quiz_id]);

            $pdo->commit();
            header("Location: view_quizzes.php?deleted=1");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Quiz ID invalide.";
    }
} else {
    header("Location: view-quizzes.php");
    exit;
}

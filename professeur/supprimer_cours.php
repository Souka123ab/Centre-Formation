<?php
session_start();
require_once '../config/database.php';
require_once '/xampp/htdocs/centre-formation/include/hedear.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/seconnecter.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get course ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erreur : ID du cours non valide.");
}
$course_id = $_GET['id'];

// Check if course exists and belongs to the user
$stmt = $pdo->prepare("SELECT id FROM cours WHERE id = ? AND created_by = ?");
$stmt->execute([$course_id, $user_id]);
if ($stmt->fetchColumn() === false) {
    die("Erreur : Cours non trouvé ou vous n'avez pas la permission de le supprimer.");
}

// Delete course
$stmt = $pdo->prepare("DELETE FROM cours WHERE id = ? AND created_by = ?");
$stmt->execute([$course_id, $user_id]);

header("Location: mesformation.php?success=1");
exit;
?>
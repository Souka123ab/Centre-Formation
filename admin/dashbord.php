<?php
session_start();
require_once '../config/database.php'; // Assure-toi que $pdo est bien défini ici

try {
    // Total utilisateurs
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmtUsers->fetchColumn();

    // Formateurs
    $stmtTeachers = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
    $stmtTeachers->execute(['formateur']);
    $totalTeachers = $stmtTeachers->fetchColumn();

    // Étudiants
    $stmtStudents = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
    $stmtStudents->execute(['etudiant']);
    $totalStudents = $stmtStudents->fetchColumn();

    // Cours
    $stmtCourses = $pdo->query("SELECT COUNT(*) FROM cours");
    $totalCourses = $stmtCourses->fetchColumn();

    // Formations
    $stmtFormations = $pdo->query("SELECT COUNT(*) FROM formations");
    $totalFormations = $stmtFormations->fetchColumn();

    // Quizzes
    $stmtQuizzes = $pdo->query("SELECT COUNT(*) FROM quizzes");
    $totalQuizzes = $stmtQuizzes->fetchColumn();

} catch (PDOException $e) {
    // Si erreur : toutes les stats à zéro
    $totalUsers = $totalTeachers = $totalStudents = $totalCourses = $totalFormations = $totalQuizzes = 0;
    error_log("Erreur DB : " . $e->getMessage());
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/dashbord.css">
</head>
<body>

<?php require_once '../include/hedear.php'; ?>

<div class="de">
    <div class="dashboard-container">
        <h1 class="dashboard-title">Admin Dashboard</h1>

        <!-- BOUTON Ajouter Formation -->
        <div style="margin-bottom: 20px;">
            <a href="ajouter-formation.php" class="add-button">➕ Ajouter une formation</a>
        </div>

        <div class="stats-grid">
            <!-- Total Users -->
            <div class="stat-card">
                <div class="icon-wrapper blue">👤</div>
                <div class="stat-content">
                    <p class="stat-label">Total Users</p>
                    <p class="stat-value"><?= htmlspecialchars($totalUsers) ?></p>
                </div>
            </div>

            <!-- Teachers -->
            <div class="stat-card">
                <div class="icon-wrapper green">👨‍🏫</div>
                <div class="stat-content">
                    <p class="stat-label">Teachers</p>
                    <p class="stat-value"><?= htmlspecialchars($totalTeachers) ?></p>
                </div>
            </div>

            <!-- Students -->
            <div class="stat-card">
                <div class="icon-wrapper yellow">🎓</div>
                <div class="stat-content">
                    <p class="stat-label">Students</p>
                    <p class="stat-value"><?= htmlspecialchars($totalStudents) ?></p>
                </div>
            </div>

            <!-- Cours -->
            <div class="stat-card">
                <div class="icon-wrapper purple">📚</div>
                <div class="stat-content">
                    <p class="stat-label">Courses</p>
                    <p class="stat-value"><?= htmlspecialchars($totalCourses) ?></p>
                </div>
            </div>

            <!-- Formations -->
            <div class="stat-card">
                <div class="icon-wrapper orange">🏛️</div>
                <div class="stat-content">
                    <p class="stat-label">Formations</p>
                    <p class="stat-value"><?= htmlspecialchars($totalFormations) ?></p>
                </div>
            </div>

            <!-- Quizzes -->
            <div class="stat-card">
                <div class="icon-wrapper red">❓</div>
                <div class="stat-content">
                    <p class="stat-label">Quizzes</p>
                    <p class="stat-value"><?= htmlspecialchars($totalQuizzes) ?></p>
                </div>
            </div>
        </div>

        <div class="platform-status-card">
            <div class="card-header">
                🛠️
                <h2>Platform Status</h2>
            </div>
            <div class="status-alert">
                ✅
                <div>
                    <p class="alert-title">Platform Initialized</p>
                    <p class="alert-message">Your training center platform is ready! Start by adding teachers and students.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Bouton ajouter formation (tu peux adapter selon ton CSS existant) */
.add-button {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s ease;
}
.add-button:hover {
    background-color: #0056b3;
}
</style>

</body>
</html>

<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Formateur</title>
    <link rel="stylesheet" href="../css/dasbordP.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once '/xampp/htdocs/centre-formation/include/hedear.php';
    ?>
    <div class="container">
        <!-- Header -->
 <header class="header">
    <div class="user-info">
        <h1>
            Bienvenue,
            <span class="user-name">
                <?= isset($_SESSION['first_name'], $_SESSION['last_name']) 
                    ? htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) 
                    : 'Utilisateur'; ?>
            </span>
        </h1>

        <?php if (isset($_SESSION['role'], $_SESSION['email'])): ?>
            <p class="user-role">
                <?= ucfirst($_SESSION['role']) . ' • ' . htmlspecialchars($_SESSION['email']) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="profile-icon">
        <i class="fas fa-book"></i>
    </div>
</header>

    


        <!-- Main Dashboard -->
        <main class="dashboard">
            <!-- Navigation Cards Grid -->
            <div class="nav-grid">
                <div class="nav-card" onclick="handleCardClick('formations')">
                    <a href="../professeur/mesformation.php">
                    <div class="card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="card-content">
                        <h3>Mes Formations</h3>
                        <p>Gérer vos cours</p>
                    </div>
                    </a>
                </div>
                <div class="nav-card"  onclick="handleCardClick('lesson')">
                 <a href="../professeur/ceratLesson.php">
                    <div class="card-icon">
                       <i class="fas fa-plus"></i>
                    </div>
                    <div class="card-content">
                        <h3>Créer une Leçon</h3>
                        <p>Ajouter du contenu</p>
                    </div>
                    </a>
                </div>
                <!-- <a href="etudiant.php">
               <div class="nav-card" onclick="handleCardClick('students')">
    <div class="card-icon">
        <i class="fas fa-users"></i>
    </div>
    <div class="card-content">
        <h3>Étudiants</h3>
        <p>Suivre vos étudiants</p>
    </div>
</div>
</a> -->

                <!-- <div class="nav-card" onclick="handleCardClick('planning')">
                    <div class="card-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="card-content">
                        <h3>Planning</h3>
                        <p>Organiser vos sessions</p>
                    </div>
                </div> -->
                 <!-- Statistics Section -->
    <div class="nav-card">
        <div class="stats-card">
                        <a href="view_quizzes.php" style="text-decoration: none; color: inherit;">

            <div class="card-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="card-content">
                <h3>View Quiz</h3>
                <p>Voir les quiz disponibles</p>
            </div>
        </div>
    </div>
</a>

            <a href="../professeur/add-quize.php" class="nav-card">
            <div class="card-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="card-content">
                <h3>Quiz</h3>
                <p>Créer ou gérer les quiz</p>
            </div>
        </a>

            </div>

           
            <!-- Formations Summary -->
            <div class="formations-summary">
                <h3>Vos Formations</h3>
                <p class="summary-text">Vous animez actuellement <span class="highlight">0 formations</span> avec <span class="highlight">0 étudiants</span> au total.</p>
            </div>
        </main>
    </div>

    <script src="dasbordP.js">
      

    </script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme d'Apprentissage</title>
    <link rel="stylesheet" href="../css/dashbordET.css">
</head>
<body>
    <?php
    require_once '/xampp/htdocs/centre-formation/include/hedear.php';
    ?>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="user-info">
                <h1 class="welcome-title">Bienvenue, <?php echo htmlspecialchars($_SESSION['first_name'] ?? ''); ?></h1>
                <div class="user-details">
                    <span class="badge">Étudiant</span>
                    <span class="separator">•</span>
                    <span class="email"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
                </div>
            </div>
            <div class="avatar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
        </header>

        <!-- Navigation Cards -->
        <div class="nav-grid">
            <a href="mescours.php" class="nav-card">
            <!-- <div class="nav-card" onclick="navigateTo('courses')"> -->
                <div class="card-header">
                    <div class="icon-wrapper blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <h3>Mes Cours</h3>
                </div>
                <p class="card-description">Accéder à vos formations</p>
            </a>

            <!-- <a href="planning.php" class="nav-card">
                <div class="card-header">
                    <div class="icon-wrapper green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <h3>Planning</h3>
                </div>
                <p class="card-description">Consulter votre planning</p>
                </a> -->

            <!-- <a href="progression.php" class="nav-card">
                <div class="card-header">
                    <div class="icon-wrapper purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22,7 13.5,15.5 8.5,10.5 2,17"></polyline>
                            <polyline points="16,7 22,7 22,13"></polyline>
                        </svg>
                    </div>
                    <h3>Progression</h3>
                </div>
                <p class="card-description">Suivre vos progrès</p>
            </a> -->
                <!-- Carte Formations Disponibles -->
                 <!-- Carte Leçons Complétées -->
<a href="lessons.php" class="nav-card">
    <div class="card-header">
        <div class="icon-wrapper green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14"></path>
                <path d="M12 5v14"></path>
            </svg>
        </div>
        <h3>Leçons Complétées</h3>
    </div>
    <p class="card-description"><?php echo $lessons_completed ?? 0; ?> leçons terminées</p>
</a>

<!-- Carte Quiz Complétés -->
<a href="quizzes-complet.php" class="nav-card">
    <div class="card-header">
        <div class="icon-wrapper blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2l4 -4"></path>
                <circle cx="12" cy="12" r="10"></circle>
            </svg>
        </div>
        <h3>Quiz Réussis</h3>
    </div>
    <p class="card-description"><?php echo $quizzes_completed ?? 0; ?> quiz complétés</p>
</a>

            <a href="formationdespo.php" class="nav-card">
            <div class="card-header">
            <div class="icon-wrapper orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16v16H4z"></path>
                    <path d="M4 9h16"></path>
                    <path d="M9 4v16"></path>
                </svg>
            </div>
            <h3>Formations Disponibles</h3>
        </div>
        <p class="card-description">Voir toutes les formations proposées</p>
        </a>

<!-- Carte Quiz -->
    <a href="quizzes.php" class="nav-card">
    <div class="card-header">
        <div class="icon-wrapper red">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M8 15h.01"></path>
                <path d="M12 15h.01"></path>
                <path d="M16 15h.01"></path>
                <path d="M9 9h6"></path>
            </svg>
        </div>
        <h3>Quiz</h3>
    </div>
    <p class="card-description">Testez vos connaissances</p>
</a>

            <!-- <a href="profil.php" class="nav-card">
                <div class="card-header">
                    <div class="icon-wrapper gray">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </div>
                    <h3>Profil</h3>
                </div>
                <p class="card-description">Gérer votre compte</p>
            </a>
        </div> -->

        <!-- Current Training Section -->
        <div class="training-section">
            <h2>Vos Formations Actuelles</h2>
            <p>Vous êtes inscrit à 0 formations. Continuez votre apprentissage !</p>
        </div>
    </div>

    <script src="scriptdas.js"></script>
</body>
</html>
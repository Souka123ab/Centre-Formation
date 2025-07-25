<?php
// session_start();
require_once './config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/hedear.css">
    <link rel="stylesheet" href="css/home.css">
    <title>Document</title>
</head>
<body>
<?php
// require_once './include/hedear.php';
?>


 <div class="container">
        <!-- Header Section -->

        <header class="header-homme">
            <h1>Plateforme de Gestion d'un Centre de Formation</h1>
            <p class="subtitle">
                Une solution complète pour la gestion des formations, étudiants et formateurs avec un 
                système d'authentification sécurisé pour tous les utilisateurs.
            </p>
        </header>

        <!-- User Access Cards -->
        <section class="access-cards">
            <div class="card student-card">
                <div class="card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Espace Étudiant</h3>
                <p>Accédez à vos cours, suivez votre progression et interagissez avec vos formateurs.</p>
                <div class="card-buttons">
                    <button class="btn btn-primary" onclick="handleLogin('student')"><a href="auth/loginetudant.php">Connexion →</a></button>
                    <button class="btn btn-secondary" onclick="handleRegister('student')"> <a href="auth/s'inscrireetudiant.php">S'inscrire</a></button>
                </div>
            </div>

            <div class="card instructor-card">
                <div class="card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Espace Formateur</h3>
                <p>Créez et gérez vos formations, suivez les progrès de vos étudiants.</p>
                <div class="card-buttons">
                    <button class="btn btn-primary" onclick="handleLogin('instructor')"><a href="auth/loginprofesseur.php">Connexion →</a></button>
                    <button class="btn btn-secondary-1" onclick="handleRegister('instructor')"> <a href="auth/sinscrireprofesseur.php">S'inscrire</a></button>
                </div>
            </div>

            <div class="card admin-card">
                <div class="card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Administration</h3>
                <p>Gérez l'ensemble de la plateforme, utilisateurs et contenus de formation.</p>
                <div class="card-buttons">
                    <button class="btn btn-primary" onclick="handleLogin('admin')"><a href="auth/login-admin.php">Connexion →</a></button>
                    <button class="btn btn-secondary-2" onclick="handleRegister('admin')"> <a href="auth/inscript-admin.php">S'inscrire</a></button>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <h2>Fonctionnalités Principales</h2>
            <div class="features-grid">
                <div class="feature-column">
                    <h3>Gestion des Utilisateurs</h3>
                    <ul>
                        <li>• Authentification sécurisée par rôle</li>
                        <li>• Profils personnalisés pour chaque utilisateur</li>
                        <li>• Gestion des permissions et accès</li>
                        <li>• Système de notifications intégré</li>
                    </ul>
                </div>
                <div class="feature-column">
                    <h3>Gestion des Formations</h3>
                    <ul>
                        <li>• Création et organisation des cours</li>
                        <li>• Suivi des progressions en temps réel</li>
                        <li>• Évaluations et certifications</li>
                        <li>• Ressources pédagogiques centralisées</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>

</body>
</html>
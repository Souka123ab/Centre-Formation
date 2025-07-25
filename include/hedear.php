<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

// Valeurs par défaut
$user_name = 'Invité';
$user_role = null;

// Si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare('SELECT first_name, last_name, role FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_name = trim($user['first_name'] . ' ' . $user['last_name']);
            $user_role = $user['role'];
        }
    } catch (PDOException $e) {
        $user_name = 'Invité';
    }
}

// Gérer la déconnexion
if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: /centre-formation/home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formation Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <!-- Ton fichier CSS -->
    <link rel="stylesheet" href="/centre-formation/css/hedear.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap');
        .user-name {
            color: #333;
            font-weight: 500;
            margin-right: 15px;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <i class="fa-sharp fa-solid fa-graduation-cap"></i>
            <h3>Formation Center</h3>
        </div>
        <nav>
            <?php if ($user_role === 'formateur'): ?>
                <a href="/centre-formation/professeur/notifications.php">
                    <i class="fa-regular fa-bell"></i> Notifications
                </a>
            <?php endif; ?>

            <?php if ($user_role === 'admin'): ?>
                <span style="color: #007bff; font-weight: bold; margin-left: 10px;">
                    (Admin connecté)
                </span>
            <?php endif; ?>

            <!-- Nom de l'utilisateur connecté -->
            <span class="user-name">
                <?php
                if (isset($_SESSION['admin_name'])) {
                    echo htmlspecialchars($_SESSION['admin_name']);
                } else {
                    echo htmlspecialchars($user_name);
                }
                ?>
            </span>

            <!-- Lien vers Dashboard -->
            <?php if ($user_role): ?>
                <?php
                $dashboardLink = match ($user_role) {
                    'admin'     => '/centre-formation/admin/dashboard.php',
                    'formateur' => '/centre-formation/professeur/dasbordP.php',
                    'etudiant'  => '/centre-formation/etudiant/dashobrdE.php',
                    default     => '/centre-formation/home.php',
                };
                ?>
                <a href="<?= $dashboardLink ?>">
                    <i class="fa-regular fa-user"></i> Dashboard
                </a>
            <?php endif; ?>

            <!-- Connexion / Déconnexion -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?logout=true">
                    <i class="fa-solid fa-right-from-bracket"></i> Se déconnecter
                </a>
            <?php else: ?>
                <a href="/centre-formation/home.php">
                    <i class="fa-solid fa-right-to-bracket"></i> Se connecter
                </a>
            <?php endif; ?>
        </nav>
    </header>
</body>
</html>

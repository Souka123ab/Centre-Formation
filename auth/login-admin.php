<?php
session_start();
require_once '../config/database.php'; // chemin vers ta config PDO

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } else {
        // Chercher l'admin par email
        $stmt = $pdo->prepare("SELECT id, full_name, email, password FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

       if ($admin && password_verify($password, $admin['password'])) {
    // Auth OK - sauvegarder session
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_role'] = 'admin'; // ← fixe le problème de redirection

    // Redirection vers le dashboard admin
    header("Location: ../admin/dashbord.php");
    exit;
}

         else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login-admin.css">
</head>
<body>
    <?php
    require_once '/xampp/htdocs/centre-formation/include/hedear.php';?>
    <div class="log">
    <div class="login-card">
        <div class="icon-container">
            <!-- Shield icon from Lucide React, converted to SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <h1>Connexion Admin</h1>
        <p class="subtitle">Accès administrateur professionnel</p>
       <form method="POST">
    <div class="form-group">
        <label for="email">Email administrateur</label>
        <div class="input-wrapper">
            <input type="email" id="email" name="email" placeholder="admin@example.com" required>
        </div>
    </div>
    <div class="form-group">
        <label for="password">Mot de passe</label>
        <div class="input-wrapper">
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            <span class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">
                <!-- Icon SVG -->
            </span>
        </div>
    </div>
    <button type="submit">Se connecter</button>
</form>

        <p class="signup-link">
            Créer un compte administrateur ? <a href="inscript-admin.php">S'inscrire</a>
        </p>
    </div>
</div>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        `;
        const eyeOffIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.06 18.06 0 0 1 5.47-2.55M12 10a2 2 0 0 0-3.18-2.2L12 10Z"/>
                <path d="M6 6l2 2"/>
                <path d="M22 22 2 2"/>
                <path d="M10.79 5.22 12 2c7 0 10 7 10 7a18.06 18.06 0 0 1-1.47 3.65"/>
            </svg>
        `;

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change the icon based on visibility
            if (type === 'password') {
                togglePassword.innerHTML = eyeIcon;
                togglePassword.setAttribute('aria-label', 'Show password');
            } else {
                togglePassword.innerHTML = eyeOffIcon;
                togglePassword.setAttribute('aria-label', 'Hide password');
            }
        });
    </script>
</body>
</html>
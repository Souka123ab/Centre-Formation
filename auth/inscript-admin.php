<?php
session_start();
require_once '../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $organization = trim($_POST['organization']);
    $admin_code = trim($_POST['admin_code']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifie si tous les champs sont remplis
    if (empty($full_name) || empty($email) || empty($organization) || empty($admin_code) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif ($admin_code !== 'ADM2024') { // tu peux changer ce code
        $error = "Code administrateur incorrect.";
    } else {
        // Vérifie si l'email est déjà utilisé
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Hash du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insertion
            $insert = $pdo->prepare("INSERT INTO admins (full_name, email, organization, password) VALUES (?, ?, ?, ?)");
            if ($insert->execute([$full_name, $email, $organization, $hashed_password])) {
                $success = "Compte administrateur créé avec succès.";
                header("Location: login-admin.php"); // redirection après inscription
                exit;
            } else {
                $error = "Erreur lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../css/inscription-admin.css">
</head>
<body>
    <?php require_once '../include/hedear.php'; ?>
    <div class="cont">

   
    <div class="form-container">
        <div class="header">
            <div class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <h1 class="title">Inscription Admin</h1>
            <p class="subtitle">Créez votre compte administrateur</p>
        </div>
<form class="registration-form" method="POST">
    <div class="form-group">
        <label for="full-name">Nom complet</label>
        <input type="text" id="full-name" name="full_name" placeholder="Pierre Durand" required>
    </div>
    <div class="form-group">
        <label for="email">Email professionnel</label>
        <input type="email" id="email" name="email" placeholder="admin@organization.com" required>
    </div>
    <div class="form-group">
        <label for="organization">Organisation</label>
        <input type="text" id="organization" name="organization" placeholder="Nom de votre organisation" required>
    </div>
    <div class="form-group">
        <label for="admin-code">Code d'accès administrateur</label>
        <input type="text" id="admin-code" name="admin_code" placeholder="Code fourni par l'organisation" required>
    </div>
    <div class="form-group password-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="********" required>
        <!-- bouton toggle password -->
    </div>
    <div class="form-group password-group">
        <label for="confirm-password">Confirmer le mot de passe</label>
        <input type="password" id="confirm-password" name="confirm_password" placeholder="********" required>
        <!-- bouton toggle password -->
    </div>
    <button type="submit" class="submit-button">S'inscrire</button>
</form>

        <div class="login-link">
            Déjà un compte ? <a href="login-admin.php">Se connecter</a>
        </div>
    </div>
     </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
  const togglePasswordButtons = document.querySelectorAll(".toggle-password")

  togglePasswordButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const passwordInput = button.previousElementSibling // The input field is the sibling before the button
      const eyeIcon = button.querySelector(".eye-icon")

      if (passwordInput.type === "password") {
        passwordInput.type = "text"
        // Change icon to eye-off
        eyeIcon.innerHTML =
          '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.76 9.76 0 0 0 5.46-1.39"></path><line x1="2" x2="22" y1="2" y2="22"></line>'
      } else {
        passwordInput.type = "password"
        // Change icon back to eye
        eyeIcon.innerHTML =
          '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>'
      }
    })
  })
})

    </script>
</body>
</html>

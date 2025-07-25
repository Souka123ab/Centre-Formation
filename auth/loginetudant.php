<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '/xampp/htdocs/centre-formation/config/database.php'; // Database connection

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /centre-formation/etudiant/dashobrdE.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];

    // Basic validation
    if (empty($email)) {
        $errors['email'] = 'L\'email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse email invalide.';
    }

    if (empty($password)) {
        $errors['password'] = 'Le mot de passe est requis.';
    }

    // Verify credentials
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT id, email, password, role FROM users WHERE email = ? AND role = "etudiant"');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                header('Location: /centre-formation/etudiant/dashobrdE.php');
                exit;
            } else {
                $errors['login'] = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $errors['database'] = 'Erreur de connexion à la base de données.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Étudiant</title>
    <link rel="stylesheet" href="../css/loginetudant.css">
</head>
<body>
    <?php
    require_once '/xampp/htdocs/centre-formation/include/hedear.php';
    ?>
    <div class="group">
    <div class="container">
        <div class="login-form">
            <div class="user-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="#6B7280"/>
                    <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="#6B7280"/>
                </svg>
            </div>
            
            <h1>Connexion Étudiant</h1>
            <p class="subtitle">Accédez à votre espace formation</p>

            <?php if (!empty($errors)): ?>
                <div class="error-container">
                    <?php foreach ($errors as $error): ?>
                        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" 
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <div class="error-message" id="emailError"></div>
                </div>
                
              <div class="form-group">
    <label for="password">Mot de passe</label>
    <div class="password-input">
        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
        <button type="button" class="toggle-password" id="togglePassword">
            <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    <div class="error-message" id="passwordError"></div>
</div>
                
                <button type="submit" class="login-button">Se connecter</button>
            </form>
            
            <p class="signup-link">
                Pas encore de compte ? <a href="/centre-formation/auth/s'inscrireetudiant.php" id="signupLink">S'inscrire</a>
            </p>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("password");
            const loginForm = document.getElementById("loginForm");
            const signupLink = document.getElementById("signupLink");
            const emailInput = document.getElementById("email");
            const emailError = document.getElementById("emailError");
            const passwordError = document.getElementById("passwordError");

            // Toggle password visibility
            togglePassword.addEventListener("click", () => {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                const eyeIcon = togglePassword.querySelector(".eye-icon");
                eyeIcon.style.opacity = type === "text" ? "0.7" : "1";
            });

            // Handle form submission
            loginForm.addEventListener("submit", (e) => {
                let isValid = true;
                
                // Reset error messages
                emailError.textContent = '';
                passwordError.textContent = '';

                // Client-side validation
                if (!emailInput.value) {
                    emailError.textContent = 'L\'email est requis.';
                    isValid = false;
                } else if (!/^\S+@\S+\.\S+$/.test(emailInput.value)) {
                    emailError.textContent = 'Adresse email invalide.';
                    isValid = false;
                }

                if (!passwordInput.value) {
                    passwordError.textContent = 'Le mot de passe est requis.';
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }

                const loginButton = document.querySelector(".login-button");
                const originalText = loginButton.textContent;
                
                if (isValid) {
                    loginButton.textContent = "Connexion...";
                    loginButton.disabled = true;
                }
            });

            // Handle signup link
            signupLink.addEventListener("click", (e) => {
                e.preventDefault();
                window.location.href = "/centre-formation/auth/s'inscrireetudiant.php";
            });

            // Add input validation feedback
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach((input) => {
                input.addEventListener("blur", function () {
                    if (this.value && !this.checkValidity()) {
                        this.style.borderColor = "#ef4444";
                    } else if (this.value) {
                        this.style.borderColor = "#10b981";
                    } else {
                        this.style.borderColor = "#d1d5db";
                    }
                });

                input.addEventListener("input", function () {
                    this.style.borderColor = "#d1d5db";
                });
            });
        });
    </script>
</body>
</html>
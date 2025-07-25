<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '/xampp/htdocs/centre-formation/config/database.php'; // Database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Sanitize and validate input
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Un email valide est requis.';
    }
    if (empty($password)) {
        $errors['password'] = 'Le mot de passe est requis.';
    }

    // If no errors, proceed with authentication
    if (empty($errors)) {
        try {
$stmt = $pdo->prepare('SELECT id, email, password, role, first_name, last_name FROM users WHERE email = ? AND role = "formateur"');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($user && password_verify($password, $user['password'])) {
           $_SESSION['user_id'] = $user['id'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

                header('Location: /centre-formation/professeur/dasbordP.php');
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
    <link rel="stylesheet" href="../css/loginprofesseur.css">
    <title>Connexion Formateur</title>
</head>
<body>
    <?php require_once '/xampp/htdocs/centre-formation/include/hedear.php'; ?>
    <div class="pro">
        <div class="login-container">
            <div class="icon-container">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                    <path d="M3 2v20h18V2H3zm16 18H5V4h14v16z"/>
                </svg>
            </div>
            
            <h1 class="title">Connexion Formateur</h1>
            <p class="subtitle">Accédez à votre espace formation</p>
            
            <form id="loginForm" method="POST" action="">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="votre@email.com"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                        required
                    >
                    <div class="error-message" id="emailError">
                        <?php if (isset($errors['email'])) echo '<span>' . $errors['email'] . '</span>'; ?>
                        <?php if (isset($errors['login'])) echo '<span>' . $errors['login'] . '</span>'; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="password-container">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="••••••••"
                            required
                        >
                        <button type="button" class="password-toggle" id="togglePassword">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path id="eyeIcon" d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="error-message" id="passwordError">
                        <?php if (isset($errors['password'])) echo '<span>' . $errors['password'] . '</span>'; ?>
                    </div>
                </div>
                
                <button type="submit" class="login-button">Se connecter</button>
            </form>
            
            <p class="signup-link">
                Pas encore de compte ? <a href="/centre-formation/formateur/sinscrireprofesseur.php" id="signupLink">S'inscrire</a>
            </p>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                eyeIcon.setAttribute('d', 'M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92 1.41-1.41L3.51 2.3 2.1 3.71l1.43 1.43C2.52 6.15 1.73 8.92 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42 1.41-1.41L12 7z');
            } else {
                eyeIcon.setAttribute('d', 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z');
            }
        });

        // Form validation and submission
        const loginForm = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        function showError(element, message) {
            element.innerHTML = message;
            element.style.display = 'block';
        }

        function hideError(element) {
            element.style.display = 'none';
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            
            // Reset errors
            hideError(emailError);
            hideError(passwordError);
            
            // Validate email
            if (!emailInput.value.trim()) {
                showError(emailError, 'L\'email est requis');
                isValid = false;
            } else if (!validateEmail(emailInput.value)) {
                showError(emailError, 'Veuillez entrer un email valide');
                isValid = false;
            }
            
            // Validate password
            if (!passwordInput.value.trim()) {
                showError(passwordError, 'Le mot de passe est requis');
                isValid = false;
            } else if (passwordInput.value.length < 6) {
                showError(passwordError, 'Le mot de passe doit contenir au moins 6 caractères');
                isValid = false;
            }
            
            if (isValid) {
                // Submit the form
                loginForm.submit();
            }
        });

        // Sign up link
        document.getElementById('signupLink').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/centre-formation/formateur/sinscrireprofesseur.php';
        });

        // Real-time validation
        emailInput.addEventListener('blur', function() {
            if (this.value.trim() && !validateEmail(this.value)) {
                showError(emailError, 'Veuillez entrer un email valide');
            } else {
                hideError(emailError);
            }
        });

        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0 && this.value.length < 6) {
                showError(passwordError, 'Le mot de passe doit contenir au moins 6 caractères');
            } else {
                hideError(passwordError);
            }
        });
    </script>
</body>
</html>
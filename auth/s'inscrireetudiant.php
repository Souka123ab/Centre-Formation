<?php
require_once '../config/database.php'; // Database connection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = 'etudiant'; // Set role to 'etudiant'
    
    $errors = [];
    
    // Server-side validation
    // Full name validation
    if (empty($fullName) || strlen($fullName) < 3) {
        $errors['fullName'] = 'Le nom doit contenir au moins 3 caractères.';
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse email invalide.';
    }
    
    // Enhanced password validation
    if (strlen($password) < 8) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = 'Le mot de passe doit contenir au moins une majuscule.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Le mot de passe doit contenir au moins un chiffre.';
    } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors['password'] = 'Le mot de passe doit contenir au moins un caractère spécial.';
    }
    
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Les mots de passe ne correspondent pas.';
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors['email'] = 'Cet email est déjà utilisé.';
    }
    
    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Insert into database
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare('
                INSERT INTO users (first_name, last_name, email, password, role, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ');
            
            // Split full name into first and last name
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
            
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $role]);
            
            $pdo->commit();
            $registrationSuccess = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['database'] = 'Erreur lors de l\'inscription: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/hedear.css">
    <link rel="stylesheet" href="../css/sinscrireetudiant.css">
    <link rel="stylesheet" href="styles.css">
    <title>Inscription Étudiant</title>
</head>
<body>
    <?php
    require_once '/xampp/htdocs/centre-formation/include/hedear.php';
    ?>
    <div class="container">
    <div class="form-container">
        <div class="form-header">
            <div class="form-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <h1 class="form-title">Inscription Étudiant</h1>
            <p class="form-subtitle">Créer votre compte étudiant</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-container">
                <?php foreach ($errors as $error): ?>
                    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="registrationForm" method="POST" action="">
            <div class="form-group">
                <label for="fullName" class="form-label">Nom complet</label>
                <input 
                    type="text" 
                    id="fullName" 
                    name="fullName" 
                    class="form-input" 
                    placeholder="Jean Dupont"
                    value="<?php echo isset($fullName) ? htmlspecialchars($fullName) : ''; ?>"
                    required
                >
                <div class="error-message" id="fullNameError"></div>
                <div class="success-message" id="fullNameSuccess"></div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="jean@exemple.com"
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                    required
                >
                <div class="error-message" id="emailError"></div>
                <div class="success-message" id="emailSuccess"></div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    >
                    <button type="button" class="password-toggle" data-target="password">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                        </svg>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength"></div>
                <div class="error-message" id="passwordError"></div>
                <div class="success-message" id="passwordSuccess"></div>
                <div class="password-requirements">
                    <p>Le mot de passe doit contenir :</p>
                    <ul>
                        <li id="length">Au moins 8 caractères</li>
                        <li id="uppercase">Une majuscule</li>
                        <li id="number">Un chiffre</li>
                        <li id="special">Un caractère spécial</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="confirmPassword" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    >
                    <button type="button" class="password-toggle" data-target="confirmPassword">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                        </svg>
                    </button>
                </div>
                <div class="error-message" id="confirmPasswordError"></div>
                <div class="success-message" id="confirmPasswordSuccess"></div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span class="btn-text">S'inscrire</span>
                <div class="btn-loader" id="btnLoader"></div>
            </button>
        </form>

        <div class="form-footer">
            <a href="./loginetudant.php" id="signInLink">Déjà un compte ? Se connecter</a>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal" id="successModal" <?php echo isset($registrationSuccess) ? 'style="display: flex;"' : ''; ?>>
        <div class="modal-content">
            <div class="modal-icon success">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
            </div>
            <h2>Inscription réussie !</h2>
            <p>Votre compte étudiant a été créé avec succès.</p>
            <button class="modal-btn" id="closeModal">Continuer</button>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('registrationForm');
            const modal = document.getElementById('successModal');
            const closeModal = document.getElementById('closeModal');

            const fullName = document.getElementById('fullName');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');

            const fullNameError = document.getElementById('fullNameError');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');

            const fullNameSuccess = document.getElementById('fullNameSuccess');
            const emailSuccess = document.getElementById('emailSuccess');
            const passwordSuccess = document.getElementById('passwordSuccess');
            const confirmPasswordSuccess = document.getElementById('confirmPasswordSuccess');

            const passwordStrength = document.getElementById('passwordStrength');
            const lengthReq = document.getElementById('length');
            const uppercaseReq = document.getElementById('uppercase');
            const numberReq = document.getElementById('number');
            const specialReq = document.getElementById('special');

            // 🔒 Afficher/masquer les mots de passe
            document.querySelectorAll('.password-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    targetInput.type = targetInput.type === 'password' ? 'text' : 'password';
                });
            });

            // 📊 Vérifie la force du mot de passe
            password.addEventListener('input', () => {
                const pwd = password.value;
                const strength = getPasswordStrength(pwd);
                passwordStrength.textContent = strength.label;
                passwordStrength.style.color = strength.color;

                // Update requirement indicators
                lengthReq.style.color = pwd.length >= 8 ? 'green' : 'red';
                uppercaseReq.style.color = /[A-Z]/.test(pwd) ? 'green' : 'red';
                numberReq.style.color = /[0-9]/.test(pwd) ? 'green' : 'red';
                specialReq.style.color = /[^A-Za-z0-9]/.test(pwd) ? 'green' : 'red';
            });

            function getPasswordStrength(pwd) {
                if (pwd.length < 8) return { label: 'Faible', color: 'red' };
                if (/[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && /[^A-Za-z0-9]/.test(pwd))
                    return { label: 'Fort', color: 'green' };
                return { label: 'Moyen', color: 'orange' };
            }

            // ✅ Validation du formulaire
            form.addEventListener('submit', (e) => {
                let isValid = true;

                resetMessages();

                // Nom
                if (fullName.value.trim().length < 3) {
                    fullNameError.textContent = 'Le nom doit contenir au moins 3 caractères.';
                    isValid = false;
                } else {
                    fullNameSuccess.textContent = 'Nom valide.';
                }

                // Email
                if (!/^\S+@\S+\.\S+$/.test(email.value)) {
                    emailError.textContent = 'Adresse email invalide.';
                    isValid = false;
                } else {
                    emailSuccess.textContent = 'Email valide.';
                }

                // Mot de passe
                if (password.value.length < 8) {
                    passwordError.textContent = 'Le mot de passe doit contenir au moins 8 caractères.';
                    isValid = false;
                } else if (!/[A-Z]/.test(password.value)) {
                    passwordError.textContent = 'Le mot de passe doit contenir au moins une majuscule.';
                    isValid = false;
                } else if (!/[0-9]/.test(password.value)) {
                    passwordError.textContent = 'Le mot de passe doit contenir au moins un chiffre.';
                    isValid = false;
                } else if (!/[^A-Za-z0-9]/.test(password.value)) {
                    passwordError.textContent = 'Le mot de passe doit contenir au moins un caractère spécial.';
                    isValid = false;
                } else {
                    passwordSuccess.textContent = 'Mot de passe valide.';
                }

                // Confirmation mot de passe
                if (confirmPassword.value !== password.value) {
                    confirmPasswordError.textContent = 'Les mots de passe ne correspondent pas.';
                    isValid = false;
                } else {
                    confirmPasswordSuccess.textContent = 'Mot de passe confirmé.';
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function resetMessages() {
                [fullNameError, emailError, passwordError, confirmPasswordError,
                 fullNameSuccess, emailSuccess, passwordSuccess, confirmPasswordSuccess]
                    .forEach(el => el.textContent = '');
                [lengthReq, uppercaseReq, numberReq, specialReq].forEach(el => el.style.color = 'red');
            }

            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
                window.location.href = './loginetudant.php';
            });
        });
    </script>
</body>
</html>
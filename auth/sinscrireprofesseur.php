<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

// Charger les spécialisations dynamiquement
$specializationsList = [];
try {
    $stmtSpec = $pdo->query("SELECT id, name FROM specializations");
    $specializationsList = $stmtSpec->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur chargement spécialisations : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $education = trim($_POST['education'] ?? '');
    $certifications = $_POST['certifications'] ?? [];
    $previousExperience = $_POST['experiences'] ?? [];
    $presentation = trim($_POST['presentation'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validation
    if (empty($fullName)) $errors['fullName'] = 'Le nom complet est requis.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Un email valide est requis.';
    if (empty($password)) $errors['password'] = 'Le mot de passe est requis.';
    elseif (strlen($password) < 6) $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    elseif ($password !== $confirmPassword) $errors['password'] = 'Les mots de passe ne correspondent pas.';
    if (empty($specialization)) $errors['specialization'] = 'La spécialisation est requise.';
    if ($experience === '' || $experience < 0) $errors['experience'] = 'Les années d\'expérience doivent être un nombre positif.';
    if (empty($phone)) $errors['phone'] = 'Le numéro de téléphone est requis.';
    elseif (!preg_match('/^[0-9]{8,15}$/', $phone)) $errors['phone'] = 'Numéro de téléphone invalide.';

    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            $certificationsStr = implode(', ', array_filter($certifications));
            $previousExpStr = implode('; ', array_filter($previousExperience));

            $stmt = $pdo->prepare('
                INSERT INTO users (
                    first_name, last_name, email, password, role,
                    phone, specialization, experience, education,
                    certifications, previous_experience, presentation
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $stmt->execute([
                $firstName,
                $lastName,
                $email,
                $hashedPassword,
                'formateur',
                $phone,
                $specialization,
                $experience,
                $education,
                $certificationsStr,
                $previousExpStr,
                $presentation
            ]);

            header('Location: /centre-formation/auth/loginprofesseur.php');
            exit;
        } catch (PDOException $e) {
            $errors['database'] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Formateur</title>
    <link rel="stylesheet" href="../css/sinscrireprofesseur.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }
    </style>
</head>
<body>
<?php require_once '../include/hedear.php'; ?>

<div class="container">
    <div class="form-card">
        <div class="header">
            <div class="icon"><i class="fas fa-book"></i></div>
            <h1>Inscription Formateur</h1>
            <p class="subtitle">Créez votre profil professionnel de formateur</p>
        </div>

        <form id="trainerForm" method="POST">
            <div class="section">
                <h2>Informations personnelles</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullName">Nom complet *</label>
                        <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($fullName ?? '') ?>" required>
                        <?php if (isset($errors['fullName'])): ?><p class="error-message"><?= $errors['fullName']; ?></p><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="email">Email professionnel *</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?><p class="error-message"><?= $errors['email']; ?></p><?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone">Numéro de téléphone *</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>" required>
                    <?php if (isset($errors['phone'])): ?><p class="error-message"><?= $errors['phone']; ?></p><?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group password-wrapper">
                        <label for="password">Mot de passe *</label>
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                        <?php if (isset($errors['password'])): ?><p class="error-message"><?= $errors['password']; ?></p><?php endif; ?>
                    </div>
                    <div class="form-group password-wrapper">
                        <label for="confirmPassword">Confirmer le mot de passe *</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmPassword', this)"></i>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Expérience professionnelle</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="specialization">Spécialisation *</label>
                        <select id="specialization" name="specialization" required>
                            <option value="">Choisir une spécialisation</option>
                            <?php foreach ($specializationsList as $spec): ?>
                                <option value="<?= htmlspecialchars($spec['name']) ?>" <?= ($specialization ?? '') === $spec['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($spec['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['specialization'])): ?><p class="error-message"><?= $errors['specialization']; ?></p><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="experience">Années d'expérience *</label>
                        <input type="number" id="experience" name="experience" min="0" value="<?= htmlspecialchars($experience ?? '') ?>" required>
                        <?php if (isset($errors['experience'])): ?><p class="error-message"><?= $errors['experience']; ?></p><?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="education">Formation académique</label>
                    <input type="text" id="education" name="education" value="<?= htmlspecialchars($education ?? '') ?>">
                </div>
                <div class="form-group" id="certifications-container">
                    <label>Certifications professionnelles</label>
                    <input type="text" name="certifications[]" placeholder="Ex: Google Analytics, TOEIC">
                    <button type="button" class="add-button" onclick="addCertification()"><i class="fas fa-plus"></i> Ajouter</button>
                </div>
                <div class="form-group" id="experiences-container">
                    <label>Expériences précédentes</label>
                    <input type="text" name="experiences[]" placeholder="Ex: Formateur chez IBM (2018-2021)">
                    <button type="button" class="add-button" onclick="addExperience()"><i class="fas fa-plus"></i> Ajouter</button>
                </div>
                <div class="form-group">
                    <label for="presentation">Présentation personnelle</label>
                    <textarea id="presentation" name="presentation" rows="4"><?= htmlspecialchars($presentation ?? '') ?></textarea>
                </div>
            </div>

            <button type="submit" class="submit-button">Créer mon profil formateur</button>
            <div class="footer-text">Déjà un compte ? <a href="/centre-formation/auth/loginprofesseur.php">Se connecter</a></div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId, icon) {
    const input = document.getElementById(fieldId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

function addCertification() {
    const container = document.getElementById("certifications-container");
    const input = document.createElement("input");
    input.type = "text";
    input.name = "certifications[]";
    input.placeholder = "Nouvelle certification";
    input.style.marginTop = "8px";
    container.appendChild(input);
}

function addExperience() {
    const container = document.getElementById("experiences-container");
    const input = document.createElement("input");
    input.type = "text";
    input.name = "experiences[]";
    input.placeholder = "Nouvelle expérience";
    input.style.marginTop = "8px";
    container.appendChild(input);
}
function togglePassword(fieldId, icon) {
    const input = document.getElementById(fieldId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

</script>
</body>
</html>

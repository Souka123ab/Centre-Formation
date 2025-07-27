<?php
require_once '../config/database.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, email, role, phone, created_at, specialization, experience, education, certifications, previous_experience, presentation 
        FROM users 
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .profile-info {
            margin-top: 20px;
        }
        .profile-info p {
            margin: 10px 0;
            font-size: 16px;
        }
        .profile-info strong {
            color: #2c3e50;
        }
        .back-link,
        .edit-button {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
        }
        .back-link {
            color: #3498db;
        }
        .edit-button {
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            width: fit-content;
            margin: 20px auto;
        }
        .edit-button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <?php 
    // require_once '../include/hedear.php';
     ?>
    <div class="container">
        <h1>Profil Utilisateur</h1>
        <div class="profile-info">
            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Rôle :</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Non spécifié'); ?></p>
            <p><strong>Date de création :</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
            <p><strong>Spécialisation :</strong> <?php echo htmlspecialchars($user['specialization'] ?? 'Non spécifié'); ?></p>
            <p><strong>Expérience (années) :</strong> <?php echo htmlspecialchars($user['experience'] ?? 'Non spécifié'); ?></p>
            <p><strong>Éducation :</strong> <?php echo htmlspecialchars($user['education'] ?? 'Non spécifié'); ?></p>
            <p><strong>Certifications :</strong> <?php echo htmlspecialchars($user['certifications'] ?? 'Non spécifié'); ?></p>
            <p><strong>Expérience précédente :</strong> <?php echo htmlspecialchars($user['previous_experience'] ?? 'Non spécifié'); ?></p>
            <p><strong>Présentation :</strong> <?php echo htmlspecialchars($user['presentation'] ?? 'Non spécifié'); ?></p>
        </div>

        <a href="modifier-profil.php" class="edit-button">Modifier le profil</a>
        <a href="dasbordP.php" class="back-link">Retour au tableau de bord</a>
    </div>
</body>
</html>

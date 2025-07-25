<!-- <?php
session_start();
require_once '../config/database.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT u.id, u.full_name, u.email
        FROM users u
        JOIN inscriptions i ON i.etudiant_id = u.id
        JOIN cours c ON c.id = i.cours_id
        WHERE c.created_by = :user_id AND u.role = 'etudiant'
        ORDER BY u.full_name ASC
    ");
    $stmt->execute(['user_id' => $user_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes étudiants</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Liste des étudiants inscrits à mes cours</h1>

    <?php if (empty($students)): ?>
        <p>Aucun étudiant n'est inscrit à vos cours pour le moment.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Nom complet</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html> -->

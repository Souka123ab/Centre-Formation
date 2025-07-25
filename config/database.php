<?php
$host = 'localhost';
$db   = 'trainingcentre';  // nom de ta base
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Connexion réussie à la base de données.";
} catch (\PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

<?php
// db.php — connexion à la base de données distante

$host = '10.10.51.122';
$db   = 'dbgroupe2';
$user = 'admin';
$pass = 'Groupe2';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try { // Crée une instance PDO pour se connecter à la base de données
    // Vérification de l'extension PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion : " . $e->getMessage()]);
    exit;
}

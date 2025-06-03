<?php
// db.php — connexion à la base de données distante

$host = '10.10.51.122';  // ← IP de ton serveur MySQL
$db   = 'dbgroupe2';
$user = 'admin';
$pass = 'Groupe2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion : " . $e->getMessage()]);
    exit;
}

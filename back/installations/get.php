<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once(__DIR__ . '/../db.php');

try {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("
            SELECT i.*, l.latitude, l.longitude
            FROM installation i
            JOIN localisation l ON i.id_localistation = l.id_localistation
            WHERE i.id_installation = ?
        ");
        $stmt->execute([$_GET['id']]);
        $result = $stmt->fetch();
    } else {
        $stmt = $pdo->query("
            SELECT i.*, l.latitude, l.longitude
            FROM installation i
            JOIN localisation l ON i.id_localistation = l.id_localistation
        ");
        $result = $stmt->fetchAll();
    }

    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
}

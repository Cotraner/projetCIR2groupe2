<?php
require_once '../db.php';

try {
    $stmt = $pdo->query("SELECT * FROM installation");
    $installations = $stmt->fetchAll();
    echo json_encode($installations);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération : ' . $e->getMessage()]);
}

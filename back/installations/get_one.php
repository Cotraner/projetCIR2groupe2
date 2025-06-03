<?php
require_once(__DIR__ . '/../db.php');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

$id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT i.*, l.latitude, l.longitude
        FROM installation i
        JOIN localisation l ON i.id_localistation = l.id_localistation
        WHERE i.id_installation = ?
    ");
    $stmt->execute([$id]);
    $installation = $stmt->fetch();

    if ($installation) {
        echo json_encode($installation);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Installation non trouvée']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de lecture : ' . $e->getMessage()]);
}

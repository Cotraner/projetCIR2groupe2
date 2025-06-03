<?php
require_once '../db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de l\'installation manquant']);
    exit;
}

$id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM installation WHERE id_installation = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['message' => 'Installation supprimée']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Installation non trouvée']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
}

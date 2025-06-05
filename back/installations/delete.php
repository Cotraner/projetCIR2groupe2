<?php
require_once '../db.php';
header("Content-Type: application/json");

// Récupération de l'ID via le corps JSON
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => "ID d'installation manquant"]);
    exit;
}

try {
    $pdo->beginTransaction();

    // Supprimer les panneaux liés
    $stmt = $pdo->prepare("DELETE FROM panneau WHERE id_installation = ?");
    $stmt->execute([$id]);

    // Supprimer l'installation
    $stmt = $pdo->prepare("DELETE FROM installation WHERE id_installation = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    echo json_encode(['message' => "Installation $id supprimée avec succès"]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
}

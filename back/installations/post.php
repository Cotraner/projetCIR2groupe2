<?php
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
}

try {
    $sql = "INSERT INTO installation (
        date_installation, nb_panneaux, surface, puissance_crete,
        nb_ondulateur, pente, pente_opti, orientation, orientation_opti, prod_pvgis
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['date_installation'],
        $data['nb_panneaux'],
        $data['surface'],
        $data['puissance_crete'],
        $data['nb_ondulateur'],
        $data['pente'],
        $data['pente_opti'],
        $data['orientation'],
        $data['orientation_opti'],
        $data['prod_pvgis']
    ]);

    echo json_encode(['message' => 'Installation ajoutée', 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'ajout : ' . $e->getMessage()]);
}

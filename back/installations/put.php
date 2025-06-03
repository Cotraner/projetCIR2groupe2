<?php
require_once '../db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de l\'installation manquant']);
    exit;
}

$id = (int) $_GET['id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
}

try {
    $sql = "UPDATE installation SET
        date_installation = ?,
        nb_panneaux = ?,
        surface = ?,
        puissance_crete = ?,
        nb_ondulateur = ?,
        pente = ?,
        pente_opti = ?,
        orientation = ?,
        orientation_opti = ?,
        prod_pvgis = ?
        WHERE id_installation = ?";

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
        $data['prod_pvgis'],
        $id
    ]);

    echo json_encode(['message' => 'Installation mise à jour']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
}

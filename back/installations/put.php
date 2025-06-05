<?php
require_once '../db.php';
header("Content-Type: application/json");

// Vérifie que l'ID est dans l'URL
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => "ID d'installation manquant"]);
    exit;
}
$id = (int) $_GET['id'];

// Données reçues en JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Données JSON invalides']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Mise à jour des champs directs
    $stmt = $pdo->prepare("UPDATE installation SET
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
        WHERE id_installation = ?");
    $stmt->execute([
        $data['date_installation'],
        $data['nb_panneaux'],
        $data['surface'],
        $data['puissance_crete'],
        $data['nb_onduleur'],
        $data['pente'],
        $data['pente_optimum'],
        $data['orientation'],
        $data['orientation_optimum'],
        $data['production_pvgis'],
        $id
    ]);

    // 2. Mise à jour de localisation (par code INSEE)
    $stmt = $pdo->prepare("UPDATE localisation
        SET latitude = ?, longitude = ?
        WHERE code_INSEE = ?");
    $stmt->execute([
        $data['lat'],
        $data['lon'],
        $data['code_insee']
    ]);

    // 3. Mise à jour commune (ville / code postal / départ.)
    $stmt = $pdo->prepare("UPDATE commune
        SET nom_commune = ?, code_pos = ?, dep_code = ?
        WHERE code_INSEE = ?");
    $stmt->execute([
        $data['locality'],
        $data['postal_code'],
        $data['administrative_area_level_2'],
        $data['code_insee']
    ]);

    // 4. Mise à jour département
    $stmt = $pdo->prepare("UPDATE departement
        SET dep_nom = ?, reg_code = ?
        WHERE dep_code = ?");
    $stmt->execute([
        $data['administrative_area_level_2'],
        $data['administrative_area_level_1'],
        $data['administrative_area_level_2']
    ]);

    // 5. Mise à jour région
    $stmt = $pdo->prepare("UPDATE region
        SET reg_nom = ?
        WHERE reg_code = ?");
    $stmt->execute([
        $data['administrative_area_level_1'],
        $data['administrative_area_level_1']
    ]);

    $pdo->commit();
    echo json_encode(['message' => 'Installation mise à jour avec succès']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
}

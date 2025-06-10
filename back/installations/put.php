<?php
require_once '../db.php';
header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => "ID actuel manquant"]);
    exit;
}

$id_actuel = $_GET['id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => "JSON invalide"]);
    exit;
}

$nouvel_id = $data['nouvel_id_installation'] ?? $id_actuel;

try {
    $pdo->beginTransaction();

    // Vérifier ou insérer pays
    $stmt = $pdo->prepare("SELECT id_pays FROM pays WHERE nom_pays = ?");
    $stmt->execute([$data['country']]);
    $id_pays = $stmt->fetchColumn();
    if (!$id_pays) {
        $stmt = $pdo->prepare("INSERT INTO pays (nom_pays) VALUES (?)");
        $stmt->execute([$data['country']]);
        $id_pays = $pdo->lastInsertId();
    }

    // Vérifier ou insérer région
    $stmt = $pdo->prepare("SELECT reg_code FROM region WHERE reg_nom = ?");
    $stmt->execute([$data['administrative_area_level_1']]);
    $reg_code = $stmt->fetchColumn();
    if (!$reg_code) {
        $stmt = $pdo->prepare("INSERT INTO region (reg_nom, id_pays) VALUES (?, ?)");
        $stmt->execute([$data['administrative_area_level_1'], $id_pays]);
        $reg_code = $pdo->lastInsertId();
    }

    // Vérifier ou insérer département
    $stmt = $pdo->prepare("SELECT dep_code FROM departement WHERE dep_code = ?");
    $stmt->execute([$data['administrative_area_level_2']]);
    $dep_code = $stmt->fetchColumn();
    if (!$dep_code) {
        $stmt = $pdo->prepare("INSERT INTO departement (dep_code, dep_nom, reg_code) VALUES (?, ?, ?)");
        $stmt->execute([$data['administrative_area_level_2'], $data['administrative_area_level_2'], $reg_code]);
    }

    // Vérifier ou insérer commune
    $stmt = $pdo->prepare("SELECT code_INSEE FROM commune WHERE code_INSEE = ?");
    $stmt->execute([$data['code_insee']]);
    $insee = $stmt->fetchColumn();
    if (!$insee) {
        $stmt = $pdo->prepare("INSERT INTO commune (code_INSEE, nom_commune, population, code_pos, dep_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['code_insee'], $data['locality'], 0, $data['postal_code'], $data['administrative_area_level_2']]);
    }

    // Vérifier ou insérer localisation
    $stmt = $pdo->prepare("SELECT id_localisation FROM localisation WHERE latitude = ? AND longitude = ? AND code_INSEE = ?");
    $stmt->execute([$data['lat'], $data['lon'], $data['code_insee']]);
    $id_localisation = $stmt->fetchColumn();
    if (!$id_localisation) {
        $stmt = $pdo->prepare("INSERT INTO localisation (latitude, longitude, code_INSEE) VALUES (?, ?, ?)");
        $stmt->execute([$data['lat'], $data['lon'], $data['code_insee']]);
        $id_localisation = $pdo->lastInsertId();
    }

    // Vérifier ou insérer installateur
    $stmt = $pdo->prepare("SELECT id_installateur FROM installateur WHERE nom_installateur = ?");
    $stmt->execute([$data['installateur']]);
    $id_installateur = $stmt->fetchColumn();
    if (!$id_installateur) {
        $stmt = $pdo->prepare("INSERT INTO installateur (nom_installateur) VALUES (?)");
        $stmt->execute([$data['installateur']]);
        $id_installateur = $pdo->lastInsertId();
    }

    // Update installation
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
        prod_pvgis = ?,
        id_localisation = ?,
        id_installateur = ?
        WHERE id_installation = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['date_installation'] ?? null,
        $data['nb_panneaux'] ?? 0,
        $data['surface'] ?? 0,
        $data['puissance_crete'] ?? 0,
        $data['nb_onduleur'] ?? 0,
        $data['pente'] ?? 0,
        $data['pente_optimum'] ?? 0,
        $data['orientation'] ?? 0,
        $data['orientation_optimum'] ?? 0,
        $data['production_pvgis'] ?? 0,
        $id_localisation,
        $id_installateur,
        $id_actuel
    ]);

    $pdo->commit();
    echo json_encode(['message' => "Installation mise à jour"]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
}

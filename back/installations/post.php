<?php
require_once '../db.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Pays
    $stmt = $pdo->prepare("INSERT IGNORE INTO pays (nom_pays) VALUES (?)");
    $stmt->execute([$data['country']]);
    $id_pays = $pdo->lastInsertId() ?: $pdo->query("SELECT id_pays FROM pays WHERE nom_pays = " . $pdo->quote($data['country']))->fetchColumn();

    // Région
    $stmt = $pdo->prepare("INSERT IGNORE INTO region (reg_code, reg_nom, id_pays) VALUES (?, ?, ?)");
    $stmt->execute([$data['administrative_area_level_1'], $data['administrative_area_level_1'], $id_pays]);

    // Département
    $stmt = $pdo->prepare("INSERT IGNORE INTO departement (dep_code, dep_nom, reg_code) VALUES (?, ?, ?)");
    $stmt->execute([$data['administrative_area_level_2'], $data['administrative_area_level_2'], $data['administrative_area_level_1']]);

    // Commune
    $stmt = $pdo->prepare("INSERT IGNORE INTO commune (code_INSEE, nom_commune, population, code_pos, dep_code) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['code_insee'],
        $data['locality'],
        0,
        $data['postal_code'],
        $data['administrative_area_level_2']
    ]);

    // Localisation
    $stmt = $pdo->prepare("INSERT INTO localisation (latitude, longitude, code_INSEE) VALUES (?, ?, ?)");
    $stmt->execute([$data['lat'], $data['lon'], $data['code_insee']]);
    $id_localisation = $pdo->lastInsertId();

    // Installateur
    $stmt = $pdo->prepare("INSERT IGNORE INTO installateur (nom_installateur) VALUES (?)");
    $stmt->execute([$data['installateur']]);
    $id_installateur = $pdo->lastInsertId() ?: $pdo->query("SELECT id_installateur FROM installateur WHERE nom_installateur = " . $pdo->quote($data['installateur']))->fetchColumn();

    // Marque / Modèle onduleur
    $stmt = $pdo->prepare("INSERT IGNORE INTO marque_onduleur (marque) VALUES (?)");
    $stmt->execute([$data['onduleur_marque']]);
    $id_marque_onduleur = $pdo->lastInsertId() ?: $pdo->query("SELECT id_marque FROM marque_onduleur WHERE marque = " . $pdo->quote($data['onduleur_marque']))->fetchColumn();

    $stmt = $pdo->prepare("INSERT IGNORE INTO modele_onduleur (modele_onduleur) VALUES (?)");
    $stmt->execute([$data['onduleur_modele']]);
    $id_modele_onduleur = $pdo->lastInsertId() ?: $pdo->query("SELECT id_modele FROM modele_onduleur WHERE modele_onduleur = " . $pdo->quote($data['onduleur_modele']))->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO onduleur (id_modele, id_marque) VALUES (?, ?)");
    $stmt->execute([$id_modele_onduleur, $id_marque_onduleur]);
    $id_onduleur = $pdo->lastInsertId();

    // Marque / Modèle panneau
    $stmt = $pdo->prepare("INSERT IGNORE INTO marque_panneau (marque) VALUES (?)");
    $stmt->execute([$data['panneaux_marque']]);
    $id_marque_panneau = $pdo->lastInsertId() ?: $pdo->query("SELECT id_marque FROM marque_panneau WHERE marque = " . $pdo->quote($data['panneaux_marque']))->fetchColumn();

    $stmt = $pdo->prepare("INSERT IGNORE INTO modele_panneau (modele) VALUES (?)");
    $stmt->execute([$data['panneaux_modele']]);
    $id_modele_panneau = $pdo->lastInsertId() ?: $pdo->query("SELECT id_modele FROM modele_panneau WHERE modele = " . $pdo->quote($data['panneaux_modele']))->fetchColumn();

    // Installation
    $stmt = $pdo->prepare("INSERT INTO installation (
        date_installation, nb_panneaux, surface, puissance_crete,
        nb_ondulateur, pente, pente_opti, orientation, orientation_opti,
        prod_pvgis, id_onduleur, id_localisation, id_installateur
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

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
        $id_onduleur,
        $id_localisation,
        $id_installateur
    ]);

    $id_installation = $pdo->lastInsertId();

    // Panneau
    $stmt = $pdo->prepare("INSERT INTO panneau (id_installation, id_marque, id_modele) VALUES (?, ?, ?)");
    $stmt->execute([$id_installation, $id_marque_panneau, $id_modele_panneau]);

    $pdo->commit();

    echo json_encode(['message' => 'Installation ajoutée avec succès', 'id' => $id_installation]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
}

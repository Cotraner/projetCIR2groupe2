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
    $stmt = $pdo->prepare("SELECT id_pays FROM pays WHERE nom_pays = ?");
    $stmt->execute([$data['country']]);
    $id_pays = $stmt->fetchColumn();
    if (!$id_pays) {
        $stmt = $pdo->prepare("INSERT INTO pays (nom_pays) VALUES (?)");
        $stmt->execute([$data['country']]);
        $id_pays = $pdo->lastInsertId();
    }

    $reg_code = $data['reg_code']; // doit être un entier, par exemple 52
    $reg_nom = $data['administrative_area_level_1']; // le nom complet

    $stmt = $pdo->prepare("SELECT reg_code FROM region WHERE reg_code = ?");
    $stmt->execute([$reg_code]);
    $existing = $stmt->fetchColumn();
    if (!$existing) {
        $stmt = $pdo->prepare("INSERT INTO region (reg_code, reg_nom, id_pays) VALUES (?, ?, ?)");
        $stmt->execute([$reg_code, $reg_nom, $id_pays]);
    }

    // Département
    $stmt = $pdo->prepare("SELECT dep_code FROM departement WHERE dep_code = ?");
    $stmt->execute([$data['administrative_area_level_2']]);
    $dep_code = $stmt->fetchColumn();
    if (!$dep_code) {
        $stmt = $pdo->prepare("INSERT INTO departement (dep_code, dep_nom, reg_code) VALUES (?, ?, ?)");
        $stmt->execute([$data['administrative_area_level_2'], $data['administrative_area_level_2'], $reg_code]);
    }

    // Commune
    $stmt = $pdo->prepare("SELECT code_INSEE FROM commune WHERE code_INSEE = ?");
    $stmt->execute([$data['code_insee']]);
    $insee = $stmt->fetchColumn();
    if (!$insee) {
        $stmt = $pdo->prepare("INSERT INTO commune (code_INSEE, nom_commune, population, code_pos, dep_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['code_insee'], $data['locality'], 0, $data['postal_code'], $data['administrative_area_level_2']]);
    }

    // Localisation
    $stmt = $pdo->prepare("SELECT id_localisation FROM localisation WHERE latitude = ? AND longitude = ? AND code_INSEE = ?");
    $stmt->execute([$data['lat'], $data['lon'], $data['code_insee']]);
    $id_localisation = $stmt->fetchColumn();
    if (!$id_localisation) {
        $stmt = $pdo->prepare("INSERT INTO localisation (latitude, longitude, code_INSEE) VALUES (?, ?, ?)");
        $stmt->execute([$data['lat'], $data['lon'], $data['code_insee']]);
        $id_localisation = $pdo->lastInsertId();
    }

    // Installateur
    $stmt = $pdo->prepare("SELECT id_installateur FROM installateur WHERE nom_installateur = ?");
    $stmt->execute([$data['installateur']]);
    $id_installateur = $stmt->fetchColumn();
    if (!$id_installateur) {
        $stmt = $pdo->prepare("INSERT INTO installateur (nom_installateur) VALUES (?)");
        $stmt->execute([$data['installateur']]);
        $id_installateur = $pdo->lastInsertId();
    }

    // Marque onduleur
    $stmt = $pdo->prepare("SELECT id_marque FROM marque_onduleur WHERE marque = ?");
    $stmt->execute([$data['onduleur_marque']]);
    $id_marque_onduleur = $stmt->fetchColumn();
    if (!$id_marque_onduleur) {
        $stmt = $pdo->prepare("INSERT INTO marque_onduleur (marque) VALUES (?)");
        $stmt->execute([$data['onduleur_marque']]);
        $id_marque_onduleur = $pdo->lastInsertId();
    }

    // Modèle onduleur
    $stmt = $pdo->prepare("SELECT id_modele FROM modele_onduleur WHERE modele_onduleur = ?");
    $stmt->execute([$data['onduleur_modele']]);
    $id_modele_onduleur = $stmt->fetchColumn();
    if (!$id_modele_onduleur) {
        $stmt = $pdo->prepare("INSERT INTO modele_onduleur (modele_onduleur) VALUES (?)");
        $stmt->execute([$data['onduleur_modele']]);
        $id_modele_onduleur = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare("INSERT INTO onduleur (id_modele, id_marque) VALUES (?, ?)");
    $stmt->execute([$id_modele_onduleur, $id_marque_onduleur]);
    $id_onduleur = $pdo->lastInsertId();

    // Marque panneau
    $stmt = $pdo->prepare("SELECT id_marque FROM marque_panneau WHERE marque = ?");
    $stmt->execute([$data['panneaux_marque']]);
    $id_marque_panneau = $stmt->fetchColumn();
    if (!$id_marque_panneau) {
        $stmt = $pdo->prepare("INSERT INTO marque_panneau (marque) VALUES (?)");
        $stmt->execute([$data['panneaux_marque']]);
        $id_marque_panneau = $pdo->lastInsertId();
    }

    // Modèle panneau
    $stmt = $pdo->prepare("SELECT id_modele FROM modele_panneau WHERE modele = ?");
    $stmt->execute([$data['panneaux_modele']]);
    $id_modele_panneau = $stmt->fetchColumn();
    if (!$id_modele_panneau) {
        $stmt = $pdo->prepare("INSERT INTO modele_panneau (modele) VALUES (?)");
        $stmt->execute([$data['panneaux_modele']]);
        $id_modele_panneau = $pdo->lastInsertId();
    }

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
} catch (PDOException $e) { // En cas d'erreur lors de la requête
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
}

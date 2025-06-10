<?php
require_once '../db.php';
header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => "ID actuel manquant"]);
    exit;
}

$id_actuel = (int)$_GET['id'];
$data      = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => "JSON invalide"]);
    exit;
}

$nouvel_id = $data['nouvel_id_installation'] ?? $id_actuel;

try {
    $pdo->beginTransaction();

    /* ------------------------------------------------------------------
     * 1. GÉO : pays → région → département → commune → localisation
     * ------------------------------------------------------------------*/
    /* … (ta partie géo inchangée, conservée) … */

    /* ------------------------------------------------------------------
     * 2. INSTALLATEUR
     * ------------------------------------------------------------------*/
    /* … (ta partie installateur inchangée) … */

    /* ------------------------------------------------------------------
     * 3. ONDULEUR  (marque + modèle → id_onduleur)
     * ------------------------------------------------------------------*/
    // Marque
    $stmt = $pdo->prepare("SELECT id_marque FROM marque_onduleur WHERE marque = ?");
    $stmt->execute([$data['marque_onduleur']]);
    $id_marque_onduleur = $stmt->fetchColumn();
    if (!$id_marque_onduleur) {
        $stmt = $pdo->prepare("INSERT INTO marque_onduleur (marque) VALUES (?)");
        $stmt->execute([$data['marque_onduleur']]);
        $id_marque_onduleur = $pdo->lastInsertId();
    }

    // Modèle
    $stmt = $pdo->prepare("SELECT id_modele FROM modele_onduleur WHERE modele_onduleur = ?");
    $stmt->execute([$data['modele_onduleur']]);
    $id_modele_onduleur = $stmt->fetchColumn();
    if (!$id_modele_onduleur) {
        $stmt = $pdo->prepare("INSERT INTO modele_onduleur (modele_onduleur) VALUES (?)");
        $stmt->execute([$data['modele_onduleur']]);
        $id_modele_onduleur = $pdo->lastInsertId();
    }

    // Onduleur (couple marque/modèle)
    $stmt = $pdo->prepare("
        SELECT id_onduleur
        FROM onduleur
        WHERE id_marque = ? AND id_modele = ?
    ");
    $stmt->execute([$id_marque_onduleur, $id_modele_onduleur]);
    $id_onduleur = $stmt->fetchColumn();
    if (!$id_onduleur) {
        $stmt = $pdo->prepare("
            INSERT INTO onduleur (id_modele, id_marque)
            VALUES (?, ?)
        ");
        $stmt->execute([$id_modele_onduleur, $id_marque_onduleur]);
        $id_onduleur = $pdo->lastInsertId();
    }

    /* ------------------------------------------------------------------
     * 4. PANNEAUX  (marque + modèle)  — on recrée les N panneaux de l’installation
     * ------------------------------------------------------------------*/
    // Marque
    $stmt = $pdo->prepare("SELECT id_marque FROM marque_panneau WHERE marque = ?");
    $stmt->execute([$data['marque_panneau']]);
    $id_marque_panneau = $stmt->fetchColumn();
    if (!$id_marque_panneau) {
        $stmt = $pdo->prepare("INSERT INTO marque_panneau (marque) VALUES (?)");
        $stmt->execute([$data['marque_panneau']]);
        $id_marque_panneau = $pdo->lastInsertId();
    }

    // Modèle
    $stmt = $pdo->prepare("SELECT id_modele FROM modele_panneau WHERE modele = ?");
    $stmt->execute([$data['modele_panneau']]);
    $id_modele_panneau = $stmt->fetchColumn();
    if (!$id_modele_panneau) {
        $stmt = $pdo->prepare("INSERT INTO modele_panneau (modele) VALUES (?)");
        $stmt->execute([$data['modele_panneau']]);
        $id_modele_panneau = $pdo->lastInsertId();
    }

    // On supprime les panneaux existants de l’installation
    $stmt = $pdo->prepare("DELETE FROM panneau WHERE id_installation = ?");
    $stmt->execute([$id_actuel]);

    // Puis on recrée exactement nb_panneaux en liant la marque et le modèle
    $nbPanneaux = max(0, (int)($data['nb_panneaux'] ?? 0));
    if ($nbPanneaux > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO panneau (id_installation, id_marque, id_modele)
            VALUES (?, ?, ?)
        ");
        for ($i = 0; $i < $nbPanneaux; $i++) {
            $stmt->execute([$nouvel_id, $id_marque_panneau, $id_modele_panneau]);
        }
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
    /* ------------------------------------------------------------------
     * 5. MISE À JOUR DE L’INSTALLATION
     * ------------------------------------------------------------------*/
    $sql = "
        UPDATE installation SET
            id_installation   = ?,
            date_installation = ?,
            nb_panneaux       = ?,
            surface           = ?,
            puissance_crete   = ?,
            nb_ondulateur     = ?,
            pente             = ?,
            pente_opti        = ?,
            orientation       = ?,
            orientation_opti  = ?,
            prod_pvgis        = ?,
            id_onduleur       = ?,  
            id_localisation   = ?,
            id_installateur   = ?
        WHERE id_installation = ?
    ";

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
        $nouvel_id,
        $data['date_installation']     ?? null,
        $nbPanneaux,
        $data['surface']               ?? 0,
        $data['puissance_crete']       ?? 0,
        $data['nb_onduleur']           ?? 0,
        $data['pente']                 ?? 0,
        $data['pente_optimum']         ?? 0,
        $data['orientation']           ?? 0,
        $data['orientation_optimum']   ?? 0,
        $data['production_pvgis']      ?? 0,
        $id_onduleur,                    
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

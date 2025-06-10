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

// On garde l'ID actuel, pas de modification d'ID

try {
    $pdo->beginTransaction();
    
    // Marque onduleur - création automatique si n'existe pas
    $stmt = $pdo->prepare("SELECT id_marque FROM marque_onduleur WHERE marque = ?");
    $stmt->execute([$data['marque_onduleur']]);
    $id_marque_onduleur = $stmt->fetchColumn();
    if (!$id_marque_onduleur) {
        $stmt = $pdo->prepare("INSERT INTO marque_onduleur (marque) VALUES (?)");
        $stmt->execute([$data['marque_onduleur']]);
        $id_marque_onduleur = $pdo->lastInsertId();
    }

    // Modèle onduleur - création automatique si n'existe pas
    $stmt = $pdo->prepare("SELECT id_modele FROM modele_onduleur WHERE modele_onduleur = ?");
    $stmt->execute([$data['modele_onduleur']]);
    $id_modele_onduleur = $stmt->fetchColumn();
    if (!$id_modele_onduleur) {
        $stmt = $pdo->prepare("INSERT INTO modele_onduleur (modele_onduleur) VALUES (?)");
        $stmt->execute([$data['modele_onduleur']]);
        $id_modele_onduleur = $pdo->lastInsertId();
    }

    // Onduleur (couple marque/modèle) - création automatique si n'existe pas
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

    // Marque panneau - création automatique si n'existe pas
    $stmt = $pdo->prepare("SELECT id_marque FROM marque_panneau WHERE marque = ?");
    $stmt->execute([$data['marque_panneau']]);
    $id_marque_panneau = $stmt->fetchColumn();
    if (!$id_marque_panneau) {
        $stmt = $pdo->prepare("INSERT INTO marque_panneau (marque) VALUES (?)");
        $stmt->execute([$data['marque_panneau']]);
        $id_marque_panneau = $pdo->lastInsertId();
    }

    // Modèle panneau - création automatique si n'existe pas
    $stmt = $pdo->prepare("SELECT id_modele FROM modele_panneau WHERE modele = ?");
    $stmt->execute([$data['modele_panneau']]);
    $id_modele_panneau = $stmt->fetchColumn();
    if (!$id_modele_panneau) {
        $stmt = $pdo->prepare("INSERT INTO modele_panneau (modele) VALUES (?)");
        $stmt->execute([$data['modele_panneau']]);
        $id_modele_panneau = $pdo->lastInsertId();
    }

    // GESTION DE LA LOCALISATION
    $stmt = $pdo->prepare("
    SELECT id_localisation 
    FROM localisation 
    WHERE latitude = ? AND longitude = ? AND code_INSEE = ?
    ");
    $stmt->execute([$data['lat'], $data['lon'], $data['code_INSEE']]);
    $id_localisation = $stmt->fetchColumn();

    if (!$id_localisation) {
        // 2. Insérer la nouvelle localisation
        $stmt = $pdo->prepare("
            INSERT INTO localisation (latitude, longitude, code_INSEE) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $data['lat'],
            $data['lon'],
            $data['code_INSEE']
        ]);
        $id_localisation = $pdo->lastInsertId();
    }

    // GESTION DE L'INSTALLATEUR - création automatique si n'existe pas
    $stmt = $pdo->prepare("SELECT id_installateur FROM installateur WHERE nom = ?");
    $stmt->execute([$data['installateur']]);
    $id_installateur = $stmt->fetchColumn();
    if (!$id_installateur) {
        $stmt = $pdo->prepare("INSERT INTO installateur (nom) VALUES (?)");
        $stmt->execute([$data['installateur']]);
        $id_installateur = $pdo->lastInsertId();
    }

    // Supprimer les panneaux existants de l'installation
    $stmt = $pdo->prepare("DELETE FROM panneau WHERE id_installation = ?");
    $stmt->execute([$id_actuel]);
    
    // Recréer les panneaux
    $nbPanneaux = max(0, (int)($data['nb_panneaux'] ?? 0));
    if ($nbPanneaux > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO panneau (id_installation, id_marque, id_modele)
            VALUES (?, ?, ?)
        ");
        for ($i = 0; $i < $nbPanneaux; $i++) {
            $stmt->execute([$id_actuel, $id_marque_panneau, $id_modele_panneau]);
        }
    }

    // Créer la date d'installation
    $date_installation = null;
    if (!empty($data['mois_installation']) && !empty($data['an_installation'])) {
        $mois = str_pad($data['mois_installation'], 2, '0', STR_PAD_LEFT);
        $date_installation = $data['an_installation'] . '-' . $mois . '-01';
    }

    $sql = "
        UPDATE installation SET
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
        $date_installation,                      // date_installation = ?
        $data['nb_panneaux'] ?? 0,              // nb_panneaux = ?
        $data['surface'] ?? 0,                  // surface = ?
        $data['puissance_crete'] ?? 0,          // puissance_crete = ?
        $data['nb_onduleur'] ?? 0,              // nb_ondulateur = ?
        $data['pente'] ?? 0,                    // pente = ?
        $data['pente_optimum'] ?? 0,            // pente_opti = ?
        $data['orientation'] ?? 0,              // orientation = ?
        $data['orientation_optimum'] ?? 0,      // orientation_opti = ?
        $data['production_pvgis'] ?? 0,         // prod_pvgis = ?
        $id_onduleur,                           // id_onduleur = ?
        $id_localisation,                       // id_localisation = ?
        $id_installateur,                       // id_installateur = ?
        $id_actuel                              // WHERE id_installation = ?
    ]);

    $pdo->commit();
    echo json_encode(['message' => "Installation mise à jour"]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
}
?>
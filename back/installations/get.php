<?php
// Récupération des installations depuis la base de données
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');// Définition du type de contenu pour la réponse JSON
require_once(__DIR__ . '/../db.php');

try {
    if (isset($_GET['id'])) {// Si un ID est fourni, on récupère une seule installation
        $stmt = $pdo->prepare("
            SELECT i.*, 
                   l.latitude, l.longitude, 
                   d.dep_code, d.dep_nom,
                   mo.marque AS marque_onduleur,
                   GROUP_CONCAT(DISTINCT mp.marque) AS marques_panneaux
            FROM installation i
            JOIN localisation l ON i.id_localisation = l.id_localisation
            JOIN commune c ON l.code_INSEE = c.code_INSEE
            JOIN departement d ON c.dep_code = d.dep_code
            JOIN onduleur o ON i.id_onduleur = o.id_onduleur
            JOIN marque_onduleur mo ON o.id_marque = mo.id_marque
            LEFT JOIN panneau p ON p.id_installation = i.id_installation
            LEFT JOIN marque_panneau mp ON p.id_marque = mp.id_marque
            WHERE i.id_installation = ?
            GROUP BY i.id_installation
        ");
        $stmt->execute([$_GET['id']]);
        $result = $stmt->fetch();
    } else {// Si aucun ID n'est fourni, on récupère toutes les installations
        $stmt = $pdo->query("
            SELECT i.*, 
                   l.latitude, l.longitude, 
                   d.dep_code, d.dep_nom,
                   mo.marque AS marque_onduleur,
                   GROUP_CONCAT(DISTINCT mp.marque) AS marques_panneaux
            FROM installation i
            JOIN localisation l ON i.id_localisation = l.id_localisation
            JOIN commune c ON l.code_INSEE = c.code_INSEE
            JOIN departement d ON c.dep_code = d.dep_code
            JOIN onduleur o ON i.id_onduleur = o.id_onduleur
            JOIN marque_onduleur mo ON o.id_marque = mo.id_marque
            LEFT JOIN panneau p ON p.id_installation = i.id_installation
            LEFT JOIN marque_panneau mp ON p.id_marque = mp.id_marque
            GROUP BY i.id_installation
        ");
        $result = $stmt->fetchAll();
    }

    echo json_encode($result);
} catch (PDOException $e) {// En cas d'erreur lors de la requête
    http_response_code(500);
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
}
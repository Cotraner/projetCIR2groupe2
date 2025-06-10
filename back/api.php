<?php
// Affiche les erreurs PHP pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Spécifie le format de la réponse
header('Content-Type: application/json');

// Inclusion sécurisée de la base de données
require_once(__DIR__ . '/db.php');

// Récupération de la méthode HTTP et des paramètres
$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? null;
$id = $_GET['id'] ?? null;

// Gestion des ressources disponibles
switch ($resource) {
    case 'stats':
        if ($method === 'GET') {
            $result = [];

            // Statistiques globales
            $result['total_installations'] = (int) $pdo->query("SELECT COUNT(*) FROM installation")->fetchColumn();

            $result['installations_par_annee'] = (int) $pdo->query("
                SELECT COUNT(DISTINCT YEAR(date_installation)) FROM installation
            ")->fetchColumn();

            $result['installations_par_region'] = (int) $pdo->query("
                SELECT COUNT(DISTINCT r.reg_nom)
                FROM installation i
                JOIN localisation l ON i.id_localisation = l.id_localisation
                JOIN commune c ON l.code_INSEE = c.code_INSEE
                JOIN departement d ON c.dep_code = d.dep_code
                JOIN region r ON d.reg_code = r.reg_code
            ")->fetchColumn();

            // Vrai total des installations groupées par année + région
            $result['installations_par_annee_region'] = (int) $pdo->query("
                SELECT COUNT(*) 
                FROM installation i
                JOIN localisation l ON i.id_localisation = l.id_localisation
                JOIN commune c ON l.code_INSEE = c.code_INSEE
                JOIN departement d ON c.dep_code = d.dep_code
                JOIN region r ON d.reg_code = r.reg_code
                WHERE YEAR(i.date_installation) IS NOT NULL AND r.reg_nom IS NOT NULL
            ")->fetchColumn();

            $result['nb_installateurs'] = (int) $pdo->query("SELECT COUNT(*) FROM installateur")->fetchColumn();

            $result['marques_onduleurs'] = (int) $pdo->query("SELECT COUNT(DISTINCT marque) FROM marque_onduleur")->fetchColumn();

            $result['marques_panneaux'] = (int) $pdo->query("SELECT COUNT(DISTINCT marque) FROM marque_panneau")->fetchColumn();

            // Années distinctes
            $result['annees'] = $pdo->query("
                SELECT DISTINCT YEAR(date_installation) AS annee 
                FROM installation 
                WHERE date_installation IS NOT NULL
                ORDER BY annee
            ")->fetchAll(PDO::FETCH_COLUMN);

            // Régions distinctes
            $result['regions'] = $pdo->query("
                SELECT DISTINCT r.reg_nom 
                FROM region r
                JOIN departement d ON d.reg_code = r.reg_code
                JOIN commune c ON c.dep_code = d.dep_code
                JOIN localisation l ON l.code_INSEE = c.code_INSEE
                JOIN installation i ON i.id_localisation = l.id_localisation
                WHERE r.reg_nom IS NOT NULL
                ORDER BY r.reg_nom
            ")->fetchAll(PDO::FETCH_COLUMN);

            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
        }
        break;
        // Gestion des installations
    case 'installations':
        if ($method === 'GET') {// Récupération des installations
            if ($id) {
                $stmt = $pdo->prepare("
                    SELECT 
                        i.*, 
                        l.latitude, 
                        l.longitude, 
                        d.dep_code, 
                        d.dep_nom,
                        mo.marque AS marque_onduleur,
                        mp.marque AS marque_panneau
                    FROM installation i
                    JOIN localisation l ON i.id_localisation = l.id_localisation
                    JOIN commune c ON l.code_INSEE = c.code_INSEE
                    JOIN departement d ON c.dep_code = d.dep_code
                    LEFT JOIN onduleur o ON i.id_onduleur = o.id_onduleur
                    LEFT JOIN marque_onduleur mo ON o.id_marque = mo.id_marque
                    LEFT JOIN panneau p ON p.id_installation = i.id_installation
                    LEFT JOIN marque_panneau mp ON p.id_marque = mp.id_marque
                    WHERE i.id_installation = ?
                ");
                $stmt->execute([$id]);
                echo json_encode($stmt->fetch());
            } else {// Récupération de toutes les installations
                $stmt = $pdo->query("
                    SELECT 
                        i.*, 
                        l.latitude, 
                        l.longitude, 
                        d.dep_code, 
                        d.dep_nom,
                        mo.marque AS marque_onduleur,
                        mp.marque AS marque_panneau
                    FROM installation i
                    JOIN localisation l ON i.id_localisation = l.id_localisation
                    JOIN commune c ON l.code_INSEE = c.code_INSEE
                    JOIN departement d ON c.dep_code = d.dep_code
                    LEFT JOIN onduleur o ON i.id_onduleur = o.id_onduleur
                    LEFT JOIN marque_onduleur mo ON o.id_marque = mo.id_marque
                    LEFT JOIN panneau p ON p.id_installation = i.id_installation
                    LEFT JOIN marque_panneau mp ON p.id_marque = mp.id_marque
                ");
                echo json_encode($stmt->fetchAll());
            }
        } elseif ($method === 'POST') { // Création d'une nouvelle installation
            require_once(__DIR__ . '/installations/post.php');
        } elseif (in_array($method, ['PUT', 'PATCH'])) { // Modification d'une installation existante
            if ($id) {
                $_GET['id'] = $id;
                require_once(__DIR__ . '/installations/put.php');
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID requis pour modification']);
            }
        } elseif ($method === 'DELETE') { // Suppression d'une installation
            if ($id) {
                $_GET['id'] = $id;
                require_once(__DIR__ . '/installations/delete.php');
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID requis pour suppression']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
        }
        break;
    case 'installations_par_annee_region':
    if ($method === 'GET') { // Récupération du nombre d'installations par année et région
        $annee = $_GET['annee'] ?? null;
        $region = $_GET['region'] ?? null;

        if ($annee && $region) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS nombre
                FROM installation i
                JOIN localisation l ON i.id_localisation = l.id_localisation
                JOIN commune c ON l.code_INSEE = c.code_INSEE
                JOIN departement d ON c.dep_code = d.dep_code
                JOIN region r ON d.reg_code = r.reg_code
                WHERE YEAR(i.date_installation) = :annee AND r.reg_nom = :region
            ");
            // Exécution de la requête avec les paramètres
            $stmt->execute([
                'annee' => $annee,
                'region' => $region
            ]);
            $result = $stmt->fetch();
            echo json_encode(['nombre_installations' => (int) $result['nombre']]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres année et région requis']);
        }
    } else {// Méthode non autorisée
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
    break;


    default:
        http_response_code(404);
        echo json_encode(['error' => 'Ressource inconnue']);
}

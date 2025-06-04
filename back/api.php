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
            $result = [
                'total_installations' => (int) $pdo->query("SELECT COUNT(*) FROM installation")->fetchColumn(),

                'installations_par_annee' => (int) $pdo->query("
                    SELECT COUNT(DISTINCT YEAR(date_installation)) FROM installation
                ")->fetchColumn(),

                'installations_par_region' => (int) $pdo->query("
                    SELECT COUNT(DISTINCT r.reg_nom)
                    FROM installation i
                    JOIN localisation l ON i.id_localisation = l.id_localisation
                    JOIN commune c ON l.code_INSEE = c.code_INSEE
                    JOIN departement d ON c.dep_code = d.dep_code
                    JOIN region r ON d.reg_code = r.reg_code
                ")->fetchColumn(),

                'installations_par_annee_region' => (int) $pdo->query("
                    SELECT COUNT(*) FROM (
                        SELECT YEAR(i.date_installation) AS annee, r.reg_nom
                        FROM installation i
                        JOIN localisation l ON i.id_localisation = l.id_localisation
                        JOIN commune c ON l.code_INSEE = c.code_INSEE
                        JOIN departement d ON c.dep_code = d.dep_code
                        JOIN region r ON d.reg_code = r.reg_code
                        GROUP BY annee, r.reg_nom
                    ) AS sub
                ")->fetchColumn(),

                'nb_installateurs' => (int) $pdo->query("SELECT COUNT(*) FROM installateur")->fetchColumn(),

                'marques_onduleurs' => (int) $pdo->query("SELECT COUNT(DISTINCT marque) FROM marque_onduleur")->fetchColumn(),

                'marques_panneaux' => (int) $pdo->query("SELECT COUNT(DISTINCT marque) FROM marque_panneau")->fetchColumn()
            ];

            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
        }
        break;

    case 'installations':
        if ($method === 'GET') {
            if ($id) {
                // Une seule installation
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
                $result = $stmt->fetch();
                echo json_encode($result);
            } else {
                // Toutes les installations
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
                $result = $stmt->fetchAll();
                echo json_encode($result);
            }
        } elseif ($method === 'POST') {
            require_once(__DIR__ . '/installations/post.php');
        } elseif (in_array($method, ['PUT', 'PATCH'])) {
            if ($id) {
                $_GET['id'] = $id;
                require_once(__DIR__ . '/installations/put.php');
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID requis pour modification']);
            }
        } elseif ($method === 'DELETE') {
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

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Ressource inconnue']);
}

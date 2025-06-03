<?php
// Affiche les erreurs PHP pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Spécifie le format de la réponse
header('Content-Type: application/json');

// Inclusion sécurisée de la base de données
require_once(__DIR__ . '/db.php'); // ✅ plus robuste

// Récupération de la méthode HTTP et des paramètres
$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? null;
$id = $_GET['id'] ?? null;

// Gestion des ressources disponibles
switch ($resource) {
    case 'installations':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $_GET['id'] = $id;
                    require_once(__DIR__ . '/installations/get_one.php'); // ✅ sécurise le chemin
                } else {
                    require_once(__DIR__ . '/installations/get.php');
                }
                break;

            case 'POST':
                require_once(__DIR__ . '/installations/post.php');
                break;

            case 'PUT':
            case 'PATCH':
                if ($id) {
                    $_GET['id'] = $id;
                    require_once(__DIR__ . '/installations/put.php');
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID requis pour modifier']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $_GET['id'] = $id;
                    require_once(__DIR__ . '/installations/delete.php');
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID requis pour suppression']);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Méthode non autorisée']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Ressource inconnue']);
}

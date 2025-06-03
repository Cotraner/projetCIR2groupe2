<?php
header('Content-Type: application/json');
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? null;
$id = $_GET['id'] ?? null;

switch ($resource) {
    case 'installations':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $_GET['id'] = $id;
                    require 'installations/get_one.php';  // si tu veux l'ajouter plus tard
                } else {
                    require 'installations/get.php';
                }
                break;

            case 'POST':
                require 'installations/post.php';
                break;

            case 'PUT':
            case 'PATCH':
                if ($id) {
                    $_GET['id'] = $id;
                    require 'installations/put.php';
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID requis pour modifier']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $_GET['id'] = $id;
                    require 'installations/delete.php';
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

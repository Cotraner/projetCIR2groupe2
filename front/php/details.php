<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../back/db.php');

$id = $_GET['id'] ?? null;
$data = null;
$error = null;

if ($id) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                i.id_installation,
                i.date_installation,
                i.surface,
                i.puissance_crete,
                i.nb_panneaux,
                l.latitude,
                l.longitude,
                c.nom_commune,
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
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if (!$data) {
            $error = "Installation introuvable.";
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération : " . $e->getMessage();
    }
} else {
    $error = "Aucun ID d’installation fourni.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Installation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link href="https://fonts.googleapis.com/css?family=Itim" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <style>
        body {
            font-family: 'Itim', cursive;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#"><img id="logo" src="../../images/logo-Soleil-SOLAIRE.png" alt="Logo Soleil"></a>
        <div class="collapse navbar-collapse justify-content-around w-100" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="recherches.php">Recherches</a></li>
                <li class="nav-item"><a class="nav-link" href="carte.php">Carte</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <h2 class="mb-4">Détails de l'installation #<?= $data['id_installation'] ?></h2>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>Date :</strong> <?= htmlspecialchars($data['date_installation']) ?></li>
                <li class="list-group-item"><strong>Surface :</strong> <?= htmlspecialchars($data['surface']) ?> m²</li>
                <li class="list-group-item"><strong>Puissance crête :</strong> <?= htmlspecialchars($data['puissance_crete']) ?> kWc</li>
                <li class="list-group-item"><strong>Nombre de panneaux :</strong> <?= htmlspecialchars($data['nb_panneaux']) ?></li>
                <li class="list-group-item"><strong>Onduleur :</strong> <?= htmlspecialchars($data['marque_onduleur']) ?></li>
                <li class="list-group-item"><strong>Panneaux :</strong> <?= htmlspecialchars($data['marque_panneau']) ?></li>
                <li class="list-group-item"><strong>Localisation :</strong> <?= htmlspecialchars($data['nom_commune']) ?> (<?= htmlspecialchars($data['dep_nom']) ?>)</li>
                <li class="list-group-item"><strong>Coordonnées GPS :</strong> <?= $data['latitude'] ?>, <?= $data['longitude'] ?></li>
            </ul>

            <?php if ($data['latitude'] && $data['longitude']): ?>
                <h4 class="mb-3">Localisation sur la carte</h4>
                <div id="map" style="height: 400px; border-radius: 10px;"></div>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const map = L.map('map').setView([<?= $data['latitude'] ?>, <?= $data['longitude'] ?>], 6);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                            minZoom: 1,
                            maxZoom: 20
                        }).addTo(map);
                        L.marker([<?= $data['latitude'] ?>, <?= $data['longitude'] ?>]).addTo(map);
                    });
                </script>
            <?php endif; ?>

            <!-- ✅ Bouton retour -->
            <div class="text-center mt-4">
                <button onclick="history.back()" class="btn btn-secondary">← Retour à la recherche</button>
            </div>
        <?php endif; ?>
    </div>

    <footer class="custom-footer text-yellow mt-5">
        <div class="container d-flex justify-content-between align-items-center py-3 flex-wrap">
            <div class="d-flex align-items-center">
                <img src="../../images/linkedin.png" alt="LinkedIn" class="linkedin-logo me-3">
                <div>
                    <p class="mb-0"><a href="https://www.linkedin.com/in/cl%C3%A9ment-robin123/">clementrobin</a></p>
                    <p class="mb-0">louislacoste</p>
                </div>
            </div>
            <div class="text-center flex-fill">
                <p class="mb-0 fw-bold">Groupe 2 CIR 2</p>
            </div>
            <div>
                <img src="../../images/isen.png" alt="ISEN" class="isen-logo">
            </div>
        </div>
    </footer>
</body>
</html>

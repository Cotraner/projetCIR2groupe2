<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Carte Soleil SOLAIRE</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/carte.css">
    <link href="https://fonts.googleapis.com/css?family=Itim" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="" defer></script>
    <script src="../../back/carte.js" defer></script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">
            <img id="logo" src="../../images/logo-Soleil-SOLAIRE.png" alt="Soleil Solaire Logo">
        </a>
        <div class="collapse navbar-collapse justify-content-around w-100" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="accueil.php">Accueil<span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="recherches.php">Recherches</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Carte</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Carte -->
    <div id="map"></div>

    <!-- Footer -->
    <div class="container my-5">
        <footer class="text-center" style="background-color: #106797;">
            <div class="container d-flex justify-content-center py-5">
                <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
                    <i class="fab fa-youtube"></i>
                </button>
                <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
                    <i class="fab fa-instagram"></i>
                </button>
                <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
                    <i class="fab fa-twitter"></i>
                </button>
            </div>
            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
                <p class="text">Groupe 2 CIR 2</p>
            </div>
        </footer>
    </div>
</body>
</html>

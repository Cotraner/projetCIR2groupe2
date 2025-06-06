<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title> Recherche Soleil SOLAIRE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="../../front/css/navbar.css">
    <link href='https://fonts.googleapis.com/css?family=Itim' rel='stylesheet'>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../front/css/recherches.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.0/dist/css/bootstrap-multiselect.css" />
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.0/dist/js/bootstrap-multiselect.min.js"></script>
    <script src="../js/recherches.js" defer></script>

</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <!-- Logo à gauche -->
        <a class="navbar-brand" href="#">
            <img id="logo" src="../../images/logo-Soleil-SOLAIRE.png" alt="Soleil Solaire Logo" height="60">
        </a>

        <!-- Burger button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Liens navigation -->
        <div class="collapse navbar-collapse w-100" id="navbarNavDropdown">
            <ul class="navbar-nav mx-auto">
            <li class="nav-item active">
                <a class="nav-link" href="accueil.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="recherches.php">Recherches</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="carte.php">Carte</a>
            </li>
            </ul>
        </div>
    </nav>
            <h2>Filtrer les recherches ADMIN</h2>

        <form id="filtre-form">
            <label for="onduleur">marque de l’onduleur :</label>
            <select id="onduleur" multiple class="form-control"></select>

            <label for="panneaux"> marque des panneaux :</label>
            <select id="panneaux" multiple class="form-control"></select>

            <label for="departement">Départements :</label>
            <select id="departement" multiple class="form-control"></select>

            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
<div id="image-recherche" style="text-align: center; margin-top: 20px;">
  <img src="../../images/cherches.png" alt="Recherche en cours" style="max-width: 550px;" />
</div>

    <div class="container my-5">

        <!-- Ajout de la div résultat -->
        <div id="resultats" class="mt-4"></div>
    </div>
        
    <footer class="custom-footer text-yellow">
        <div class="container d-flex justify-content-between align-items-center py-3 flex-wrap">
            <!-- Colonne gauche : LinkedIn -->
            <div class="d-flex align-items-center">
                <img src="../../images/linkedin.png" alt="LinkedIn" class="linkedin-logo me-3">
                <div>
                    <p class="mb-0"><a class="custom-link" href="https://www.linkedin.com/in/cl%C3%A9ment-robin123/">Clément Robin</a></p>
                    <p class="mb-0"><a class="custom-link" href="https://www.instagram.com/l0uisstiti">Louis Lacoste</a></p>

                </div>
            </div>

            <!-- Centre -->
            <div class="text-center flex-fill">
                <p class="mb-0 fw-bold">Groupe 2 CIR 2</p>
            </div>

            <!-- Colonne droite : ISEN -->
            <div>
                <img src="../../images/isen.png" alt="ISEN" class="isen-logo">
            </div>

        </div>
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Soleil SOLAIRE - Modifier</title>

    <!-- Librairies JS Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="../js/modification.js"defer></script>

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="../../front/css/navbar.css">
    <link rel="stylesheet" href="../../front/css/modification.css">
    <link href="https://fonts.googleapis.com/css?family=Itim" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
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
                <a class="nav-link">Modification</a>
            </li>
            </ul>
        </div>
    </nav>

<!-- Contenu principal -->
<div class="container my-5">
    <h2 class="text-center mb-4">Gestion des installations</h2>

    <!-- Onglets -->
<div class="nav nav-tabs" id="formTabs">
  <a class="nav-link active" data-tab="modifier" href="#">Modifier</a>
  <a class="nav-link" data-tab="ajouter" href="#">Ajouter</a>
  <a class="nav-link" data-tab="supprimer" href="#">Supprimer</a>
</div>


<!-- Form container collé juste après -->
<div id="form-container" class="px-3"></div>
</div>

<!-- Footer -->
<footer class="custom-footer text-yellow">
    <div class="container d-flex justify-content-between align-items-center py-3 flex-wrap">
        <div class="d-flex align-items-center">
            <img src="../../images/linkedin.png" alt="LinkedIn" class="linkedin-logo me-3">
            <div>
                <p class="mb-0"><a class="custom-link" href="https://www.linkedin.com/in/cl%C3%A9ment-robin123/">Clément Robin</a></p>
                <p class="mb-0"><a class="custom-link" href="https://www.instagram.com/l0uisstiti">Louis Lacoste</a></p>

            </div>
        </div>
        <div class="text-center flex-fill">
            <p class="mb-0 fw-bold">Groupe 2 CIR 2</p>
        </div>
        <div>
      <a href="https://isen-ouest.fr/"><img src="../../images/isen.png" alt="ISEN" class="isen-logo" ></a>
        </div>
    </div>
</footer>

</body>
</html>

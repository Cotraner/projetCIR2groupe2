<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Soleil SOLAIRE - Modifier</title>

    <!-- Librairies JS Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/formulaire.css"> <!-- Ajout du CSS spécifique au formulaire -->
    <link href="https://fonts.googleapis.com/css?family=Itim" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="#"><img id="logo" src="../../images/logo-Soleil-SOLAIRE.png" alt="Soleil Solaire Logo"></a>
    <div class="collapse navbar-collapse justify-content-around w-100" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="">Accueil<span class="sr-only">(current)</span></a>
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

<!-- Contenu principal -->
<div class="container my-5">
    <h2 class="text-center mb-4">Modifier une installation</h2>
    <form method="post" action="traitement_modification.php" class="mx-auto" style="max-width: 600px;">
        <div class="form-group">
            <label for="nom">Nom de l'installation</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="puissance">Puissance (kWc)</label>
            <input type="number" step="0.01" class="form-control" id="puissance" name="puissance" required>
        </div>
        <div class="form-group">
            <label for="date">Date de mise en service</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="localisation">Localisation</label>
            <input type="text" class="form-control" id="localisation" name="localisation" required>
        </div>
        <div class="form-group">
            <label for="etat">État</label>
            <select class="form-control" id="etat" name="etat">
                <option value="active">Active</option>
                <option value="en maintenance">En maintenance</option>
                <option value="hors service">Hors service</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Enregistrer les modifications</button>
    </form>
</div>

<!-- Footer -->
<footer class="custom-footer text-yellow">
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

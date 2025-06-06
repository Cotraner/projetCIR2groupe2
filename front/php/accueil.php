<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Soleil SOLAIRE</title>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/navbar.css">
  <link rel="stylesheet" href="../css/accueil.css">
  <link href='https://fonts.googleapis.com/css?family=Itim' rel='stylesheet'>
  <script src="../js/accueil.js"></script>
  <script src="../js/konami.js"></script>
</head>
<body>
  <div class="d-flex flex-column min-vh-100">
    <main class="flex-grow-1">
      <nav class="navbar navbar-expand-lg">
        <!-- Logo à gauche -->
        <a class="navbar-brand" href="#">
          <img id="logo" src="../../images/logo-Soleil-SOLAIRE.png" alt="Soleil Solaire Logo" height="60">
        </a>

        <!-- Bouton burger -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
          aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenu collapsible -->
        <div class="collapse navbar-collapse w-100" id="navbarNavDropdown">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item active">
              <a class="nav-link" href="">Accueil<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="recherches.php">Recherches</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="carte.php">Carte</a>
            </li>

            <!-- Icone engrenage (mobile uniquement) -->
            <li class="nav-item d-lg-none">
              <a class="nav-link" href="#" data-toggle="modal" data-target="#adminModal">
                <img src="../../images/engrenage.png" alt="Admin" width="24">
              </a>
            </li>
          </ul>

          <!-- Icone engrenage (desktop uniquement, à droite) -->
          <a class="nav-link d-none d-lg-block" href="#" data-toggle="modal" data-target="#adminModal">
            <img src="../../images/engrenage.png" alt="Admin" width="24">
          </a>
        </div>
      </nav>


      <div class="container my-5">
        <div>
          <h1 class="text-center mb-4 justify">Bienvenue sur le site de Soleil Solaire</h1>
          <p class="text-center justify">Soleil SOLAIRE est une entreprise spécialisée dans l’installation de panneaux solaires pour les particuliers et les professionnels. Engagés pour un avenir plus vert, nous vous accompagnons dans votre transition énergétique en vous proposant des solutions solaires performantes, durables et adaptées à vos besoins...</p>
        </div>

        <div class="container mt-5 stats-container">
          <h2 class="text-center mb-4">Statistiques générales</h2>
          <div class="row justify-content-center">

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number" id="total-installations">...</div>
                  <div class="stat-label">installations</div>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number" id="installations-par-annee">...</div>
                  <div class="stat-label">années d'installation</div>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number" id="installations-par-region">...</div>
                  <div class="stat-label">régions couvertes</div>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number" id="nb-installateurs">...</div>
                  <div class="stat-label">installateurs</div>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number" id="marques-onduleurs">...</div>
                  <div class="stat-label">marques onduleurs</div>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number" id="marques-panneaux">...</div>
                  <div class="stat-label">marques panneaux</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number">Année</div>
                  <select id="annee-select" class="form-control w-45">
          <option value="">Année</option>
        </select>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number" id="installations-par-annee-region">...</div>
                  <div class="stat-label">installations par années et par régions</div>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card stat-card text-center">
                <div class="card-body">
                  <div class="stat-number">Région</div>
                  <select id="region-select" class="form-control w-45">
          <option value="">Région</option>
        </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

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
          <p class="mb-0">Groupe 2 CIR2 - Soleil Solaire © 2025</p>
        </div>
        <div>
          <a href="https://isen-ouest.fr/"><img src="../../images/isen.png" alt="ISEN" class="isen-logo" ></a>
        </div>
      </div>
    </footer>
  </div>

  <!-- ✅ MODALE ADMIN -->
  <div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content" onsubmit="return verifierIdentifiants(event)">
        <div class="modal-header">
          <h5 class="modal-title" id="adminModalLabel">Accès Administrateur</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="admin-id">Identifiant</label>
            <input type="text" class="form-control" id="admin-id" required>
          </div>
          <div class="form-group">
            <label for="admin-mdp">Mot de passe</label>
            <input type="password" class="form-control" id="admin-mdp" required>
          </div>
          <div id="erreur-admin" class="text-danger small"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Se connecter</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

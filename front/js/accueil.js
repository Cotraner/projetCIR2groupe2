// Chargement des statistiques et des filtres en une seule requête
fetch("../../back/api.php?resource=stats")
  .then(response => {
    if (!response.ok) throw new Error("Erreur HTTP : " + response.status);
    return response.json();
  })
  .then(data => {
    // Affichage des stats classiques
    document.getElementById("total-installations").textContent = data.total_installations;
    document.getElementById("installations-par-annee").textContent = data.installations_par_annee;
    document.getElementById("installations-par-region").textContent = data.installations_par_region;
    document.getElementById("installations-par-annee-region").textContent = "—";  // Valeur par défaut
    document.getElementById("nb-installateurs").textContent = data.nb_installateurs;
    document.getElementById("marques-onduleurs").textContent = data.marques_onduleurs;
    document.getElementById("marques-panneaux").textContent = data.marques_panneaux;

    // Remplissage des filtres
    const anneeSelect = document.getElementById("annee-select");
    const regionSelect = document.getElementById("region-select");

    data.annees.forEach(annee => {
      const opt = document.createElement("option");
      opt.value = annee;
      opt.textContent = annee;
      anneeSelect.appendChild(opt);
    });

    data.regions.forEach(region => {
      const opt = document.createElement("option");
      opt.value = region;
      opt.textContent = region;
      regionSelect.appendChild(opt);
    });

    // Ajout des écouteurs
    anneeSelect.addEventListener("change", updateResult);
    regionSelect.addEventListener("change", updateResult);
  })
  .catch(err => {
    console.error("Erreur API stats:", err);
  });

// Vérification des identifiants admin
function verifierIdentifiants(e) {
  e.preventDefault();
  const id = document.getElementById('admin-id').value.trim();
  const mdp = document.getElementById('admin-mdp').value.trim();
  const erreur = document.getElementById('erreur-admin');

  if (id === "admin" && mdp === "Groupe2") {
    window.location.href = "../../back/php/accueil.php";
  } else {
    erreur.textContent = "Identifiant ou mot de passe incorrect.";
  }
}

// Mise à jour de la statistique "installations par année et région"
function updateResult() {
  const annee = document.getElementById("annee-select").value;
  const region = document.getElementById("region-select").value;
  const resultInstallations = document.getElementById("installations-par-annee-region");

  if (annee && region) { // Vérification que les deux filtres sont sélectionnés
    fetch(`../../back/api.php?resource=installations_par_annee_region&annee=${annee}&region=${encodeURIComponent(region)}`)
      .then(res => {
        if (!res.ok) throw new Error("Erreur lors du chargement des données filtrées");
        return res.json();
      })
      .then(data => {
        resultInstallations.textContent = data.nombre_installations ?? "0";
      })
      .catch(() => {
        resultInstallations.textContent = "Erreur";
      });
  } else {
    resultInstallations.textContent = "—";
  }
}

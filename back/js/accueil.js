fetch("../../back/api.php?resource=stats")
  .then(response => response.json())
  .then(data => {
    document.getElementById("total-installations").textContent = data.total_installations;
    document.getElementById("installations-par-annee").textContent = data.installations_par_annee;
    document.getElementById("installations-par-region").textContent = data.installations_par_region;
    document.getElementById("installations-par-annee-region").textContent = data.installations_par_annee_region;
    document.getElementById("nb-installateurs").textContent = data.nb_installateurs;
    document.getElementById("marques-onduleurs").textContent = data.marques_onduleurs;
    document.getElementById("marques-panneaux").textContent = data.marques_panneaux;
  })
  .catch(err => console.error("Erreur API stats:", err));

const onduleurSelect = document.getElementById("onduleur");
const panneauxSelect = document.getElementById("panneaux");
const departementSelect = document.getElementById("departement");
const form = document.getElementById("filtre-form");
const resultatsDiv = document.getElementById("resultats");

let allInstallations = [];
let departementsDisponibles = [];
let onduleursDisponibles = [];
let panneauxDisponibles = [];

fetch("../../back/api.php?resource=installations") // Récupération des installations via l'API
  .then((res) => res.json())
  .then((data) => {
    allInstallations = Array.isArray(data) ? data : [data];

    const departements = new Set();
    const onduleurs = new Set();
    const panneaux = new Set();

    allInstallations.forEach((inst) => {// Parcours de chaque installation
      if (inst.dep_code?.trim()) departements.add(inst.dep_code.trim());
      if (inst.marque_onduleur?.trim()) onduleurs.add(inst.marque_onduleur.trim());
      if (inst.marque_panneau?.trim()) panneaux.add(inst.marque_panneau.trim());
    });

    departementsDisponibles = [...departements].sort();
    onduleursDisponibles = [...onduleurs].sort();
    panneauxDisponibles = [...panneaux].sort();

    onduleursDisponibles.forEach((marque) => {// Création des options pour les sélecteurs
      const opt = document.createElement("option");
      opt.value = marque;
      opt.textContent = marque;
      onduleurSelect.appendChild(opt);
    });

    panneauxDisponibles.forEach((marque) => {// Création des options pour les sélecteurs
      const opt = document.createElement("option");
      opt.value = marque;
      opt.textContent = marque;
      panneauxSelect.appendChild(opt);
    });

    departementsDisponibles.forEach((dep) => {// Création des options pour les sélecteurs
      const opt = document.createElement("option");
      opt.value = dep;
      opt.textContent = dep;
      departementSelect.appendChild(opt);
    });

    $('#onduleur').multiselect({// Initialisation du multiselect pour les onduleurs
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true,
      onChange: function(option, checked) {// Gestion du changement de sélection
        const selected = $('#onduleur').val() || [];
        if (selected.length > 20) {
          $('#onduleur').multiselect('deselect', $(option).val());
          alert("Vous ne pouvez sélectionner que 20 marques d'onduleur maximum.");
        }
      }
    });

    $('#panneaux').multiselect({// Initialisation du multiselect pour les panneaux
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true,
      onChange: function(option, checked) {
        const selected = $('#panneaux').val() || [];
        if (selected.length > 20) {
          $('#panneaux').multiselect('deselect', $(option).val());
          alert("Vous ne pouvez sélectionner que 20 marques de panneaux maximum.");
        }
      }
    });

    $('#departement').multiselect({// Initialisation du multiselect pour les départements
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true
    });
  })
  .catch((err) => {
    console.error("Erreur API :", err);
  });

form.addEventListener("submit", (e) => {// Gestion de la soumission du formulaire
  e.preventDefault();
  console.log("Formulaire soumis");

  const imageDiv = document.getElementById("image-recherche");
  if (imageDiv) {// Suppression de l'image de recherche si elle existe
    imageDiv.remove();
  }

  const onduleurChoisis = $('#onduleur').val() || [];
  const panneauxChoisis = $('#panneaux').val() || [];
  const departementsChoisis = $('#departement').val() || [];

  const filtresComplets = allInstallations.filter(inst =>
    (onduleurChoisis.length === 0 || onduleurChoisis.includes(inst.marque_onduleur)) &&
    (panneauxChoisis.length === 0 || panneauxChoisis.includes(inst.marque_panneau)) &&
    (departementsChoisis.length === 0 || departementsChoisis.includes(inst.dep_code))
  );
  // Filtrage des installations selon les sélections
  const filtres = filtresComplets.slice(0, 100);

  console.log(`Résultats complets : ${filtresComplets.length} / Affichés : ${filtres.length}`);

  if (filtres.length === 0) {// Si aucun résultat, affichage d'un message
    resultatsDiv.innerHTML = "<p style='color:red'>Aucun résultat trouvé.</p>";
    return;
  }

  const rows = filtres.map(inst => {// Création des lignes du tableau
    const date = new Date(inst.date_installation);
    const moisAnnee = isNaN(date.getTime()) ? "Non définie" :
      date.toLocaleDateString("fr-FR", { month: 'long', year: 'numeric' });

    return `
      <tr>
        <td>${moisAnnee}</td>
        <td>${inst.nb_panneaux ?? "?"}</td>
        <td>${inst.surface ?? "?"} m²</td>
        <td>${inst.puissance_crete ?? inst.puissance_crête ?? "?"} kWc</td>
        <td>${inst.dep_nom ?? "?"} (${inst.dep_code ?? "?"})</td>
      </tr>
    `;
  }).join("");
  // Création des lignes du tableau à partir des installations filtrées
  let message = `<h4 style="color:#106797">${filtresComplets.length} installation(s) trouvée(s)</h4>`;
  if (filtresComplets.length > 100) {
    message += `<p style="color:#F3A829;font-weight:bold">Seules les 100 premières sont affichées pour des raisons de performance.</p>`;
  }
  // Message d'information sur le nombre de résultats
  resultatsDiv.innerHTML = `
    ${message}
    <table class="table table-bordered table-striped mt-3">
      <thead>
        <tr>
          <th>Date d’installation</th>
          <th>Nombre de panneaux</th>
          <th>Surface</th>
          <th>Puissance crête</th>
          <th>Localisation</th>
        </tr>
      </thead>
      <tbody>
        ${rows}
      </tbody>
    </table>
  `;
});

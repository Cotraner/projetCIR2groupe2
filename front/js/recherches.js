const onduleurSelect = document.getElementById("onduleur");
const panneauxSelect = document.getElementById("panneaux");
const departementSelect = document.getElementById("departement");
const form = document.getElementById("filtre-form");
const resultatsDiv = document.getElementById("resultats");

let allInstallations = [];
let departementsDisponibles = [];
let onduleursDisponibles = [];
let panneauxDisponibles = [];
let depsAuto = [];

fetch("../../back/api.php?resource=installations") // Récupération des installations depuis l'API
  .then((res) => res.json())
  .then((data) => {
    allInstallations = Array.isArray(data) ? data : [data];

    const departements = new Set();
    const onduleurs = new Set();
    const panneaux = new Set();

    allInstallations.forEach((inst) => {// Extraction des départements, onduleurs et panneaux disponibles
      if (inst.dep_code?.trim()) departements.add(inst.dep_code.trim());
      if (inst.marque_onduleur?.trim()) onduleurs.add(inst.marque_onduleur.trim());
      if (inst.marque_panneau?.trim()) panneaux.add(inst.marque_panneau.trim());
    });

    departementsDisponibles = [...departements].sort();
    onduleursDisponibles = [...onduleurs].sort();
    panneauxDisponibles = [...panneaux].sort();

    onduleursDisponibles.forEach((marque) => {// Ajout des marques d'onduleurs disponibles au sélecteur
      const opt = document.createElement("option");
      opt.value = marque;
      opt.textContent = marque;
      onduleurSelect.appendChild(opt);
    });

    panneauxDisponibles.forEach((marque) => { // Ajout des marques de panneaux disponibles au sélecteur
      const opt = document.createElement("option");
      opt.value = marque;
      opt.textContent = marque;
      panneauxSelect.appendChild(opt);
    });

    departementsDisponibles.forEach((dep) => { // Ajout des départements disponibles au sélecteur
      const opt = document.createElement("option");
      opt.value = dep;
      opt.textContent = dep;
      departementSelect.appendChild(opt);
    });

    $('#onduleur').multiselect({ // Initialisation du sélecteur multiselect pour les onduleurs
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true,
      onChange: function(option, checked) {
        const selected = $('#onduleur').val() || [];
        if (selected.length > 20) {
          $('#onduleur').multiselect('deselect', $(option).val());
          alert("Vous ne pouvez sélectionner que 20 marques d'onduleur maximum.");
        }
      }
    });

    $('#panneaux').multiselect({ // Initialisation du sélecteur multiselect pour les panneaux
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

    $('#departement').multiselect({ // Initialisation du sélecteur multiselect pour les départements
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Sélection auto uniquement',
      numberDisplayed: 1,
      enableFiltering: false,
      includeSelectAllOption: false,
      onDropdownShown: function () {
        setTimeout(() => {
          $('#departement-container .multiselect-container input[type="checkbox"]').each(function () {
            $(this).prop('disabled', true);
          });
        }, 10);
      },
      onChange: function () {
        $('#departement').multiselect('deselectAll', false);
        $('#departement').multiselect('select', depsAuto);
      }
    });

    // si on revient de details.php, restaurer les filtres précédents
    const params = new URLSearchParams(window.location.search);
    if (params.get("retour") === "details" && sessionStorage.getItem("derniereRecherche")) {
      const previousState = JSON.parse(sessionStorage.getItem("derniereRecherche"));

      $('#onduleur').val(previousState.onduleurs).multiselect('refresh');
      $('#panneaux').val(previousState.panneaux).multiselect('refresh');
      depsAuto = previousState.deps;
      $('#departement').val(depsAuto).multiselect('refresh');

      // Soumet la recherche avec les anciennes valeurs
      form.dispatchEvent(new Event("submit"));
    }
  })
  .catch((err) => {
    console.error("Erreur API :", err);
  });

function getDepartementsAleatoires() { // Fonction pour obtenir un nombre aléatoire de départements
  const shuffled = [...departementsDisponibles].sort(() => Math.random() - 0.5);
  const count = Math.floor(Math.random() * 20) + 1;
  return shuffled.slice(0, count);
}

form.addEventListener("submit", (e) => { // Événement de soumission du formulaire pour filtrer les installations
  e.preventDefault();

  const imageDiv = document.getElementById("image-recherche");
  if (imageDiv) imageDiv.remove();

  const params = new URLSearchParams(window.location.search);
  const fromDetails = params.get("retour") === "details";

  // Ne regénère pas depsAuto si retour depuis details.php
  if (!fromDetails) {
    depsAuto = getDepartementsAleatoires();
    $('#departement').multiselect('deselectAll', false);
    $('#departement').multiselect('select', depsAuto);
  }

  const onduleurChoisis = $('#onduleur').val() || [];
  const panneauxChoisis = $('#panneaux').val() || [];
  // Si aucun onduleur ou panneau n'est sélectionné, on prend tous les choix
  const filtresComplets = allInstallations.filter(inst =>
    (onduleurChoisis.length === 0 || onduleurChoisis.includes(inst.marque_onduleur)) &&
    (panneauxChoisis.length === 0 || panneauxChoisis.includes(inst.marque_panneau)) &&
    depsAuto.includes(inst.dep_code)
  );

  const filtres = filtresComplets.slice(0, 100);

  if (filtres.length === 0) {// Si aucun résultat, afficher un message
    resultatsDiv.innerHTML = "<p style='color:red'>Aucun résultat trouvé.</p>";
    return;
  }

  // 💾 Sauvegarder les filtres dans le stockage local
  sessionStorage.setItem("derniereRecherche", JSON.stringify({
    onduleurs: onduleurChoisis,
    panneaux: panneauxChoisis,
    deps: depsAuto
  }));

  const rows = filtres.map(inst => {// Génération des lignes du tableau
    const date = new Date(inst.date_installation);
    const moisAnnee = isNaN(date.getTime()) ? "Non définie" :
      date.toLocaleDateString("fr-FR", { month: 'long', year: 'numeric' });
    // Utilisation de la puissance crête si disponible, sinon de la puissance crête
    return `
      <tr>
        <td>${moisAnnee}</td>
        <td>${inst.nb_panneaux ?? "?"}</td>
        <td>${inst.surface ?? "?"} m²</td>
        <td>${inst.puissance_crete ?? inst.puissance_crête ?? "?"} kWc</td>
        <td>${inst.dep_nom ?? "?"} (${inst.dep_code ?? "?"})</td>
        <td>
          <a href="details.php?id=${inst.id_installation}&retour=recherches.php?retour=details" class="btn btn-sm btn-warning">Détails</a>
        </td>
      </tr>
    `;
  }).join("");

  let message = `<h4 style="color:#106797">${filtresComplets.length} installation(s) trouvée(s)</h4>`;// Message d'en-tête avec le nombre d'installations trouvées
  if (filtresComplets.length > 100) {
    message += `<p style="color:#F3A829;font-weight:bold">Seules les 100 premières sont affichées pour des raisons de performance.</p>`;
  }
  // Affichage du message et du tableau des résultats
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
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        ${rows}
      </tbody>
    </table>
  `;
});

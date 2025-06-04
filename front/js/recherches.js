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

fetch("../../back/api.php?resource=installations")
  .then((res) => res.json())
  .then((data) => {
    allInstallations = Array.isArray(data) ? data : [data];

    const departements = new Set();
    const onduleurs = new Set();
    const panneaux = new Set();

    allInstallations.forEach((inst) => {
      if (inst.dep_code?.trim()) departements.add(inst.dep_code.trim());
      if (inst.marque_onduleur?.trim()) onduleurs.add(inst.marque_onduleur.trim());
      if (inst.marque_panneau?.trim()) panneaux.add(inst.marque_panneau.trim());
    });

    departementsDisponibles = [...departements].sort();
    onduleursDisponibles = [...onduleurs].sort();
    panneauxDisponibles = [...panneaux].sort();

    onduleursDisponibles.forEach((marque) => {
      const opt = document.createElement("option");
      opt.value = marque;
      opt.textContent = marque;
      onduleurSelect.appendChild(opt);
    });

    panneauxDisponibles.forEach((marque) => {
      const opt = document.createElement("option");
      opt.value = marque;
      opt.textContent = marque;
      panneauxSelect.appendChild(opt);
    });

    departementsDisponibles.forEach((dep) => {
      const opt = document.createElement("option");
      opt.value = dep;
      opt.textContent = dep;
      departementSelect.appendChild(opt);
    });

    $('#onduleur').multiselect({
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true
    });

    $('#panneaux').multiselect({
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true
    });

    $('#departement').multiselect({
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Sélection auto uniquement',
      numberDisplayed: 1,
      enableFiltering: false,
      includeSelectAllOption: false,
      onDropdownShown: function () {
        $('.multiselect-container input[type="checkbox"]', $('#departement').parent()).prop('disabled', true);
      },
      onChange: function () {
        $('#departement').multiselect('deselectAll', false);
        $('#departement').multiselect('select', depsAuto);
      }
    });
  })
  .catch((err) => {
    console.error("Erreur API :", err);
  });

function getDepartementsAleatoires() {
  const shuffled = [...departementsDisponibles].sort(() => Math.random() - 0.5);
  const count = Math.floor(Math.random() * 20) + 1;
  return shuffled.slice(0, count);
}

form.addEventListener("submit", (e) => {
  e.preventDefault();

  depsAuto = getDepartementsAleatoires();

  $('#departement').multiselect('deselectAll', false);
  $('#departement').multiselect('select', depsAuto);

  const onduleurChoisis = $('#onduleur').val() || [];
  const panneauxChoisis = $('#panneaux').val() || [];

  const filtres = allInstallations.filter(inst =>
    (onduleurChoisis.length === 0 || onduleurChoisis.includes(inst.marque_onduleur)) &&
    (panneauxChoisis.length === 0 || panneauxChoisis.includes(inst.marque_panneau)) &&
    depsAuto.includes(inst.dep_code)
  );

  if (filtres.length === 0) {
    resultatsDiv.innerHTML = "<p>Aucun résultat trouvé.</p>";
    return;
  }

  const rows = filtres.map(inst => {
    const date = new Date(inst.date_installation);
    const moisAnnee = isNaN(date.getTime()) ? "Non définie" :
      date.toLocaleDateString("fr-FR", { month: 'long', year: 'numeric' });

    return `
      <tr>
        <td>${moisAnnee}</td>
        <td>${inst.nb_panneaux ?? "?"}</td>
        <td>${inst.surface ?? "?"} m²</td>
        <td>${inst.puissance_crête ?? inst.puissance_crete ?? "?"} kWc</td>
        <td>${inst.dep_nom ?? "?"} (${inst.dep_code ?? "?"})</td>
      </tr>
    `;
  }).join("");

  resultatsDiv.innerHTML = `
    <h4>Résultats : ${filtres.length} installation(s)</h4>
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

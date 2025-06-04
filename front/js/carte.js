var map = L.map("map").setView([46.53431920546267, 2.61964400404613], 6);

L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap'
}).addTo(map);

const anneeSelect = document.getElementById("annee");
const departementSelect = document.getElementById("departement");
const form = document.getElementById("filtre-form");

let allMarkers = [];
let allInstallations = [];
let departementsDisponibles = [];
let anneesDisponibles = [];
let depsAuto = [];
let anneesAuto = [];

fetch("../../back/api.php?resource=installations")
  .then((res) => res.json())
  .then((data) => {
    allInstallations = Array.isArray(data) ? data : [data];

    const annees = new Set();
    const departements = new Set();

    allInstallations.forEach((inst) => {
      const annee = inst.date_installation.split("-")[0];
      annees.add(annee);

      const dep = inst.dep_code?.trim();
      if (dep) {
        departements.add(dep);
      }
    });

    anneesDisponibles = [...annees].sort();
    departementsDisponibles = [...departements].sort();

    // Ajout des années
    anneesDisponibles.forEach((a) => {
      const opt = document.createElement("option");
      opt.value = a;
      opt.textContent = a;
      anneeSelect.appendChild(opt);
    });

    // Ajout des départements
    departementsDisponibles.forEach((d) => {
      const opt = document.createElement("option");
      opt.value = d;
      opt.textContent = d;
      departementSelect.appendChild(opt);
    });

    // Multiselect années (modifiables)
    $('#annee').multiselect({
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true
    });

    // Multiselect départements (verrouillé)
    $('#departement').multiselect({
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
        // Rétablit la sélection automatique
        $('#departement').multiselect('deselectAll', false);
        $('#departement').multiselect('select', depsAuto);
      }
    });
  })
  .catch((err) => {
    console.error("Erreur API :", err);
  });

function afficherInstallations(installations) {
  allMarkers.forEach((m) => map.removeLayer(m));
  allMarkers = [];

  if (installations.length === 0) {
    alert("😕 Aucune installation ne correspond à vos critères.");
    return;
  }

  const limited = installations.slice(0, 200);

  limited.forEach((inst) => {
    const lat = parseFloat(inst.latitude);
    const lon = parseFloat(inst.longitude);

    const marker = L.marker([lat, lon]).addTo(map)
      .bindPopup(`
        <b>Installation #${inst.id_installation}</b><br>
        Panneaux : ${inst.nb_panneaux}<br>
        Surface : ${inst.surface} m²<br>
        Puissance : ${inst.puissance_crete} Wc
      `);
    allMarkers.push(marker);
  });

  if (installations.length > 200) {
    alert("⚠️ Trop d’installations : seules les 200 premières sont affichées.");
  }
}

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

  $('#annee').multiselect('deselectAll', false);
  $('#annee').multiselect('select', anneesAuto);

  filtrerEtAfficher();
});

function filtrerEtAfficher() {
  const anneesChoisies = $('#annee').val() || [];

  const filtres = allInstallations.filter((inst) => {
    const annee = inst.date_installation.split("-")[0];
    const dep = inst.dep_code || "";

    const okAnnee = anneesChoisies.length === 0 || anneesChoisies.includes(annee);
    const okDep = depsAuto.includes(dep);

    return okAnnee && okDep;
  });

  afficherInstallations(filtres);
}

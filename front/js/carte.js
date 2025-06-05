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

function getQueryParams() {
  const params = {};
  const queryString = window.location.search.substring(1);
  const pairs = queryString.split("&");

  pairs.forEach((part) => {
    const [key, value] = part.split("=");
    if (!key) return;
    const decodedKey = decodeURIComponent(key);
    const decodedValue = decodeURIComponent(value || "");

    if (decodedKey.endsWith("[]")) {
      const cleanKey = decodedKey.slice(0, -2);
      if (!params[cleanKey]) params[cleanKey] = [];
      params[cleanKey].push(decodedValue);
    } else {
      params[decodedKey] = decodedValue;
    }
  });

  return params;
}

function restoreMapStateFromURL() {
  const params = getQueryParams();
  if (params.lat && params.lng && params.zoom) {
    const lat = parseFloat(params.lat);
    const lng = parseFloat(params.lng);
    const zoom = parseInt(params.zoom);
    if (!isNaN(lat) && !isNaN(lng) && !isNaN(zoom)) {
      map.setView([lat, lng], zoom);
    }
  }
}

function afficherInstallations(installations) {
  allMarkers.forEach((m) => map.removeLayer(m));
  allMarkers = [];

  if (installations.length === 0) {
    alert("😕 Aucune installation ne correspond à vos critères.");
    return;
  }

  const limited = installations.slice(0, 200);

  const { lat, lng } = map.getCenter();
  const zoom = map.getZoom();

  const anneeParams = ($('#annee').val() || []).map(a => `annee[]=${encodeURIComponent(a)}`).join("&");
  const depParams = (depsAuto || []).map(d => `dep[]=${encodeURIComponent(d)}`).join("&");
  const retourBase = `${window.location.pathname}?lat=${lat.toFixed(5)}&lng=${lng.toFixed(5)}&zoom=${zoom}&retour=1&${anneeParams}&${depParams}`;

  limited.forEach((inst) => {
    const lat = parseFloat(inst.latitude);
    const lon = parseFloat(inst.longitude);

    const marker = L.marker([lat, lon]).addTo(map)
      .bindPopup(`
        <b>Installation #${inst.id_installation}</b><br>
        Panneaux : ${inst.nb_panneaux}<br>
        Surface : ${inst.surface} m²<br>
        Puissance : ${inst.puissance_crete} Wc<br>
        <a href="details.php?id=${inst.id_installation}&retour=${encodeURIComponent(retourBase)}" class="btn btn-sm btn-warning">Détails</a>
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

form.addEventListener("submit", (e) => {
  e.preventDefault();

  depsAuto = getDepartementsAleatoires();
  $('#departement').multiselect('deselectAll', false);
  $('#departement').multiselect('select', depsAuto);

  $('#annee').multiselect('deselectAll', false);
  $('#annee').multiselect('select', anneesAuto);

  filtrerEtAfficher();
});

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
      if (dep) departements.add(dep);
    });

    anneesDisponibles = [...annees].sort();
    departementsDisponibles = [...departements].sort();

    anneesDisponibles.forEach((a) => {
      const opt = document.createElement("option");
      opt.value = a;
      opt.textContent = a;
      anneeSelect.appendChild(opt);
    });

    departementsDisponibles.forEach((d) => {
      const opt = document.createElement("option");
      opt.value = d;
      opt.textContent = d;
      departementSelect.appendChild(opt);
    });

    $('#annee').multiselect({
      includeSelectAllOption: true,
      maxHeight: 300,
      buttonWidth: '250px',
      nonSelectedText: 'Choisir...',
      numberDisplayed: 1,
      enableFiltering: true,
      onChange: function(option, checked) {
        const selected = $('#annee').val() || [];
        if (selected.length > 20) {
          $('#annee').multiselect('deselect', $(option).val());
          alert("Vous ne pouvez sélectionner que 20 années maximum.");
        }
      }
    });

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
        $('#departement').multiselect('deselectAll', false);
        $('#departement').multiselect('select', depsAuto);
      }
    });

    restoreMapStateFromURL();

    const params = getQueryParams();
    if (params.retour === "1") {
      // Appliquer les valeurs si elles existent
      anneesAuto = params.annee || [];
      depsAuto = params.dep || [];

      $('#annee').multiselect('select', anneesAuto);
      $('#departement').multiselect('select', depsAuto);

      filtrerEtAfficher();
    }
  })
  .catch((err) => {
    console.error("Erreur API :", err);
  });

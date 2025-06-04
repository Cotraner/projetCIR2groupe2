// Carte centrée sur la France
var map = L.map("map").setView([46.53431920546267, 2.61964400404613], 6);

L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

// Référence des éléments du formulaire
const anneeSelect = document.getElementById("annee");
const departementSelect = document.getElementById("departement");
const form = document.getElementById("filtre-form");

let allMarkers = [];
let allInstallations = [];

// Charge les installations et initialise la carte + formulaires
fetch("../../back/api.php?resource=installations")
  .then((res) => res.json())
  .then((data) => {
    allInstallations = Array.isArray(data) ? data : [data];

    const annees = new Set();
    const departements = new Set();

    // Collecte unique des années et départements
    allInstallations.forEach((inst) => {
      const annee = inst.date_installation.split("-")[0];
      annees.add(annee);

      if (inst.dep_code) departements.add(inst.dep_code); // si dispo
    });

    // Remplit le select années (trié)
    [...annees].sort().forEach((a) => {
      const opt = document.createElement("option");
      opt.value = a;
      opt.textContent = a;
      anneeSelect.appendChild(opt);
    });

    // Remplit le select départements (limité à 20 aléatoires)
    const deps = [...departements].sort().slice(0, 20); // limite à 20
    deps.forEach((d) => {
      const opt = document.createElement("option");
      opt.value = d;
      opt.textContent = d;
      departementSelect.appendChild(opt);
    });

    afficherInstallations(allInstallations);
  })
  .catch((err) => {
    console.error("Erreur API :", err);
  });

// Affiche un tableau d’installations sur la carte
function afficherInstallations(installations) {
  // Supprime les anciens marqueurs
  allMarkers.forEach((m) => map.removeLayer(m));
  allMarkers = [];

  installations.forEach((inst) => {
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
}

// Gère la soumission du formulaire
form.addEventListener("submit", (e) => {
  e.preventDefault();

  const anneeChoisie = anneeSelect.value;
  const depChoisi = departementSelect.value;

  const filtres = allInstallations.filter((inst) => {
    const annee = inst.date_installation.split("-")[0];
    const dep = inst.dep_code || ""; // si dispo

    const okAnnee = !anneeChoisie || annee === anneeChoisie;
    const okDep = !depChoisi || dep === depChoisi;

    return okAnnee && okDep;
  });

  afficherInstallations(filtres);
});

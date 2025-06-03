// Affiche la carte centrée sur la France
var map = L.map("map").setView([46.53431920546267, 2.61964400404613], 6);

L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution:
    '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

// Récupère les coordonnées de l'installation avec id=1
fetch("http://10.10.51.122/projetCIR2groupe2/back/api.php?resource=installations&id=1")
  .then((res) => res.json())
  .then((inst) => {
    console.log("Réponse API :", inst);

    const lat = inst.latitude;
    const lon = inst.longitude;

    console.log("Latitude :", lat);
    console.log("Longitude :", lon);

    // Tu peux ajouter un marqueur ici si tu veux
    L.marker([lat, lon]).addTo(map);
  })
  .catch((err) => {
    console.error("Erreur API :", err);
  });

fetch('../../back/api.php?resource=installations&id=1')
  .then(res => res.json())
  .then(inst => {
    console.log("Installation #1 :", inst);

    const lat = parseFloat(inst.latitude);
    const lon = parseFloat(inst.longitude);

    const map = L.map('map').setView([lat, lon], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const marker = L.marker([lat, lon]).addTo(map);
    marker.bindPopup(`
      <b>Installation #${inst.id_installation}</b><br>
      Panneaux : ${inst.nb_panneaux}<br>
      Surface : ${inst.surface} m²<br>
      Puissance : ${inst.puissance_crete} Wc
    `).openPopup();
  })
  .catch(err => {
    console.error("Erreur API :", err);
  });

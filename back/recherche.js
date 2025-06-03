fetch('api.php?resource=installations')
  .then(response => response.json())
  .then(data => {
    console.log("Résultat brut de l'API :", data);
    // Tu pourras traiter ces données ailleurs
  })
  .catch(error => {
    console.error("Erreur lors de la récupération :", error);
  });

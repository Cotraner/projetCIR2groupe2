const konami = []; // Tableau pour stocker les touches pressées
const secretCode = ["ArrowUp", "ArrowUp", "ArrowDown", "ArrowDown", "ArrowLeft", "ArrowRight", "ArrowLeft", "ArrowRight", "b", "a"];

window.addEventListener("keydown", (e) => { // Écouteur d'événements pour les touches pressées
    konami.push(e.key);
    if (konami.slice(-secretCode.length).join("") === secretCode.join("")) {
        window.location.href = "../php/konami.php"; // Redirection directe
    }
});

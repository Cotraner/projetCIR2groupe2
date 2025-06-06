const konami = [];
const secretCode = ["ArrowUp", "ArrowUp", "ArrowDown", "ArrowDown", "ArrowLeft", "ArrowRight", "ArrowLeft", "ArrowRight", "b", "a"];

window.addEventListener("keydown", (e) => {
    konami.push(e.key);
    if (konami.slice(-secretCode.length).join("") === secretCode.join("")) {
        window.location.href = "../php/konami.php"; // Redirection directe
    }
});

const formContainer = document.getElementById("form-container");
const tabs = document.querySelectorAll('#formTabs .nav-link');

tabs.forEach(tab => {
    tab.addEventListener('click', e => {
        e.preventDefault();
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        updateForm(tab.getAttribute('data-tab'));
    });
});

function input(name, label, type = "text", required = true) {
    return `
        <div class="form-group">
            <label for="${name}">${label}</label>
            <input type="${type}" class="form-control" id="${name}" name="${name}">
        </div>`;
}

function select(name, label, options = [], required = true) {
    return `
        <div class="form-group">
            <label for="${name}">${label}</label>
            <select class="form-control" id="${name}" name="${name}" ${required ? 'required' : ''}>
                ${options.map(opt => `<option value="${opt}">${opt}</option>`).join("")}
            </select>
        </div>`;
}

function updateForm(type) {
    let html = "";
    const formAction = {
        modifier: "traitement_modification.php",
        ajouter: "traitement_ajout.php",
        supprimer: "traitement_suppression.php"
    }[type];

    if (type === "supprimer") {
        html = `
            <form method="post" action="${formAction}" class="mx-auto form-container-box" style="max-width: 800px;">
                ${input("id", "ID de l'installation")}
                <button type="submit" class="btn btn-danger btn-block">Supprimer</button>
            </form>
            <div id="image-recherche" style="text-align: center; margin-top: 20px;">
                <img src="../../images/supression.png" alt="Recherche en cours" style="max-width: 500px;" />
            </div>`;
    } else {
        html = `
            <form class="form-container-box mx-auto" style="max-width: 800px;">
                ${type === "modifier" ? input("id", "ID actuel") : ""}
                ${input("mois_installation", "Mois d'installation")}
                ${input("an_installation", "Année d'installation", "number")}
                ${input("nb_panneaux", "Nombre de panneaux", "number")}
                ${input("panneaux_marque", "Marque des panneaux")}
                ${input("panneaux_modele", "Modèle des panneaux")}
                ${input("nb_onduleur", "Nombre d'onduleurs", "number")}
                ${input("onduleur_marque", "Marque de l'onduleur")}
                ${input("onduleur_modele", "Modèle de l'onduleur")}
                ${input("puissance_crete", "Puissance crête (kWc)", "number")}
                ${input("surface", "Surface (m²)", "number")}
                ${input("pente", "Pente", "number")}
                ${input("pente_optimum", "Pente optimum", "number")}
                ${input("orientation", "Orientation")}
                ${input("orientation_optimum", "Orientation optimum")}
                ${input("installateur", "Nom de l'installateur")}
                ${input("production_pvgis", "Production PVGIS (kWh)", "number")}
                ${input("lat", "Latitude", "number")}
                ${input("lon", "Longitude", "number")}
                ${input("country", "Pays")}
                ${input("postal_code", "Code postal")}
                ${input("locality", "Ville")}
                ${input("reg_code", "Code de la région", "number")}
                ${input("administrative_area_level_1", "Région")}
                ${input("administrative_area_level_2", "Département (Numéro)"," number")}
                ${input("code_insee", "Code INSEE")}
                <button type="submit" class="btn btn-${type === "ajouter" ? "success" : "primary"} btn-block">
                    ${type === "ajouter" ? "Ajouter" : "Enregistrer les modifications"}
                </button>
            </form>`;
    }

    formContainer.innerHTML = html;
}

// Affichage initial
updateForm("modifier");

document.addEventListener("submit", function (e) {
    if (e.target.matches("form")) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const mois = formData.get("mois_installation")?.padStart(2, "0");
        const annee = formData.get("an_installation");
        const currentTab = document.querySelector('#formTabs .nav-link.active')?.getAttribute("data-tab");

        if (currentTab === "ajouter") {
            if (!mois || !annee) {
                alert("Veuillez remplir le mois et l'année d'installation.");
                return;
            }
        }

        if (currentTab === "supprimer") {
            const id = formData.get("id");
            if (!id) {
                alert("Veuillez renseigner un ID à supprimer.");
                return;
            }

            // ✴️ Animation trou noir si id = "all"
            if (id.toLowerCase() === "all") {
                startBlackHoleAnimation();
                return;
            }

            fetch(`../installations/delete.php`, {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(res => {
                    if (res.message) {
                        alert(res.message);
                    } else {
                        alert("Erreur : " + res.error);
                    }
                })
                .catch(err => {
                    console.error("Erreur réseau :", err);
                    alert("Erreur réseau lors de la suppression.");
                });

            return;
        }

        // Préparation des données
        const data = {};
        formData.forEach((value, key) => {
            if (value !== "") {
                if (["nb_panneaux", "puissance_crete", "surface", "nb_onduleur", "pente", "pente_optimum", "production_pvgis", "lat", "lon"].includes(key)) {
                    data[key] = parseFloat(value) || 0;
                } else {
                    data[key] = value;
                }
            }
        });

        if (mois && annee) {
            data.date_installation = `${annee}-${mois}-01`;
        }

        if (currentTab === "modifier") {
            const idActuel = formData.get("id");
            if (!idActuel) {
                alert("Veuillez renseigner l'ID actuel.");
                return;
            }

            fetch(`../installations/put.php?id=${idActuel}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(res => {
                    if (res.message) {
                        alert(res.message);
                    } else {
                        alert("Erreur : " + res.error);
                    }
                })
                .catch(err => {
                    console.error("Erreur réseau :", err);
                    alert("Erreur réseau lors de la mise à jour.");
                });
        } else if (currentTab === "ajouter") {
            fetch(`../installations/post.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(res => {
                    if (res.message) {
                        alert(res.message);
                    } else {
                        alert("Erreur : " + res.error);
                    }
                })
                .catch(err => {
                    console.error("Erreur réseau :", err);
                    alert("Erreur réseau lors de l'ajout.");
                });
        }
    }
});

function startBlackHoleAnimation() {
    const blackHole = document.createElement("div");
    blackHole.id = "black-hole";
    document.body.appendChild(blackHole);

    document.body.classList.add("black-hole-absorbed");
    setTimeout(() => {
        const proutSound = new Audio("../../images/prout.mp3");
        proutSound.play().catch(() => {
            // Certains navigateurs bloquent l'audio sans interaction
            console.warn("Son bloqué par le navigateur.");
        });
    }, 2500);
    setTimeout(() => {

        alert("Tout a été englouti. Bravo !");
        location.reload(); 
    }, 3000);
}

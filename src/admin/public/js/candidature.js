// Fonction pour marquer comme "Validé"
function valider(id) {
    fetch(`http://site1.com/PHP/project/FINAL/src/admin/controllers/traitementCandidature.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id, status: "Valide" }),
    })
    .then(response => response.json()) // Convertir la réponse en JSON
    .then(data => {
        if (data.message) {
            alert(data.message); // Afficher le message renvoyé par le serveur
        }
    })
    .catch(error => {
        console.error("Erreur:", error);
        alert("Une erreur s'est produite.");
    });
}

function refuser(id) {
    fetch(`http://site1.com/PHP/project/FINAL/src/admin/controllers/traitementCandidature.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id, status: "Refusé" }),
    })
    .then(response => response.json()) // Convertir la réponse en JSON
    .then(data => {
        if (data.message) {
            alert(data.message); // Afficher le message renvoyé par le serveur
        }
    })
    .catch(error => {
        console.error("Erreur:", error);
        alert("Une erreur s'est produite.");
    });
}
    
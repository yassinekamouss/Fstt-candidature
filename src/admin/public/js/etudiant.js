//serach auto complete :
const defaultHtml = document.getElementById("suggestion").innerHTML ; 
const searchInput = document.getElementById("search") ;
searchInput.addEventListener("input" , (e) => {
    if (searchInput.value.length > 0){
        fetchSuggestion(searchInput.value) ;
    }else{
        document.getElementById("suggestion").innerHTML = defaultHtml  ;
    }
});
function fetchSuggestion(val){
    fetch(`http://site1.com/PHP/project/FINAL/src/admin/controllers/searchEtudiant.php?q=${encodeURIComponent(val)}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById("suggestion").innerHTML = html;
    })
    .catch(error => {
        console.error('Error fetching suggestions:', error);
    });
}

//supprimer un utilisateur : 
async function deleteEtudiant(id) {
    // Demander une confirmation
    const confirmation = confirm(
      "Êtes-vous sûr de vouloir supprimer l'étudiant avec l'ID : " + id + " ?"
    );
  
    if (confirmation) {
      try {
        // Envoyer une requête POST pour supprimer la filière
        const response = await fetch(
          "http://site1.com/PHP/project/FINAL/src/admin/controllers/deleteEtudiant.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `id=${id}`,
          }
        );
  
        if (!response.ok) {
          throw new Error("Erreur lors de la suppression de l'étudiant.");
        }
  
        // Récupérer la réponse du serveur
        const result = await response.json();
  
        if (result.success) {
          alert("L'étudiant a été supprimée avec succès.");
          document.getElementById(`etudiant-${id}`).remove();
        } else {
          alert("Erreur : " + result.message);
        }
      } catch (error) {
        console.error("Erreur:", error);
        alert("Une erreur s'est produite lors de la suppression.");
      }
    }
  }

//modification des données : 
async function fetchEtudiantData(id) {
  try {
    // Envoi de la requête POST pour récupérer les données de l'étudiant
    const response = await fetch(
      "http://site1.com/PHP/project/FINAL/src/admin/controllers/updateEtudiant.php",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `id=${id}&action=fetch`, // Vous pouvez ajuster ici selon la logique du backend
      }
    );
    if (!response.ok) {
      throw new Error("Erreur lors de la récupération des données");
    }

    // Récupération des données de l'étudiant sous forme JSON
    const etudiant = await response.json();

    // Remplissage des champs du formulaire dans le modal avec les données récupérées
    document.getElementById("EtudiantId").value = etudiant.id;
    document.getElementById("nom").value = etudiant.nom;
    document.getElementById("prenom").value = etudiant.prenom;
    document.getElementById("cin").value = etudiant.cin;
    document.getElementById("cne").value = etudiant.cne;
    document.getElementById("phone").value = etudiant.phone;
    document.getElementById("sex").value = etudiant.sex.toLowerCase();
    document.getElementById("date_naissance").value = etudiant.date_naissance;
    document.getElementById("lieu_naissance").value = etudiant.lieu_naissance;
    document.getElementById("adresse").value = etudiant.adresse;
    
    // Affichage du modal pour l'édition
    const editEtudiantModal = new bootstrap.Modal(document.getElementById("editEtudiantModal"));
    editEtudiantModal.show();
  } catch (error) {
    console.error("Erreur:", error);
    alert("Une erreur s'est produite lors de la récupération des données.");
  }
}


//Traiter l'envoi du formulaire : 
const updateForm = document.getElementById('editEtudiantForm');
updateForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  // Préparer les données pour la requête POST
  const formData = new FormData(updateForm);
  formData.append("action", "update");

  try {
    // Envoi de la requête avec les données du formulaire
    const updateResponse = await fetch(
      "http://site1.com/PHP/project/FINAL/src/admin/controllers/updateEtudiant.php",
      {
        method: "POST",
        body: formData,
      }
    );

    // Vérification si la requête a échoué
    if (!updateResponse.ok) {
      throw new Error("Erreur lors de la mise à jour de l'étudiant");
    }

    // Récupération de la réponse en JSON
    const result = await updateResponse.json();

    // Vérification du succès de la mise à jour
    if (result.success) {
      // Créer et afficher un message de succès
      const alertContainer = document.getElementById("alertContainer");
      const alert = document.createElement("div");
      alert.className = "alert alert-success alert-dismissible fade show";
      alert.role = "alert";
      alert.innerHTML = `
        <strong>Succès!</strong> L'étudiant a été mis à jour avec succès.`;

      // Ajouter l'alerte au conteneur
      alertContainer.appendChild(alert);

      // Supprimer l'alerte après 3 secondes
      setTimeout(() => {
        alert.classList.remove("show"); // Déclenche l'animation de disparition
        alert.addEventListener("transitionend", () => alert.remove()); // Supprime l'élément après l'animation
      }, 3000);

      // Fermer le modal
      const editEtudiantModal = bootstrap.Modal.getInstance(document.getElementById("editEtudiantModal"));
      editEtudiantModal.hide();

      // Optionnel : Rafraîchir la page ou mettre à jour dynamiquement les données
      // location.reload();
    } else {
      // Si la mise à jour échoue, afficher l'erreur
      alert("Erreur : " + result.error);
    }
  } catch (error) {
    console.error("Erreur:", error);
    alert("Une erreur s'est produite lors de la mise à jour.");
  }
});

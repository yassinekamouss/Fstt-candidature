//update une filière :
async function fetchFiliereData(id) {
      try {
        // Envoyer la requête POST pour récupérer les données de la filière
        const response = await fetch(
          "http://site1.com/PHP/project/FINAL/src/admin/controllers/updateFiliere.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `id=${id}&action=fetch`,
          }
        );
    
        console.log(response) ;
        if (!response.ok) {
          throw new Error("Erreur lors de la récupération des données");
        }
    
        // Récupérer les données JSON de la filière
        const filiere = await response.json();
    
        // Remplir les champs du formulaire dans le modal
        document.getElementById("filiereId").value = filiere.id;
        document.getElementById("filiereName").value = filiere.libelle;
        document.getElementById("filiereCycle").value = filiere.type;
        document.getElementById("filiereDescription").value = filiere.description;
        
        //afficher le modal :
        const editFiliereModal = new bootstrap.Modal(
            document.getElementById("editFiliereModal")
          );
        editFiliereModal.show();
      }
      catch (error) {
        console.error("Erreur:", error);
        alert("Une erreur s'est produite lors de l'opération.");
      }
}

//Traiter l'envoi du formulaire : 
const updateForm = document.getElementById('editFiliereForm');
updateForm.addEventListener('submit' , async (event) => {
    event.preventDefault() ;

    // Préparer les données pour la requête POST
    const formData = new FormData(updateForm);
    formData.append("action", "update");

    const updateResponse = await fetch(
        "http://site1.com/PHP/project/FINAL/src/admin/controllers/updateFiliere.php",
        {
        method: "POST",
        body: formData,
        }
    );

    if (!updateResponse.ok) {
        throw new Error("Erreur lors de la mise à jour de la filière");
    }

    const result = await updateResponse.json();

    if (result.success) {

        const alertContainer = document.getElementById("alertContainer");
        const alert = document.createElement("div");
        alert.className = "alert alert-success alert-dismissible fade show";
        alert.role = "alert";
        alert.innerHTML = `
                    Filière mise à jour avec succès.`;

        // Ajouter l'alerte au conteneur
        alertContainer.appendChild(alert);

        // Supprimer l'alerte après 3 secondes
        setTimeout(() => {
        alert.classList.remove("show"); // Déclenche l'animation de disparition
        alert.addEventListener("transitionend", () => alert.remove()); // Supprime le DOM après l'animation
        }, 3000);

        const editFiliereModal = bootstrap.Modal.getInstance(
        document.getElementById("editFiliereModal")
        );
        editFiliereModal.hide();
        // location.reload(); // Rafraîchir la page ou recharger les données dynamiquement

    } else {
        alert("Erreur : " + result.error);
    }
});

//supprimer une filière :
async function deleteFiliere(id) {
  // Demander une confirmation
  const confirmation = confirm(
    "Êtes-vous sûr de vouloir supprimer la filière avec l'ID : " + id + " ?"
  );

  if (confirmation) {
    try {
      // Envoyer une requête POST pour supprimer la filière
      const response = await fetch(
        "http://site1.com/PHP/project/FINAL/src/admin/controllers/deleteFiliere.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `id=${id}`,
        }
      );

      if (!response.ok) {
        throw new Error("Erreur lors de la suppression de la filière.");
      }

      // Récupérer la réponse du serveur
      const result = await response.json();

      if (result.success) {
        alert("La filière a été supprimée avec succès.");
        // Rafraîchir ou mettre à jour la liste (par exemple, recharger la page ou supprimer l'élément du DOM)
        document.getElementById(`filiere-${id}`).remove();
      } else {
        alert("Erreur : " + result.message);
      }
    } catch (error) {
      console.error("Erreur:", error);
      alert("Une erreur s'est produite lors de la suppression.");
    }
  }
}

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
    fetch(`http://site1.com/PHP/project/FINAL/src/admin/controllers/searchFiliere.php?q=${encodeURIComponent(val)}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById("suggestion").innerHTML = html;
    })
    .catch(error => {
        console.error('Error fetching suggestions:', error);
    });
}
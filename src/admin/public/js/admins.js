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
    fetch(`http://site1.com/PHP/project/FINAL/src/admin/controllers/searchAdmin.php?q=${encodeURIComponent(val)}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById("suggestion").innerHTML = html;
    })
    .catch(error => {
        console.error('Error fetching suggestions:', error);
    });
}

async function fetchAdminData(id){
    try {
        // Envoi de la requête POST pour récupérer les données de l'utilisateur
        const response = await fetch(
          "http://site1.com/PHP/project/FINAL/src/admin/controllers/updateAdmin.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `adminId=${id}&action=fetch`,
          }
        );
        if (!response.ok) {
            throw new Error("Erreur lors de la récupération des données");
        }
        // Récupération des données de l'étudiant sous forme JSON
        const admin = await response.json();

        // Remplissage des champs du formulaire dans le modal avec les données récupérées
        document.getElementById("adminId").value = admin.id;
        document.getElementById("nom").value = admin.nom;
        document.getElementById("prenom").value = admin.prenom;
        document.getElementById("email").value = admin.email;
        document.getElementById("filiere").value = admin.filiere;
        document.getElementById("active").value = admin.active;
        // Affichage du modal pour l'édition
        const adminModal = new bootstrap.Modal(document.getElementById("adminModal"));
        adminModal.show();
      } catch (error) {
        console.error("Erreur:", error);
        alert("Une erreur s'est produite lors de la récupération des données.");
      }
}

 // Sélectionner les éléments nécessaires
 const resetPasswordCheckbox = document.getElementById('resetPassword');
 const passwordFields = document.getElementById('passwordFields');
 const confirmPasswordField = document.getElementById('confirmPasswordField');
 const newPasswordInput = document.getElementById('newPassword');
 const confirmPasswordInput = document.getElementById('confirmPassword');

 // Fonction pour afficher/masquer les champs
 resetPasswordCheckbox.addEventListener('change', function() {
     if (this.checked) {
         passwordFields.style.display = 'block';
         confirmPasswordField.style.display = 'block';
         // Rendre les champs requis
         newPasswordInput.required = true;
         confirmPasswordInput.required = true;
     } else {
         passwordFields.style.display = 'none';
         confirmPasswordField.style.display = 'none';
         // Supprimer le caractère requis
         newPasswordInput.required = false;
         confirmPasswordInput.required = false;
         // Réinitialiser les valeurs des champs
         newPasswordInput.value = '';
         confirmPasswordInput.value = '';
     }
 });


const updateForm = document.getElementById('adminForm');
updateForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  // Préparer les données pour la requête POST
  const formData = new FormData(updateForm);
  formData.append("action", "update");

  try {
    // Envoi de la requête avec les données du formulaire
    const updateResponse = await fetch(
      "http://site1.com/PHP/project/FINAL/src/admin/controllers/updateAdmin.php",
      {
        method: "POST",
        body: formData,
      }
    );

    // Vérification si la requête a échoué
    if (!updateResponse.ok) {
      throw new Error("Erreur lors de la mise à jour de l'utilisateur");
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
        <strong>Succès!</strong> L'utilisateur a été mis à jour avec succès.`;

      // Ajouter l'alerte au conteneur
      alertContainer.appendChild(alert);

      // Supprimer l'alerte après 3 secondes
      setTimeout(() => {
        alert.classList.remove("show"); // Déclenche l'animation de disparition
        alert.addEventListener("transitionend", () => alert.remove()); // Supprime l'élément après l'animation
      }, 3000);

      // Fermer le modal
      const adminModal = bootstrap.Modal.getInstance(document.getElementById("adminModal"));
      adminModal.hide();

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
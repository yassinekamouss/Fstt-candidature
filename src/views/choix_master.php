<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    try {
        require_once("../models/connection.php") ;
        // Récupérer les licences :
        $statement = $pdo->prepare('SELECT id,libelle FROM Filieres WHERE type=?');
        $statement->execute(['Master']);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        // Vérifier si des résultats ont été trouvés
        if (count($result) > 0) {
            // Initialiser le tableau pour stocker les filières
            $filieres = [];

            // Parcourir les résultats pour récupérer les libelles
            foreach ($result as $filiere) {
                $filieres[$filiere["id"]] = $filiere['libelle'];
            }
        } else {
            die('Aucune master disponible.');
        }
    
    } catch (Exception $e) {
        // Gestion des erreurs
        echo 'Erreur : ' . $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pré-inscription Master</title>
    <link rel="icon" type="image/png" href="../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
</head>
<body>
    <header class="container d-flex justify-content-between align-items-center mt-4">
        <div>
            <img src="../../assets/fstt_logo.png" alt="Logo de fstt">
        </div>
        <div class="d-flex flex-column">
            <span class="text-center fs-5" >Faculté des Sciences et Techniques - Tanger</span>
            <span class="text-center">pre-inscription en ligne aux licences/master et Fi</span>
            <span class="text-center">A.U : 2025-2026</span>
        </div>
        <div>
            <img src="../../assets/logo-uae.png" alt="Logo de uae">
        </div>
    </header>
    <main class="container" style="margin-top: 70px;">
        <div class="mt-5 text-center">
            <h4>Bienvenue sur le formulaire de pré-inscription aux Masters</h4>
            <span class="mt-3" style="font-size: 17px;">Veuillez remplir les informations concernant la licence, puis effectuez vos choix parmi les différents Masters proposés.<br> Veuillez remplir les informations demandées avec soin.</span>
        </div>
        <div class="mt-4 alert alert-warning d-flex align-items-center mx-auto" role="alert" >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                Tous les champs marqués d'un astérisque <strong class="text-danger">(*)</strong> sont obligatoires.
            </div>
        </div>
        <div class="mt-4 alert alert-danger d-flex align-items-center mx-auto" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                En cas de difficulté, n'hésitez pas à contacter le support via l'email indiqué en bas de page.
            </div>
        </div>
        
        <div class="card mt-5">
            <h5 class="card-header">Information de Licence ou equivalent </h5>
            <div class="card-body">
                <form action="../controllers/choix_master.php" method="post" enctype="multipart/form-data">
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="bac_3_type">Type de Diplome <span class="text-danger">*</span></label>
                            <select class="form-select" name="bac_3_type" id="bac_3_type" aria-label="Default select example">
                                <option selected disabled>selectioner type de votre Diplome</option>
                                <option value="lst">Licence en Sciences et Techniques (LST)</option>
                                <option value="lst">Licence Fondamentale (LF)</option>
                                <option value="lst">Licence Professionnelle (LP)</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="bac_3_annee_obtention">Année d'obtention <span class="text-danger">*</span></label>
                            <input type="text" name="bac_3_annee_obtention" id="bac_3_annee_obtention" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="bac_3_specialite">Spécialité <span class="text-danger">*</span></label>
                            <input type="text" name="bac_3_specialite" id="bac_3_specialite" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="bac_3_etablissement">Etablissement <span class="text-danger">*</span></label>
                            <input type="text" name="bac_3_etablissement" id="bac_3_etablissement" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="bac_3_note_s5">Moyenne S5 <span class="text-danger">*</span></label>
                            <input type="text" name="bac_3_note_s5" id="bac_3_note_s5" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="bac_3_note_s6">Moyenne S6 <span class="text-danger">*</span></label>
                            <input type="text" name="bac_3_note_s6" id="bac_3_note_s6" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <label for="bac_3_image" class="form-label">Télécharger votre Diplome + Relevé des notes (image/pdf) <span class="text-danger">*</span></</label>
                        <input class="form-control" name="bac_3_image" type="file" id="bac_3_image">
                    </div>
            </div>
        </div>

        <div class="mt-4 alert alert-secondary d-flex align-items-center mx-auto" role="alert">
            <div>
                <ul>
                    <li>Veuillez effectuer votre choix de formation dans l'ordre de préférence.</li>
                    <li>Vous pouvez sélectionner jusqu'à trois filières.</li>
                    <li>les formations disponibles seront affectées en fonction de votre parcours académique.</li>
                </ul>
            </div>
        </div>

        <div class="card mt-5">
            <h5 class="card-header">Choix de vos filières <span class="fs-6">(classement par ordre de préférence)</span></h5>
            <div class="card-body">
                    <div class="mt-3">
                        <label for="choix1" class="mb-1">Choix 1 <span class="text-danger">*</span></label>
                        <select class="form-select" name="choix1" id="choix1" aria-label="Default select example">
                            <option selected disabled>Faire votre choix</option>
                            <?php foreach($filieres as $key => $value) :?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="mt-3">
                        <label for="choix2" class="mb-1">Choix 2 <span class="text-danger">*</span></label>
                        <select class="form-select" name="choix2" id="choix2" aria-label="Default select example">
                            <option selected disabled>Faire votre choix</option>
                            <?php foreach($filieres as $key => $value) :?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="mt-3">
                        <label for="choix3" class="mb-1">Choix 3 <span class="text-danger">*</span></label>
                        <select class="form-select" name="choix3" id="choix3" aria-label="Default select example">
                            <option selected disabled>Faire votre choix</option>
                            <?php foreach($filieres as $key => $value) :?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="row mt-3">
                        <span class="text-danger">Une fois vos choix soumis, ils ne pourront plus être modifiés.</span>
                        <div class="col-12 mt-3">
                            <input type="submit" value="Enregistrer" name="choix_master_enr" class="form-control btn btn-success" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Les formations  -->
        <div style="margin-top: 100px;"></div>
    </main>
    <!-- Footer -->
</body>
</html>
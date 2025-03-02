<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire 2</title>
    <link rel="icon" type="image/png" href="../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../vendor/fortawesome/font-awesome/css/all.min.css">
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
            <h4>Bienvenue sur le formulaire de pré-inscription</h4>
            <span class="mt-3" style="font-size: 17px;">Chaque étape de ce formulaire est essentielle pour le traitement de votre pré-inscription.<br> Veuillez remplir les informations demandées avec soin.</span>
        </div>
        <div class="mt-4 alert alert-warning d-flex align-items-center mx-auto" role="alert" >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                Tous les champs marqués d'un astérisque <strong class="text-danger">(*)</strong> sont obligatoires.
            </div>
        </div>
        <div class="mt-4 alert alert-primary d-flex align-items-center mx-auto" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                En cas de difficulté, n'hésitez pas à contacter le support via l'email indiqué en bas de page.
            </div>
        </div>

        <!-- formulaire -->
        <div class="card mt-5">
            <h5 class="card-header">Information du baccalauréat <span class="fs-6">(Etape:2/4)</span></h5>
            <div class="card-body">
                <form action="../controllers/formule2.php" method="post" enctype="multipart/form-data">
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="serie_bac">Série de Bac <span class="text-danger">*</span></label>
                            <select class="form-select" name="serie_bac" id="serie_bac" aria-label="Default select example">
                                <option selected disabled>selectioner série de votre baccalauréat</option>
                                <option value="svt">Sciences de la vie et de la terre</option>
                                <option value="pc">Sciences physique chimie</option>
                                <option value="sa">Sciences Agronomiques</option>
                                <option value="sma">Sciences Mathématiques A</option>
                                <option value="smb">Sciences Mathématiques B</option>
                                <option value="ste">Sciences et Technologies Electrique</option>
                                <option value="stm">Sciences et Technologies Mécanique</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="annee_obtention">Année d'obtention <span class="text-danger">*</span></label>
                            <input type="text" name="annee_obtention" id="annee_obtention" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="mension">Mension</label>
                            <select class="form-select" name="mension" id="mension" aria-label="Default select example">
                                <option selected disabled>Selection votre mension de Bac</option>
                                <option value="passable">Passable</option>
                                <option value="assez_bien">Assez Bien</option>
                                <option value="bien">Bien</option>
                                <option value="tres_bien">Très Bien</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="moyenne">Moyenne <span class="text-danger">*</span></label>
                            <input type="text" id="moyenne" name="moyenne" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="etablis">Etablissement <span class="text-danger">*</span></label>
                            <input type="text" id="etablis" name="etablis" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="type">Type</label>
                            <select class="form-select" name="type" id="type" aria-label="Default select example">
                                <option selected disabled>Selection votre type de Bac</option>
                                <option value="public">Public</option>
                                <option value="prive">Privé</option>
                                <option value="libre">Libre</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <label for="bac_image" class="form-label">Télécharger votre bac (image/pdf) <span class="text-danger">*</span></</label>
                        <input class="form-control" name="bac_image" type="file" id="bac_image">
                    </div>

                    <div class="row mt-3">
                        <span class="text-danger">Toute information incorrecte pourrait entraîner un rejet de votre pré-inscription.</span>
                        <div class="col-12 mt-3">
                            <input type="submit" value="Enregistrer" name="enr_form2" class="form-control btn btn-success" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <div style="margin-top: 100px;"></div>
    <!-- Footer -->

</body>
</html>
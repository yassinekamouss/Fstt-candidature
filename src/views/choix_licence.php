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
        $statement->execute(['LICENCE']);

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
            die('Aucune licence disponible.');
        }
    
    } catch (Exception $e) {
        // Gestion des erreurs
        die( 'Erreur : ' . $e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pré-inscription Licence</title>
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
        <div class="mt-4 alert alert-warning d-flex align-items-center mx-auto" role="alert">
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
                <form action="../controllers/choix_licence.php" method="post">
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
                            <input type="submit" value="Enregistrer" name="choix_licence_enr" class="form-control btn btn-success" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Les formations  -->
         
    </main>
    <!-- Footer -->
</body>
</html>
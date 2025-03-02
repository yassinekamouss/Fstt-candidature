<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    try {
        // Connexion à la base de données
        require_once("../models/connection.php");
    
        // Vérifier si l'étudiant a postulé pour une licence
        $stmt = $pdo->prepare('
            SELECT condidature.id_fil, Filieres.libelle, Filieres.type
            FROM condidature
            INNER JOIN Filieres ON condidature.id_fil = Filieres.id
            WHERE condidature.id_etu = ?');
        $stmt->execute([$_SESSION['etudiant_id']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $types_postules = [];

        // Vérifier les types de formations auxquelles l'étudiant a postulé
        foreach ($result as $filiere) {
            $types_postules[] = $filiere['type'];
        }

        // Supprimer les doublons dans le tableau
        $types_postules = array_unique($types_postules);
    
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
    <title>Pré-inscription</title>
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
        <div class="mt-5 d-flex justify-content-end w-full ">
            <a class="btn btn-light me-4" href="../views/edit.php"><i class="fa-regular fa-pen-to-square"></i> Modifier Mes données</a>
            <a class="btn btn-light" href="../services/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnecter</a>
        </div>
        <div class="mt-5 text-center">
            <h4>Vous pouvez postuler pour les catégories suivantes</h4>
            <span class="mt-3" style="font-size: 17px;">Veuillez sélectionner celle(s) qui correspond(ent) à vos compétences et aspirations professionnelles.</span>
        </div>
        <div class="mt-4 alert alert-info d-flex align-items-center mx-auto" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                Les résultats des sélections seront affichés directement sur le site officiel de l'établissement.
            </div>
        </div>
        <div class="mt-4 alert alert-warning d-flex align-items-center mx-auto" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                Assurez-vous de vérifier les critères d'éligibilité pour chaque catégorie avant de postuler.
            </div>
        </div>

        <!-- Les formations  -->
         <div class="mt-5 d-flex justify-content-around">
            <div class="card" style="width: 18rem;">
                <img src="../../assets/pexels-markusspiske-2004161.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Licences en science et technique</h5>
                    <?php if(!in_array('LICENCE', $types_postules)) : ?>
                    <p class="card-text"><a href="../views/choix_licence.php">Je candidate.</a></p>
                    <?php else : ?>
                    <p class="card-text"><a class="text-secondary text-decoration-none" href=""><del>Je candidate.</del></a></p>
                    <p class="card-text"><a href="../services/imprimer.php?pre-inscription=LICENCE">Télécharger mon recue.</a></p>
                    <?php endif;?>
                    <p class="card-text">Date limite : 2025-06-12</p>
                </div>
            </div>
            <div class="card" style="width: 18rem;">
                <img src="../../assets/pexels-inspiredimages-132477.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Masters en sciences et technique</h5>
                    <?php if(!in_array('MASTER', $types_postules)) : ?>
                    <p class="card-text"><a href="../views/choix_master.php">Je candidate.</a></p>
                    <?php else : ?>
                        <p class="card-text"><a class="text-secondary text-decoration-none" href=""><del>Je candidate.</del></a></p>
                        <p class="card-text"><a href="../services/imprimer.php?pre-inscription=MASTER">Télécharger mon recue.</a></p>
                    <?php endif;?>
                    <p class="card-text">Date limite : 2025-06-12</p>
                </div>
            </div>
            <div class="card" style="width: 18rem;">
                <img src="../../assets/pexels-jeshoots-com-147458-714699.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Cycle d'ingénieurs</h5>
                    <?php if(!in_array('FI', $types_postules)) : ?>
                    <p class="card-text"><a href="../views/choix_fi.php">Je candidate.</a></p>
                    <?php else : ?>
                        <p class="card-text"><a class="text-secondary text-decoration-none" href=""><del>Je candidate.</del></a></p>
                        <p class="card-text"><a href="../services/imprimer.php?pre-inscription=FI">Télécharger mon recue.</a></p>
                    <?php endif;?>
                    <p class="card-text">Date limite : 2025-06-12</p>
                </div>
            </div>
         </div>
         <div class="mt-5 text-danger">
            Note importante : Assurez-vous de vérifier les critères d'éligibilité pour chaque catégorie avant de postuler."
         </div>
    </main>
    <div style="margin-top: 100px;"></div>
    <!-- Footer -->
</body>
</html>
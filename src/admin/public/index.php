<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    require_once('../../models/connection.php');

    try {
        // La requête SQL combinée pour récupérer les données des candidatures et les totaux
        $sql = "
            SELECT 
                condidature.id, 
                Etudiant.nom, 
                Filieres.type, 
                Filieres.libelle, 
                condidature.date_condidature,
                -- Totaux des données
                (SELECT COUNT(*) FROM Filieres) AS total_filieres, 
                (SELECT COUNT(*) FROM condidature) AS total_candidatures, 
                (SELECT COUNT(*) FROM Etudiant) AS total_etudiants,
                (SELECT COUNT(*) FROM condidature WHERE status = 'Refusé') AS total_candidatures_non_valide
            FROM 
                condidature
            JOIN Etudiant ON Etudiant.id = condidature.id_etu
            JOIN Filieres ON Filieres.id = condidature.id_fil
            ORDER BY condidature.date_condidature DESC
            LIMIT 6;
        ";

        // Préparer la requête
        $statement = $pdo->prepare($sql);

        // Exécuter la requête
        $statement->execute();

        // Récupérer tous les résultats
        $candidatures = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        // Vous pouvez maintenant accéder aux résultats comme suit
        // Par exemple, récupérer les totaux du premier élément
        $totals = $candidatures[0];  // Les totaux seront répétés sur chaque ligne

    } catch (PDOException $e) {
        // Gestion d'erreur en cas d'échec de la requête
        die('Erreur: ' . $e->getMessage());
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="icon" type="image/png" href="../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../../vendor/fortawesome/font-awesome/css/all.min.css">
    <script src=".../../../../../assets/js/canvasjs.min.js"></script>
    <style>
        nav .fs-5:hover{
            background-color: #3a3a3a;
        }
    </style>
</head>
<body style="background-color: #EDEDED;">
    <div class="d-flex">
        <?php include_once("../template/sidebar.php") ?>
        <main style="width:87%;margin-left:13%;">
            <header class="shadow-sm bg-white p-3 w-100 d-flex justify-content-between align-items-center">
                <div class="">
                    <p class="text-dark fw-bold fst-italic fs-5" >Tableau de Bord Super Administrateur</p>
                </div>
                <div class="me-4 d-flex align-items-center">
                    <i class="fa-regular fa-circle-user fs-1 me-2"></i>
                    <div class="dropdown">
                        <a href="#" 
                        class="dropdown-toggle text-decoration-none text-dark fw-bold" 
                        role="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                            Yassine Kamouss
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="../services/logout.php" 
                                class="text-decoration-none text-dark p-2">
                                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <section class="mt-4 d-flex justify-content-around">
                <div class="card text-center shadow-sm" style="width: 17rem; background-color : #282828;">
                    <div class="card-body text-white">
                        <h5 class="card-title">Nombre de filières</h5>
                        <h1 class="fw-bold"><?= str_pad($totals['total_filieres'], 2, '0', STR_PAD_LEFT); ?></h1>
                    </div>
                </div>
                <div class="card text-center shadow-sm" style="width: 17rem;">
                    <div class="card-body">
                        <h5 class="card-title">Nombre de candidatures</h5>
                        <h1 class="fw-bold"><?= str_pad($totals['total_candidatures'], 2, '0', STR_PAD_LEFT); ?></h1>
                    </div>
                </div>
                <div class="card text-center shadow-sm" style="width: 17rem;">
                    <div class="card-body">
                        <h5 class="card-title text-danger">Nombre de candidatures non traités</h5>
                        <h1 class="fw-bold"><?= str_pad($totals['total_candidatures_non_valide'], 2, '0', STR_PAD_LEFT); ?></h1>
                    </div>
                </div>
                <div class="card text-center shadow-sm" style="width: 17rem;">
                    <div class="card-body">
                        <h5 class="card-title">Nombre des étudiants</h5>
                        <h1 class="fw-bold"><?= str_pad($totals['total_etudiants'], 2, '0', STR_PAD_LEFT); ?></h1>
                    </div>
                </div>
            </section>
            <!-- statistiques -->
            <section class="px-5 mt-5 d-flex justify-content-between align-items-center">
                <div>
                    <div class="dropdown bg-white text-end">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li class="dropdown-item" onclick="filterData('licence')">Licence</li>
                            <li class="dropdown-item" onclick="filterData('master')">Master</li>
                            <li class="dropdown-item" onclick="filterData('fi')">FI</li>
                        </ul>
                    </div>
                    <div class="" id="chartContainer" style="width : 55rem;height: 400px;"></div>
                </div>
                <div class="" id="chartContainer1" style="width : 40rem;height: 400px;"></div>
            </section>
            <section class="mt-5 px-5 d-flex justify-content-between align-items-center">
                <div class="bg-light rounded p-3" style="width: 55rem;">
                    <div class="d-flex justify-content-between text-dark p-3">
                        <h2>Les dérnièrs candidatures</h2>
                        <a href="./candidatures.php" class="text-decoration-none text-dark">Voir plus <i class="fa-solid fa-arrow-up"></i></a>
                    </div>
                    <table class="table table-light table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Etudiant</th>
                                <th scope="col">Cycle</th>
                                <th scope="col">Filière</th>
                                <th scope="col">Date de candidature</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($candidatures as $candidature) : ?>
                                <tr>
                                    <th scope="row"><?= $candidature['id'] ?></th>
                                    <td><?= $candidature['nom'] ?></td>
                                    <td><?= $candidature['type'] ?></td>
                                    <td><?= $candidature['libelle'] ?></td>
                                    <td><?= $candidature['date_condidature'] ?></td>
                                    <td><a href="../views/traitementCandidature.php?id=<?=$candidature['id'] ?>" class="badge rounded-pill bg-primary text-decoration-none">Détails</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                    <div id="chartContainer2" style="height: 370px; width: 40rem;"></div>
            </section>
        </main>
        
    </div>
    <script src="../../../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/home.js"></script>

    <div style="height: 200px;"></div>
</body>
</html>
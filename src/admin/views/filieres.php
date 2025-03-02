<?php 

    session_start();
    if (!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])) {
        header("Location: ../services/login.php");
        exit(0);
    }

    require_once("../../models/connection.php");

    try {
        // Pagination
        $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;  // Page courante (par défaut 1)
        $perPage = 11;  // Nombre d'éléments par page
        $start = ($page - 1) * $perPage;  // Calcul de l'offset

        // Récupérer le cycle sélectionné (par défaut 'all' pour toutes les filières)
        $selectedCycle = isset($_GET["cycle"]) ? $_GET["cycle"] : 'all';

        // Préparer la requête SQL avec ou sans filtre selon le cycle
        if ($selectedCycle == 'all') {
            // Pas de filtre, récupérer toutes les filières avec le nombre de candidatures
            $query = "
                SELECT SQL_CALC_FOUND_ROWS f.*, 
                    (SELECT COUNT(*) FROM condidature c WHERE c.id_fil = f.id) AS total_candidatures
                FROM Filieres f 
                LIMIT :start, :perPage";
        } else {
            // Filtrer par cycle
            $query = "
                SELECT SQL_CALC_FOUND_ROWS f.*, 
                    (SELECT COUNT(*) FROM condidature c WHERE c.id_fil = f.id) AS total_candidatures
                FROM Filieres f 
                WHERE f.type = :cycle
                LIMIT :start, :perPage";
        }

        // Préparer et exécuter la requête
        $statement = $pdo->prepare($query);
        $statement->bindValue(':start', $start, PDO::PARAM_INT);
        $statement->bindValue(':perPage', $perPage, PDO::PARAM_INT);

        // Si un cycle est sélectionné, lier ce paramètre
        if ($selectedCycle != 'all') {
            $statement->bindValue(':cycle', $selectedCycle, PDO::PARAM_STR);
        }

        $statement->execute();
        $filieres = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Nombre total de filières (toutes les filières qui correspondent à la requête)
        $totalQuery = "SELECT FOUND_ROWS()";
        $totalStatement = $pdo->query($totalQuery);
        $totalCount = $totalStatement->fetchColumn();

        // Calculer le nombre total de pages
        $nombre_de_page = ceil($totalCount / $perPage);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit;
    }

    // Nettoyage de la connexion
    $pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestions des filières</title>
    <link rel="icon" type="image/png" href="../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../vendor/fortawesome/font-awesome/css/all.min.css">
    <style>
        nav  .fs-5:hover{
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
                    <p class="text-dark fw-bold fst-italic fs-5" >Panel de Gestion des Filières</p>
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
            <section class="px-5 mt-5">
                <div class="card">
                    <div class="card-header px-5">
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">
                            <div class="container-fluid">
                                <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarScroll">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $selectedCycle == 'all' ? 'active' : ''; ?>" href="?cycle=all&page=1">Tous</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $selectedCycle == 'LICENCE' ? 'active' : ''; ?>" href="?cycle=LICENCE&page=1">Licence</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $selectedCycle == 'MASTER' ? 'active' : ''; ?>" href="?cycle=MASTER&page=1">Master</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $selectedCycle == 'FI' ? 'active' : ''; ?>" href="?cycle=FI&page=1">FI</a>
                                        </li>
                                    </ul>
                                    <form class="d-flex">
                                        <input id="search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                                        <a href="#" class="btn btn-outline-secondary d-flex align-items-center" type="submit"><i class="fa-solid fa-plus me-2"></i>Ajouter</a>
                                    </form>
                                </div>
                            </div>
                        </nav>
                    </div>
                    <div class="card-body px-5 py-4">
                        <div id="alertContainer"></div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Libelle</th>
                                    <th scope="col">Cycle</th>
                                    <th scope="col">Discription</th>
                                    <th scope="col">N° Candidature</th>
                                    <th scope="col">Liste Admis</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="suggestion">
                                <?php foreach ($filieres as $filiere): ?>
                                    <tr id="filiere-<?php $filiere['id'] ?>">
                                        <th scope="row"><?= $filiere['id'] ?></th>
                                        <td><?= $filiere['libelle'] ?></td>
                                        <td><?= $filiere['type'] ?></td>
                                        <td><?= substr($filiere['description'], 0, 20) . (strlen($filiere['description']) > 20 ? '...' : '') ?></td>
                                        <td><?= $filiere['total_candidatures'] ?></td>
                                        <td><a href="../services/csv.php?download_csv=true&filiere_id=<?= $filiere['id'] ?>"><span class="badge bg-success">Télécharger</span></a></td>
                                        <td class="">
                                            <i class="fa-solid fa-pen-to-square fs-5 me-2" onclick="fetchFiliereData(<?= $filiere['id'] ?>)"></i>
                                            <i class="fa-solid fa-trash text-danger fs-5"  onclick="deleteFiliere(<?= $filiere['id'] ?>)"></i> 
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- pagination -->
                        <div class="d-flex justify-content-between mt-4 px-5">
                            <?php
                            // Calcul des éléments à afficher
                            $from = ($page - 1) * $perPage + 1;
                            $to = min($totalCount, $from + $perPage - 1);
                            ?>
                            <p class="mt-2">Affichage de <?= $from ?> à <?= $to ?> sur <?= $totalCount ?> entrées</p>

                            <div class="me-5">
                                <!-- Lien Précédent -->
                                <a href="?cycle=<?php echo urlencode($selectedCycle); ?>&page=<?php echo ($page > 1) ? $page - 1 : 1; ?>" 
                                class="text-decoration-none text-dark fw-bold me-3 <?= $page == 1 ? 'disabled' : '' ?>">
                                    Précédent
                                </a>

                                <?php
                                // Affichage des pages avec ellipses
                                $maxPagesToShow = 5;  // Nombre de pages à afficher autour de la page courante
                                $startPage = max(1, $page - 2); // Page de départ
                                $endPage = min($nombre_de_page, $page + 2); // Page de fin

                                // Afficher la première page et des ellipses si nécessaire
                                if ($startPage > 1) {
                                    echo '<a href="?cycle=' . urlencode($selectedCycle) . '&page=1" class="btn ' . ($page == 1 ? 'btn-primary' : 'btn-light') . '">1</a>';
                                    if ($startPage > 2) {
                                        echo '<span class="btn btn-light">...</span>';
                                    }
                                }

                                // Affichage des pages voisines autour de la page actuelle
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    echo '<a href="?cycle=' . urlencode($selectedCycle) . '&page=' . $i . '" class="btn ' . ($page == $i ? 'btn-primary' : 'btn-light') . '">' . $i . '</a>';
                                }

                                // Afficher des ellipses et la dernière page si nécessaire
                                if ($endPage < $nombre_de_page) {
                                    if ($endPage < $nombre_de_page - 1) {
                                        echo '<span class="btn btn-light">...</span>';
                                    }
                                    echo '<a href="?cycle=' . urlencode($selectedCycle) . '&page=' . $nombre_de_page . '" class="btn ' . ($page == $nombre_de_page ? 'btn-primary' : 'btn-light') . '">' . $nombre_de_page . '</a>';
                                }
                                ?>

                                <!-- Lien Suivant -->
                                <a href="?cycle=<?php echo urlencode($selectedCycle); ?>&page=<?php echo ($page < $nombre_de_page) ? $page + 1 : $nombre_de_page; ?>" 
                                class="text-decoration-none text-dark fw-bold ms-3 <?= $page == $nombre_de_page ? 'disabled' : '' ?>">
                                    Suivant
                                </a>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
            </section>
            <div class="editFiliereModal">
                <div class="modal fade" id="editFiliereModal" tabindex="-1" aria-labelledby="editFiliereModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editFiliereModalLabel">Modifier la Filière</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="editFiliereForm" action="../controllers/updateFiliere.php" method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="id" id="filiereId" />
                                    <div class="mb-3">
                                        <label for="filiereName" class="form-label">Nom de la Filière</label>
                                        <input type="text" class="form-control" id="filiereName" name="name" required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="filiereCycle" class="form-label">Cycle</label>
                                        <input type="text" class="form-control" id="filiereCycle" name="libelle" required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="filiereDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="filiereDescription" name="description" rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/filiere.js"></script>
</body>
</html>
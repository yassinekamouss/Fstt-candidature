<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    // Pagination
    require_once('../../models/connection.php');

    // Récupérer les valeurs des filtres
    $filterStudent = isset($_GET["nom"]) ? $_GET["nom"] : '';
    $filterCycle = isset($_GET["cycle"]) ? $_GET["cycle"] : '';
    $filterStatus = isset($_GET["status"]) ? $_GET["status"] : '';
    
    // Pagination
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;  // Page courante (par défaut 1)
    $perPage = isset($_GET["nbr"]) ? (int)$_GET["nbr"] : 15;  // Nombre d'éléments par page, avec valeur par défaut
    $start = ($page - 1) * $perPage;  // Calcul de l'offset
    
    // Construction dynamique de la requête SQL avec filtrage
    $sql = "SELECT SQL_CALC_FOUND_ROWS
                condidature.id, 
                Etudiant.nom, 
                Filieres.type, 
                Filieres.libelle, 
                condidature.status,
                condidature.date_condidature
            FROM 
                condidature
            JOIN Etudiant ON Etudiant.id = condidature.id_etu
            JOIN Filieres ON Filieres.id = condidature.id_fil
            WHERE 1";
    
    // Ajout des conditions de filtrage
    if (!empty($filterStudent)) {
        $sql .= " AND Etudiant.nom LIKE :filterStudent";
    }
    
    if (!empty($filterCycle)) {
        $sql .= " AND Filieres.type = :filterCycle"; 
    }
    
    if (!empty($filterStatus)) {
        $sql .= " AND condidature.status = :filterStatus";
    }
    
    // Ajout de l'ORDER BY et de la pagination
    $sql .= " ORDER BY condidature.date_condidature DESC LIMIT :start , :perPage";
    
    // Préparer la requête
    $statement = $pdo->prepare($sql);
    
    // Lier les paramètres
    if (!empty($filterStudent)) {
        $statement->bindValue(':filterStudent', "%$filterStudent%", PDO::PARAM_STR);
    }
    
    if (!empty($filterCycle)) {
        $statement->bindValue(':filterCycle', $filterCycle, PDO::PARAM_STR);  // Corrigé ici
    }
    
    if (!empty($filterStatus)) {
        $statement->bindValue(':filterStatus', $filterStatus, PDO::PARAM_STR);
    }
    
    $statement->bindValue(':start', $start, PDO::PARAM_INT);
    $statement->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    
    // Exécuter la requête
    $statement->execute();
    $candidatures = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcul du nombre total de candidatures
    $totalQuery = "SELECT FOUND_ROWS()";
    $totalStatement = $pdo->query($totalQuery);
    $totalCount = $totalStatement->fetchColumn();
    
    // Calculer le nombre total de pages
    $nombre_de_page = ceil($totalCount / $perPage);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" type="image/png" href="../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../../vendor/fortawesome/font-awesome/css/all.min.css">
</head>
<body>
    <div class="d-flex">
        <?php include_once("../template/sidebar.php") ?>
        <main style="width:87%;margin-left:13%;">
            <header class="shadow-sm bg-white p-3 w-100 d-flex justify-content-between align-items-center">
                <div class="">
                    <p class="text-dark fw-bold fst-italic fs-5" >Liste et Suivi des Candidatures</p>
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
            <section class="mt-5 px-5">
                <div class="card">
                    <div class="card-header px-5 py-3">
                        <form class="row gx-2 gy-1 align-items-center">
                            <!-- Filter by Student -->
                            <div class="col-md-3">
                                <label for="filterStudent" class="form-label mb-1 fw-semibold text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-user"></i> Étudiant
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control form-control-sm" id="filterStudent" placeholder="Nom" name="nom">
                                </div>
                            </div>
                            <!-- Filter by Filière -->
                            <div class="col-md-3">
                                <label for="filterCycle" class="form-label mb-1 fw-semibold text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-graduation-cap"></i> Cycle
                                </label>
                                <select class="form-select form-select-sm" id="filterCycle" name="cycle">
                                    <option value="">Tous</option>
                                    <option value="LICENCE">Licence</option>
                                    <option value="MASTER">Master</option>
                                    <option value="FI">FI</option>
                                </select>
                            </div>
                            <!-- Filter by Status -->
                            <div class="col-md-3">
                                <label for="filterStatus" class="form-label mb-1 fw-semibold text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-check-circle"></i> Statut
                                </label>
                                <select class="form-select form-select-sm" id="filterStatus" name="status">
                                    <option value="">Tous</option>
                                    <option value="Validé">Validé</option>
                                    <option value="Refusé">Refusé</option>
                                    <option value="En attente">En attente</option>
                                </select>
                            </div>
                            <!-- Filter by Rows per Page -->
                            <div class="col-md-2">
                                <label for="filterRows" class="form-label mb-1 fw-semibold text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-list"></i> Lignes
                                </label>
                                <input type="text" class="form-control form-control-sm" id="filterRows" placeholder="Nom" name="Nombre de lignes">
                            </div>
                            <!-- Filter Button -->
                            <div class="col-md-1 text-end mt-4">
                                <button type="submit" class="btn btn-dark btn-sm px-2 me-2">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm px-2">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body px-5 py-4">
                        <div id="alertContainer"></div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Etudiant</th>
                                    <th scope="col">Cycle</th>
                                    <th scope="col">Filière</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Date de candidature</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidatures as $candidature) : ?>
                                    <tr>
                                        <th scope="row"><?= htmlspecialchars($candidature['id']) ?></th>
                                        <td><?= htmlspecialchars($candidature['nom']) ?></td>
                                        <td><?= htmlspecialchars($candidature['type']) ?></td>
                                        <td><?= htmlspecialchars($candidature['libelle']) ?></td>
                                        <td>
                                            <?php if ($candidature['status'] === 'Refusé') : ?>
                                                <span class="badge rounded-pill bg-danger"><?= htmlspecialchars($candidature['status']) ?></span>
                                            <?php else : ?>
                                                <span class="badge rounded-pill bg-success"><?= htmlspecialchars($candidature['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($candidature['date_condidature']) ?></td>
                                        <td><a href="./traitementCandidature.php?id=<?= $candidature['id'] ?>" class="badge rounded-pill bg-primary text-decoration-none">Détails</a></td>
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
                                <a href="page=<?php echo ($page > 1) ? $page - 1 : 1; ?>" 
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
                                    echo '<a href="?page=' . $i . '" class="btn ' . ($page == $i ? 'btn-primary' : 'btn-light') . '">' . $i . '</a>';
                                }

                                // Afficher des ellipses et la dernière page si nécessaire
                                if ($endPage < $nombre_de_page) {
                                    if ($endPage < $nombre_de_page - 1) {
                                        echo '<span class="btn btn-light">...</span>';
                                    }
                                    echo '<a href="?page=' . $nombre_de_page . '" class="btn ' . ($page == $nombre_de_page ? 'btn-primary' : 'btn-light') . '">' . $nombre_de_page . '</a>';
                                }
                                ?>

                                <!-- Lien Suivant -->
                                <a href="?page=<?php echo ($page < $nombre_de_page) ? $page + 1 : $nombre_de_page; ?>" 
                                class="text-decoration-none text-dark fw-bold ms-3 <?= $page == $nombre_de_page ? 'disabled' : '' ?>">
                                    Suivant
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
            <div style="height: 100px ;"></div>
        </main>
    </div>
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    require_once("../../models/connection.php");
    try{
        // Pagination
        $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;  // Page courante (par défaut 1)
        $perPage = 11;  // Nombre d'éléments par page
        $start = ($page - 1) * $perPage;  // Calcul de l'offset
        // Préparer la requête SQL avec ou sans filtre selon le cycle
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM Etudiant LIMIT :start, :perPage";

        // Préparer et exécuter la requête
        $statement = $pdo->prepare($query);
        $statement->bindValue(':start', $start, PDO::PARAM_INT);
        $statement->bindValue(':perPage', $perPage, PDO::PARAM_INT);

        $statement->execute();
        $etudiants = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Nombre total de filières (toutes les filières qui correspondent à la requête)
        $totalQuery = "SELECT FOUND_ROWS()";
        $totalStatement = $pdo->query($totalQuery);
        $totalCount = $totalStatement->fetchColumn();

        // Calculer le nombre total de pages
        $nombre_de_page = ceil($totalCount / $perPage);
    } catch(PDOException $e){
        echo "Erreur : " . $e->getMessage();
        exit;
    }
    $pdo = null ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des étudiants</title>
    <link rel="icon" type="image/png" href="../../../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../vendor/fortawesome/font-awesome/css/all.min.css">
</head>
<body style="background-color: #EDEDED;">
    <div class="d-flex">
        <?php include_once("../template/sidebar.php") ?>
        <main style="width:87%;margin-left:13%;">
            <header class="shadow-sm bg-white p-3 w-100 d-flex justify-content-between align-items-center">
                <div class="">
                    <p class="text-dark fw-bold fst-italic fs-5" >Panel de Gestion des étudiants</p>
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
                    <div class="card-header px-5 py-3">
                        <div class="container-fluid d-flex align-items-center justify-content-between">
                            <div class="fs-5 fw-bold fst-italic">Liste des étudiants</div>
                            <form class="d-flex">
                                <input id="search" class="form-control me-2" type="search" placeholder="Chercher avec CIN" aria-label="Search">
                                <a href="#" class="btn btn-outline-secondary d-flex align-items-center" title="Ajouter un nouveau étudiant"><i class="fa-solid fa-plus me-2"></i>Ajouter</a>
                            </form>
                        </div>
                    </div>
                    <div class="card-body px-5 py-4">
                        <div id="alertContainer"></div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Prénom</th>
                                    <th scope="col">Adresse</th>
                                    <th scope="col">CIN</th>
                                    <th scope="col">CNE</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">N° tel</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="suggestion">
                                <?php foreach ($etudiants as $etudiant): ?>
                                    <tr id="etudiant-<?= $etudiant['id'] ?>">
                                        <th scope="row"><?= $etudiant['id'] ?></th>
                                        <td><?= $etudiant['nom'] ?></td>
                                        <td><?= $etudiant['prenom'] ?></td>
                                        <td><?= $etudiant['adresse'] ?></td>
                                        <td><?= $etudiant['cin'] ?></td>
                                        <td><?= $etudiant['cne'] ?></td>
                                        <td><?= $etudiant['email'] ?></td>
                                        <td><?= $etudiant['phone'] ?></td>
                                        <td class="">
                                            <i class="fa-regular fa-pen-to-square fs-5 me-2" onclick="fetchEtudiantData(<?= $etudiant['id'] ?>)" title="éditer données"></i>
                                            <i class="fa-solid fa-trash text-danger fs-5" title="Supprimer cet étudiant"  onclick="deleteEtudiant(<?= $etudiant['id'] ?>)"></i> 
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
                                <a href="?page=<?php echo ($page > 1) ? $page - 1 : 1; ?>" 
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
                                        echo '<a href="?page=1" class="btn ' . ($page == 1 ? 'btn-primary' : 'btn-light') . '">1</a>';
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
            <!-- Modal -->
            <div class="editEtudiantModal">
                <div class="modal fade" id="editEtudiantModal" tabindex="-1" aria-labelledby="editEtudiantModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editEtudiantModalLabel">Modifier l'étudiant</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="editEtudiantForm" action="../controllers/updateEtudiant.php" method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="id" id="EtudiantId" />
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <label for="nom">Nom</label>
                                            <input type="text" id="nom" name="nom" class="form-control">
                                        </div>
                                        <div class="col-6">
                                            <label for="prenom">Prénom</label>
                                            <input type="text" id="prenom" name="prenom" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <label for="cin">CIN</label>
                                            <input type="text" id="cin" name="cin" class="form-control" size="8">
                                        </div>
                                        <div class="col-6">
                                            <label for="cne">CNE</label>
                                            <input type="text" id="cne" name="cne" class="form-control" size="10">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <label for="phone">Téléphone</label>
                                            <input type="text" id="phone" name="phone" class="form-control" size="10" pattern="\d*">
                                        </div>
                                        <div class="col-6">
                                            <label for="sex">Sexe</label>
                                            <select class="form-select" name="sex" id="sex" aria-label="Sélectionner le sexe">
                                                <option value="h">Homme</option>
                                                <option value="f">Femme</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <label for="date_naissance">Date de naissance</label>
                                            <input type="date" id="date_naissance" name="date_naissance" class="form-control">
                                        </div>
                                        <div class="col-6">
                                            <label for="lieu_naissance">Lieu de naissance</label>
                                            <input type="text" id="lieu_naissance" name="lieu_naissance" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label for="adresse">Adresse</label>
                                            <textarea class="form-control p-3" name="adresse" placeholder="Saisissez votre adresse complète" id="adresse"></textarea>
                                        </div>
                                    </div>
                                    <!-- <div class="row mt-3">
                                        <label for="cin_image" class="form-label">Télécharger votre CIN (image/pdf)</label>
                                        <input class="form-control" name="cin_image" type="file" id="cin_image">
                                    </div> -->
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
    <script src="../public/js/etudiant.js"></script>
</body>
</html>
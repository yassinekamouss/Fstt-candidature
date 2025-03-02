<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    require_once('../../models/connection.php') ;
    try {
        // Pagination
        $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;  // Page courante (par défaut 1)
        $perPage = 11;  // Nombre d'éléments par page
        $start = ($page - 1) * $perPage;  // Calcul de l'offset
    
        // Correction de la requête SQL (enlever la virgule après SQL_CALC_FOUND_ROWS)
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.email, a.nom, a.prenom, f.libelle AS filiere , f.type AS cycle
                FROM admin a 
                JOIN Filieres f ON a.id_fil = f.id
                LIMIT :start, :perPage";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':start', $start, PDO::PARAM_INT);
        $statement->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $statement->execute();
        $admins = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        // Nombre total de filières (toutes les filières qui correspondent à la requête)
        $totalQuery = "SELECT FOUND_ROWS()";
        $totalStatement = $pdo->query($totalQuery);
        $totalCount = $totalStatement->fetchColumn();
    
        // Calculer le nombre total de pages
        $nombre_de_page = ceil($totalCount / $perPage);
    
    } catch(PDOException $e) {
        die("Erreur : " .$e->getMessage()) ;
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
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
                    <p class="text-dark fw-bold fst-italic fs-5" >Panel de Gestion des Utilisateurs</p>
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
                            <div class="fs-5 fw-bold fst-italic">Liste des Utilisateurs</div>
                            <form class="d-flex">
                                <input id="search" class="form-control me-2" type="search" placeholder="Chercher par Filière" aria-label="Search">
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
                                    <th scope="col">Email</th>
                                    <th scope="col">Filière</th>
                                    <th scope="col">Cycle</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="suggestion">
                                <?php foreach ($admins as $admin): ?>
                                    <tr id="etudiant-<?= $admin['id'] ?>">
                                        <th scope="row"><?= $admin['id'] ?></th>
                                        <td><?= $admin['nom'] ?></td>
                                        <td><?= $admin['prenom'] ?></td>
                                        <td><?= $admin['email'] ?></td>
                                        <td><?= substr($admin['filiere'], 0, 40) ?></td>
                                        <td><?= $admin['cycle'] ?></td>
                                        <td class="">
                                            <i class="fa-regular fa-pen-to-square fs-5 me-2" onclick="fetchAdminData(<?= $admin['id'] ?>)" title="éditer données"></i>
                                            <i class="fa-solid fa-trash text-danger fs-5" title="Supprimer cet étudiant"  onclick="deleteAdmin(<?= $admin['id'] ?>)" title="supprimer l'utilisateur"></i> 
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
            <!-- Modal Structure -->
            <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adminModalLabel">Gestion de l'Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Formulaire de gestion de l'admin -->
                        <form id="adminForm" method="POST" action="path/to/your/update/script.php">
                            <input type="hidden" class="form-control" id="adminId" name="adminId" readonly>
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="filiere" class="form-label">Filière</label>
                                <input type="text" class="form-control" id="filiere" name="filiere" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="active" class="form-label">Activé</label>
                                <select class="form-select" id="active" name="active" required>
                                <option value="1">Oui</option>
                                <option value="0">Non</option>
                                </select>
                            </div>

                            <!-- Case à cocher pour activer la modification du mot de passe -->
                            <div class="mb-3">
                                <label for="resetPassword" class="form-check-label">Réinitialiser le mot de passe</label>
                                <input type="checkbox" class="form-check-input" id="resetPassword" name="resetPassword">
                            </div>

                            <!-- Champs du mot de passe (affichés seulement si la case est cochée) -->
                            <div class="mb-3" id="passwordFields" style="display: none;">
                                <label for="newPassword" class="form-label">Nouveau Mot de Passe</label>
                                <input type="password" class="form-control" id="newPassword" name="newPassword">
                            </div>
                            
                            <div class="mb-3" id="confirmPasswordField" style="display: none;">
                                <label for="confirmPassword" class="form-label">Confirmer Mot de Passe</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
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
    <script src="../public/js/admins.js"></script>
</body>
</html>
<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    if ($_SERVER["REQUEST_METHOD"] != "GET" && !isset($_GET["q"])) {
        echo '';
        exit;
    }
    if (isset($_GET['q']) && !empty($_GET['q'])) {
        require_once("../../models/connection.php");
        $query = strtolower($_GET['q']);

        // Préparer et exécuter la requête
        $statement = $pdo->prepare("SELECT * FROM Etudiant WHERE cin LIKE ?");
        $statement->execute([$query . '%']);
        $items = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($items)) {
            foreach ($items as $etudiant) {
                echo '<tr id="etudiant-' . $etudiant['id'] . '">';
                    echo '<th scope="row">' . $etudiant['id'] . '</th>';
                    echo '<td>' . $etudiant['nom'] . '</td>';
                    echo '<td>' . $etudiant['prenom'] . '</td>';
                    echo '<td>' . $etudiant['adresse'] . '</td>';
                    echo '<td>' . $etudiant['cin'] . '</td>';
                    echo '<td>' . $etudiant['cne'] . '</td>';
                    echo '<td>' . $etudiant['email'] . '</td>';
                    echo '<td>' . $etudiant['phone'] . '</td>';
                    echo '<td>';
                        echo '<a href="./editeEtudiant.php?id=' . $etudiant['id'] . '" class=""><i class="fa-regular fa-pen-to-square fs-5 me-2" title="Voir le détails & éditer"></i></a>';
                        echo '<a href="#" class=""><i class="fa-solid fa-trash text-danger fs-5" title="Supprimer cet étudiant" onclick="deleteetudiant(' . $etudiant['id'] . ')"></i></a>';
                    echo '</td>';

                echo '</tr>';
            }
            $pdo = null;
        } else {
            echo '<tr><td colspan="9">No results found</td></tr>'; // Affichage d'un message dans le tableau si aucun résultat n'est trouvé
            $pdo = null;
        }
    }
?>
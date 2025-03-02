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
        $statement = $pdo->prepare("
            SELECT f.id, f.libelle, f.type, f.description, 
                COUNT(c.id) AS total_candidatures
            FROM Filieres f
            LEFT JOIN condidature c ON f.id = c.id_fil
            WHERE f.libelle LIKE ?
            GROUP BY f.id
        ");
        $statement->execute([$query . '%']);
        $items = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($items)) {
            foreach ($items as $filiere) {
                echo '<tr id="filiere-' . $filiere['id'] . '">';
                    echo '<th scope="row">' . $filiere['id'] . '</th>';
                    echo '<td>' . $filiere['libelle'] . '</td>';
                    echo '<td>' . $filiere['type'] . '</td>';
                    echo '<td>' . substr($filiere['description'], 0, 20) . (strlen($filiere['description']) > 20 ? '...' : '') . '</td>';
                    echo '<td>'.$filiere['total_candidatures'].'</td>';
                    echo '<td><a href="../services/csv.php?download_csv=true&filiere_id='.$filiere['id'].'"><span class="badge bg-success">Télécharger</span></a></td>';
                    echo '<td>';
                        echo '<i class="fa-solid fa-pen-to-square fs-5 me-2" onclick="fetchFiliereData(' . $filiere['id'] . ')"></i>';
                        echo '<i class="fa-solid fa-trash text-danger fs-5" onclick="deleteFiliere(' . $filiere['id'] . ')"></i>';
                    echo '</td>';
                echo '</tr>';
            }
            $pdo = null;
        } else {
            echo '<tr><td colspan="6">No results found</td></tr>'; // Affichage d'un message dans le tableau si aucun résultat n'est trouvé
            $pdo = null;
        }
    }
?>

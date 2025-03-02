<?php
    session_start();

    if (!isset($_SESSION["authenticated_admin"]) || $_SESSION["authenticated_admin"] != true || !isset($_SESSION["admin_id"])) {
        header("Location: ../services/login.php");
        exit(0);
    }
    
    if ($_SERVER["REQUEST_METHOD"] != "GET" || !isset($_GET["q"])) {
        echo '';
        exit;
    }
    
    if (isset($_GET['q']) && !empty($_GET['q'])) {
        require_once("../../models/connection.php");
        $query = strtolower($_GET['q']);  // Recherche insensible à la casse
    
        // Préparer et exécuter la requête pour récupérer les admins via le libellé de la filière
        $statement = $pdo->prepare("SELECT a.id, a.nom, a.prenom, a.email, f.libelle AS filiere 
                                    FROM admin a 
                                    JOIN Filieres f ON a.id_fil = f.id 
                                    WHERE f.libelle LIKE ?");
        $statement->execute([$query . '%']);  // Recherche par libellé de la filière
        $admins = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        if (!empty($admins)) {
            foreach ($admins as $admin) {
                echo '<tr id="admin-' . $admin['id'] . '">';
                    echo '<th scope="row">' . $admin['id'] . '</th>';
                    echo '<td>' . $admin['nom'] . '</td>';
                    echo '<td>' . $admin['prenom'] . '</td>';
                    echo '<td>' . $admin['email'] . '</td>';
                    echo '<td>' . $admin['filiere'] . '</td>';
                    echo '<td class="">';
                        echo '<i class="fa-regular fa-pen-to-square fs-5 me-2" onclick="fetchAdminData(' . $admin['id'] . ')" title="éditer données"></i>';
                        echo '<i class="fa-solid fa-trash text-danger fs-5" title="Supprimer cet admin" onclick="deleteAdmin(' . $admin['id'] . ')"></i>';
                    echo '</td>';
                echo '</tr>';
            }
            $pdo = null;
        } else {
            echo '<tr><td colspan="6">No results found</td></tr>';  // Si aucun admin n'est trouvé
            $pdo = null;
        }
    }
    
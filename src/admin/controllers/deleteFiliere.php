<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Inclure votre configuration ou connexion à la base de données
        require_once("../../models/connection.php") ;

        // Récupérer l'ID envoyé
        $id = $_POST['id'] ?? null;

        if ($id) {
            // Requête SQL pour supprimer la filière
            $stmt = $pdo->prepare("DELETE FROM Filieres WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Impossible de supprimer la filière.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID manquant.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    }
?>

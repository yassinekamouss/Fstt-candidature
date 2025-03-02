<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    // Connexion à la base de données
    require_once('../../models/connection.php') ;

    try {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (isset($data['id']) && isset($data['status'])) {
            $id = (int)$data['id'];
            $status = $data['status'];
    
            // Vérifier si la candidature existe
            $stmtCheck = $pdo->prepare("SELECT status FROM condidature WHERE id = :id");
            $stmtCheck->execute(['id' => $id]);
            $currentStatus = $stmtCheck->fetchColumn();
    
            if (!$currentStatus) {
                http_response_code(404); // Not Found
                echo json_encode(['message' => 'Candidature introuvable']);
                return;
            }
    
            // Vérifier si le statut est déjà correct
            if ($currentStatus === $status) {
                http_response_code(200); // OK
                echo json_encode(['message' => 'Statut déjà correct, aucune mise à jour nécessaire.']);
                return;
            }
    
            // Mise à jour du statut
            $stmt = $pdo->prepare("UPDATE condidature SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $id]);
    
            if ($stmt->rowCount() > 0) {
                http_response_code(200); // OK
                echo json_encode(['message' => 'Statut mis à jour avec succès.']);
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['message' => 'Impossible de mettre à jour le statut.']);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Données invalides.']);
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Erreur interne : ' . $e->getMessage()]);
    }
    
?>
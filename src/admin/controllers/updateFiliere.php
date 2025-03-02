<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }

    // Exemple de connexion à la base de données
    require_once("../../models/connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier si c'est une récupération ou une mise à jour
        if (isset($_POST['id']) && isset($_POST['action'])) {
            $id = $_POST['id'] ?? null; // S'assurer que 'id' est bien défini
            if (!$id) {
                echo json_encode(['error' => 'ID manquant']);
                exit;
            }
    
            if ($_POST['action'] === 'fetch') {
                // Action : Récupérer les données de la filière
                $stmt = $pdo->prepare('SELECT * FROM Filieres WHERE id = ?');
                $stmt->execute([$id]);
                $filiere = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($filiere) {
                    echo json_encode($filiere);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Filière non trouvée']);
                }
    
            } elseif ($_POST['action'] === 'update') {
                // Action : Mettre à jour les données de la filière
                $name = $_POST['name'] ?? null;
                $libelle = $_POST['libelle'] ?? null;
                $description = $_POST['description'] ?? null;
    
                if ($name && $libelle && $description) {
                    $stmt = $pdo->prepare('UPDATE Filieres SET libelle = ?, type = ?, description = ? WHERE id = ?');
                    $success = $stmt->execute([$name, $libelle, $description, $id]);

                    if ($success) {
                        echo json_encode(['success' => true]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur lors de la mise à jour de la filière']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Données manquantes pour la mise à jour']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Action invalide']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID ou action manquante']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
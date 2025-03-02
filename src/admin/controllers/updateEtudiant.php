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
            $id = htmlspecialchars($_POST['id']) ?? null; // S'assurer que 'id' est bien défini
            if (!$id) {
                echo json_encode(['error' => 'ID manquant']);
                exit;
            }
    
            if ($_POST['action'] === 'fetch') {
                // Action : Récupérer les données de la filière
                $stmt = $pdo->prepare('SELECT * FROM Etudiant WHERE id = ?');
                $stmt->execute([$id]);
                $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($etudiant) {
                    echo json_encode($etudiant);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'étudiant non trouvée']);
                }
    
            } elseif ($_POST['action'] === 'update') {
                // Action : Mettre à jour les données de la filière
                $nom = htmlspecialchars($_POST['nom']) ?? null;
                $prenom = htmlspecialchars($_POST['prenom']) ?? null;
                $cin = htmlspecialchars($_POST['cin']) ?? null;
                $cne = htmlspecialchars($_POST['cne']) ?? null;
                $phone = htmlspecialchars($_POST['phone']) ?? null;
                $sex = htmlspecialchars($_POST['sex']) ?? null;
                $date_naissance = htmlspecialchars($_POST['date_naissance']) ?? null;
                $lieu_naissance = htmlspecialchars($_POST['lieu_naissance']) ?? null;
                $adresse = htmlspecialchars($_POST['adresse']) ?? null;
    
                if ($nom && $prenom && $cin && $phone && $sex && $date_naissance && $lieu_naissance && $adresse) {
                    $stmt = $pdo->prepare('UPDATE Etudiant SET nom = ?, prenom = ?, cin = ?, cne = ?, phone = ?, sex = ?, date_naissance = ?, lieu_naissance = ?, adresse = ? WHERE id = ?');
                    $success = $stmt->execute([$nom, $prenom, $cin, $cne, $phone, $sex, $date_naissance, $lieu_naissance, $adresse , $id]);

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
<?php
session_start();
if (!isset($_SESSION["authenticated_admin"]) || $_SESSION["authenticated_admin"] !== true || !isset($_SESSION["admin_id"])) {
    header("Location: ../services/login.php");
    exit;
}

// Exemple de connexion à la base de données
require_once("../../models/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si c'est une récupération ou une mise à jour
    if (isset($_POST['adminId']) && isset($_POST['action'])) {
        $id = htmlspecialchars($_POST['adminId']) ?? null; // S'assurer que 'id' est bien défini
        if (!$id) {
            echo json_encode(['error' => 'ID manquant']);
            exit;
        }

        if ($_POST['action'] === 'fetch') {
            // Action : Récupérer les données de l'admin
            $stmt = $pdo->prepare('SELECT a.id, a.email, a.nom, a.prenom,a.active AS active, f.libelle AS filiere, f.type AS cycle 
                       FROM admin a 
                       JOIN Filieres f ON a.id_fil = f.id 
                       WHERE a.id = ?');
            $stmt->execute([$id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                echo json_encode($admin);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Admin non trouvé']);
            }

        }elseif ($_POST['action']  === 'update') {
            // Action : Mettre à jour les données de l'admin
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $prenom = htmlspecialchars($_POST['prenom'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $active = intval($_POST['active'] ?? 0);
    
            if (!$id || !$nom || !$prenom || !$email) {
                echo json_encode(['error' => 'Données manquantes']);
                exit;
            }
    
            try {
                // Mise à jour des informations de l'admin
                $stmt = $pdo->prepare('UPDATE admin SET nom = ?, prenom = ?, email = ?, active = ? WHERE id = ?');
                $stmt->execute([$nom, $prenom, $email, $active, $id]);
    
                // Si réinitialisation du mot de passe demandée
                if (isset($_POST['resetPassword']) && $_POST['resetPassword'] === 'on') {
                    $newPassword = $_POST['newPassword'] ?? '';
                    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
                    if ($newPassword !== $confirmPassword) {
                        echo json_encode(['success' => false, 'error' => 'Les mots de passe ne correspondent pas']);
                        exit;
                    }
    
                    // Hashage du nouveau mot de passe
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
                    // Mise à jour du mot de passe
                    $stmt = $pdo->prepare('UPDATE admin SET password = ? WHERE id = ?');
                    $stmt->execute([$hashedPassword, $id]);
                }
    
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
            }
        }
        else {
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
?>

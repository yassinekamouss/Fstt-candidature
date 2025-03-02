<?php
    session_start();
    require_once("../models/connection.php");

    if (!isset($_GET['token'])) {
        header('Location: ../index.php');
        exit;
    }
    $token = htmlspecialchars($_GET['token']);

    try {
        // Vérifier le token dans la BDD
        $sql = "SELECT id,email  FROM Etudiant WHERE verification_token = :token";
        $statement = $pdo->prepare($sql);
        $statement->execute(["token" => $token]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $_SESSION["failed_message"] = "Lien de vérification invalide ou déjà utilisé.";
            header('Location: ../index.php');
            exit;
        }

        // Mettre à jour le statut de vérification
        $sql_update = "UPDATE Etudiant SET email_verified = 1, verification_token = NULL WHERE verification_token = :token";
        $statement_update = $pdo->prepare($sql_update);
        $statement_update->execute(["token" => $token]);

        //Rediriger l'etudiant vers la formulaire :
        $_SESSION["authenticated"] = true ;
        $_SESSION["etudiant_id"] = $result["id"];
        $pdo = null ;
        header("Location: ../views/formule1.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['failed_message'] = "Une erreur s'est produite lors de la vérification.";
        header('Location: ../index.php');
        exit;
    }

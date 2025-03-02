<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if($_SERVER["REQUEST_METHOD"] != "POST" or !isset($_POST["choix_fi_enr"])){
        header("Location: ../../public/index.php");
        exit ;
    }
    if(!isset($_POST["choix1"]) or !isset($_POST["choix2"]) or !isset($_POST["choix3"])){
        header("Location: ../views/choix_fi.php") ;
        exit ;
    }
    if(empty($_POST["choix1"]) or empty($_POST["choix2"]) or empty($_POST["choix3"])){
        header("Location: ../views/choix_fi.php") ;
        exit ;
    }
    //Inserer les donnees dans la base de données
    try {
        // Connexion à la base de données!
        require_once("../models/connection.php");
    
        
        $stmt = $pdo->prepare('
            INSERT INTO condidature (id_etu, id_fil, ordre)
            VALUES 
            (?, ?, 1),
            (?, ?, 2),
            (?, ?, 3)
        ');

        $stmt->execute([
            $_SESSION['etudiant_id'],
            htmlspecialchars($_POST["choix1"]),
            $_SESSION['etudiant_id'],
            htmlspecialchars($_POST["choix2"]),
            $_SESSION['etudiant_id'],
            htmlspecialchars($_POST["choix3"]),
        ]);
        header("Location: ../views/home.php");
        exit ;

    } catch (Exception $e) {
        // Gestion des erreurs
        echo 'Erreur : ' . $e->getMessage();
    }
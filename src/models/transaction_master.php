<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    //Inserer les donnees dans la base de données
    try {
        // Connexion à la base de données
            require_once("../models/connection.php");

            // Démarrer une transaction
            $pdo->beginTransaction();
            
            $stmt1 = $pdo->prepare('
                INSERT INTO condidature (id_etu, id_fil, ordre)
                VALUES 
                (?, ?, 1),
                (?, ?, 2),
                (?, ?, 3)
            ');

            $stmt1->execute([
                $_SESSION['etudiant_id'],
                $_SESSION['info_bac_3']['choix1'],
                $_SESSION['etudiant_id'],
                $_SESSION['info_bac_3']['choix2'],
                $_SESSION['etudiant_id'],
                $_SESSION['info_bac_3']['choix3'],
            ]);

            // Insertion dans la table 'diplome' pour le bac+3
            $stmt2 = $pdo->prepare("
                INSERT INTO diplome (id_etu, type, specialite, annee_obtention,etablissement,image_url ,note1,note2,niveau) 
                VALUES (?, ?, ?, ?, ? , ? , ? , ?,?)
            ");

            $stmt2->execute([
                $_SESSION["etudiant_id"], // Clé étrangère vers etudiant
                $_SESSION['info_bac_3']['bac_3_type'],
                $_SESSION['info_bac_3']['bac_3_specialite'],
                $_SESSION['info_bac_3']['bac_3_annee_obtention'],
                $_SESSION['info_bac_3']['bac_3_etablissement'],
                $_SESSION['info_bac_3']['bac_3_image'],
                $_SESSION['info_bac_3']['bac_3_note_s5'],
                $_SESSION['info_bac_3']['bac_3_note_s6'],
                3
            ]);

            // Commit de la transaction
            $pdo->commit();
        
            header("Location: ../views/home.php");
            exit ;

    } catch (PDOException $e) {
        die('Erreur PDO : ' . $e->getMessage());
    }catch(Exception $e){
        // Rollback si une erreur survient
        $pdo->rollBack();
        die("Erreur: ".$e->getMessage());
    }
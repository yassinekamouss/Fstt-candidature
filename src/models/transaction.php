<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }

    require_once('./connection.php') ;
    try{

        // Démarrer une transaction
        $pdo->beginTransaction();

        // Ajouter les info dans la table 'etudiant'
        $statement1 = $pdo->prepare("
            UPDATE Etudiant 
            SET 
                nom = ?, 
                prenom = ?, 
                adresse = ?, 
                phone = ?, 
                date_naissance = ?, 
                cin = ?, 
                lieu_naissance = ?, 
                sex = ?
            WHERE id = ?
        ");

        $statement1->execute([
            $_SESSION['info_personnel']['nom'],
            $_SESSION['info_personnel']['prenom'],
            $_SESSION['info_personnel']['adresse'],
            $_SESSION['info_personnel']['tel'],
            $_SESSION['info_personnel']['date_naissance'],
            $_SESSION['info_personnel']['cin'],
            $_SESSION['info_personnel']['lieu_naissance'],
            $_SESSION['info_personnel']['sex'],
            $_SESSION["etudiant_id"]
        ]);

        // Insertion dans la table 'diplome' pour le bac
        $statement2 = $pdo->prepare("
            INSERT INTO diplome (id_etu, type, specialite, annee_obtention, note1,etablissement,image_url,niveau) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $statement2->execute([
            $_SESSION["etudiant_id"], // Clé étrangère vers etudiant
            'Bac',//type du diplome
            $_SESSION['info_bac']['serie_bac'],
            $_SESSION['info_bac']['annee_obtention'],
            $_SESSION['info_bac']['moyenne'],
            $_SESSION['info_bac']['etablis'],
            $_SESSION['info_bac']['bac_image'],
            1
        ]);

        // Insertion dans la table 'diplome' pour le bac+2
        $statement3 = $pdo->prepare("
            INSERT INTO diplome (id_etu, type, specialite, annee_obtention,etablissement,image_url ,note1,note2,note3,note4 ,niveau) 
            VALUES (?, ?, ?, ?, ? , ? , ? , ? , ? , ? , ?)
        ");

        $statement3->execute([
            $_SESSION["etudiant_id"], // Clé étrangère vers etudiant
            $_SESSION['info_bac_2']['bac_2_type'], //type du diplome (deust /deug etc)
            $_SESSION['info_bac_2']['bac_2_specialite'],
            $_SESSION['info_bac_2']['bac_2_annee_obtention'],
            $_SESSION['info_bac_2']['bac_2_etablissement'],
            $_SESSION['info_bac_2']['bac_2_image'],
            $_SESSION['info_bac_2']['bac_2_note_s1'],
            $_SESSION['info_bac_2']['bac_2_note_s2'],
            $_SESSION['info_bac_2']['bac_2_note_s3'],
            $_SESSION['info_bac_2']['bac_2_note_s4'],
            2
        ]);
        // Commit de la transaction
        $pdo->commit();

        //Rediriger l'etudiant vers la page home(de choix):
        header("Location: ../views/home.php");
        exit;

    }catch (PDOException $e) {
        die('Erreur PDO : ' . $e->getMessage());
    }catch(Exception $e){
        // Rollback si une erreur survient
        $pdo->rollBack();
        die("Erreur: ".$e->getMessage());
    }
<?php
    
    session_start();
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if($_SERVER["REQUEST_METHOD"] != 'POST'){
        header("Location: ../../public/index.php") ;
        exit ;
    }
    if(!isset($_POST['edite_info']) and !isset($_POST['edite_bac']) and !isset($_POST['edite_bac_2']) and !isset($_POST['edite_licence'])){
        header("Location: ../../public/index.php");
        exit ;
    }
    require_once("../models/connection.php") ;
    //Modifier info personnelles :
    if(isset($_POST['edite_info'])){
        // Préparer la requête SQL pour la mise à jour des informations
        $query = "
            UPDATE Etudiant
            SET 
                nom = ?,
                prenom = ?,
                cin = ?,
                cne = ?,
                phone = ?,
                sex = ?,
                date_naissance = ?,
                lieu_naissance = ?,
                adresse = ?
            WHERE id = ?
        ";
        try{
            // Préparer la requête
            $statement = $pdo->prepare($query);
            $statement->execute([
                htmlspecialchars($_POST['nom']),
                htmlspecialchars($_POST['prenom']),
                htmlspecialchars($_POST['cin']),
                htmlspecialchars($_POST['cne']),
                htmlspecialchars($_POST['tel']),
                htmlspecialchars($_POST['sex']),
                htmlspecialchars($_POST['date_naissance']),
                htmlspecialchars($_POST['lieu_naissance']),
                htmlspecialchars($_POST['adresse']),
                $_SESSION['etudiant_id']
            ]);
            header("Location: ../views/edit.php");
            exit;

        }catch(Exception $e){
            die("Erreur: ".$e->getMessage());
        }

    }
    //Modifier info de bac :
    else if(isset($_POST['edite_bac'])){

        // Requête SQL pour mettre à jour les informations de Bac
        $query = "
            UPDATE diplome
            SET 
                specialite = ?,
                annee_obtention = ?,
                mention = ?,
                moyenne = ?,
                etablissement = ?,
            WHERE id_etu = ?
            AND type = 'Bac'
        ";

        try {
            // Préparer la requête
            $statement = $pdo->prepare($query);
            // Exécuter la requête avec les paramètres
            $statement->execute([
                htmlspecialchars($_POST['serie_bac']),
                htmlspecialchars($_POST['annee_obtention']),
                htmlspecialchars($_POST['mension']),
                htmlspecialchars($_POST['moyenne']),
                htmlspecialchars($_POST['etablis']),
                $_SESSION['etudiant_id']
            ]);
            
            // Rediriger vers la page d'édition après la mise à jour
            header("Location: ../views/edit.php");
            exit;

        } catch (Exception $e) {
            die("Erreur: ".$e->getMessage());
        }
    }   
    //Modifier info de bac+2 :
    else if(isset($_POST['edite_bac_2'])){

        try{
            // Démarrer une transaction
            $pdo->beginTransaction();

            $query_diplome = "
                UPDATE diplome
                SET 
                    type = ?, 
                    annee_obtention = ?, 
                    specialite = ?, 
                    etablissement = ?
                WHERE id_etu = ?
                AND type = 'deust'
            ";
            $statement_diplome = $pdo->prepare($query_diplome);
            $statement_diplome->execute([
                htmlspecialchars($_POST['bac_2_type']),
                htmlspecialchars($_POST['bac_2_annee_obtention']),
                htmlspecialchars($_POST['bac_2_specialite']),
                htmlspecialchars($_POST['bac_2_etablissement']),
                $_SESSION["etudiant_id"]
            ]);

            // Récupérer l'ID du diplôme mis à jour, s'il existe
            $query_get_updated = "SELECT id FROM diplome WHERE id_etu = ? AND type = 'deust'";
            $statement_get = $pdo->prepare($query_get_updated);
            $statement_get->execute([$_SESSION["etudiant_id"]]);
            $diplome_id = $statement_get->fetchColumn();
            if ($diplome_id) {
                // Mise à jour de la table note_deust avec le bon id_diplome
                $query_notes = "
                    UPDATE note_deust
                    SET 
                        s1 = ?, 
                        s2 = ?, 
                        s3 = ?, 
                        s4 = ?
                    WHERE id_diplome = ?
                ";
                $statement_notes = $pdo->prepare($query_notes);
                $statement_notes->execute([
                    htmlspecialchars($_POST['bac_2_note_s1']),
                    htmlspecialchars($_POST['bac_2_note_s2']),
                    htmlspecialchars($_POST['bac_2_note_s3']),
                    htmlspecialchars($_POST['bac_2_note_s4']),
                    $diplome_id
                ]);
            }
            // Si tout s'est bien passé, valider la transaction
            $pdo->commit();

            // Rediriger ou afficher un message de succès
            header("Location: ../views/edit.php");
            exit;

        } catch (Exception $e) {
            die("Erreur: ".$e->getMessage());
        }
    }
    //Modifier info de licence :
    else{

        try {
            // Démarrer une transaction
            $pdo->beginTransaction();
        
            // Mise à jour de la table diplome
            $query_diplome = "
                UPDATE diplome
                SET 
                    type = ?, 
                    annee_obtention = ?, 
                    specialite = ?, 
                    etablissement = ?
                WHERE id_etu = ?
                AND type = 'lst'
            ";
            $statement_diplome = $pdo->prepare($query_diplome);
            $statement_diplome->execute([
                htmlspecialchars($_POST['bac_3_type']),
                htmlspecialchars($_POST['bac_3_annee_obtention']),
                htmlspecialchars($_POST['bac_3_specialite']),
                htmlspecialchars($_POST['bac_3_etablissement']),
                $_SESSION["etudiant_id"]
            ]);
        
            // Récupérer l'ID du diplôme mis à jour, s'il existe
            $query_get_updated = "SELECT id FROM diplome WHERE id_etu = ? AND type = 'lst'";
            $statement_get = $pdo->prepare($query_get_updated);
            $statement_get->execute([$_SESSION["etudiant_id"]]);
            $diplome_id = $statement_get->fetchColumn();
        
            if ($diplome_id) {
                // Mise à jour de la table note_licence avec le bon id_diplome
                $query_notes = "
                    UPDATE note_licence
                    SET 
                        s5 = ?, 
                        s6 = ?
                    WHERE id_diplome = ?
                ";
                $statement_notes = $pdo->prepare($query_notes);
                $statement_notes->execute([
                    htmlspecialchars($_POST['bac_3_note_s1']),
                    htmlspecialchars($_POST['bac_3_note_s2']),
                    $diplome_id
                ]);
            }
        
            // Si tout s'est bien passé, valider la transaction
            $pdo->commit();
        
            // Rediriger ou afficher un message de succès
            header("Location: ../views/edit.php");
            exit;
        
        } catch (Exception $e) {
            die("Erreur: ".$e->getMessage());
        }

    }
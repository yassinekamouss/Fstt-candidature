<?php
    session_start();
    if (!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])) {
        header("Location: ../services/login.php");
        exit(0);
    }

    if (isset($_GET['download_csv']) && $_GET['download_csv'] == 'true') {
        require_once('../../models/connection.php');

        // $filiere_id = isset($_GET['filiere_id']) ? $_GET['filiere_id'] : null;
        
        // try {
        //     // Configuration pour UTF-8
        //     $pdo->exec("SET NAMES 'utf8'");
            
        //     // Requête SQL améliorée
        //     $query = "
        //         SELECT 
        //             vm.id AS etudiant_id, 
        //             vm.nom, 
        //             vm.prenom, 
        //             vm.cne,
        //             vm.moyenne_generale
        //         FROM 
        //             v_etudiants_moyennes_detaillees vm
        //         INNER JOIN condidature c ON vm.id = c.id_etu
        //         INNER JOIN Filieres f ON c.id_fil = f.id
        //         WHERE 
        //             c.status = 'Valide'
        //             AND c.id_fil = :filiere_id
        //         ORDER BY 
        //             vm.moyenne_generale DESC
        //         LIMIT 300;
        //     ";
            
        //     $statement = $pdo->prepare($query);
            
        //     if ($filiere_id) {
        //         $statement->bindParam(':filiere_id', $filiere_id, PDO::PARAM_INT);
        //     }
            
        //     $statement->execute();
        //     $etudiants = $statement->fetchAll(PDO::FETCH_ASSOC);
            
        //     if (empty($etudiants)) {
        //         throw new Exception("Aucun étudiant trouvé.");
        //     }

        //     // Configuration du fichier CSV
        //     $filename = 'Liste_des_admis_' . date('Y-m-d_H-i-s') . '.csv';
            
        //     // Headers pour le téléchargement et l'encodage UTF-8
        //     header('Content-Type: text/csv; charset=UTF-8');
        //     header('Content-Disposition: attachment; filename="' . $filename . '"');
        //     header('Pragma: no-cache');
        //     header('Expires: 0');
            
        //     // Ajout du BOM pour Excel
        //     echo "\xEF\xBB\xBF";
            
        //     $output = fopen('php://output', 'w');
            
        //     // En-têtes en français
        //     $headers = [
        //         '#',
        //         'Nom',
        //         'Prénom',
        //         'CNE',
        //         'N° Dossier'
        //     ];
            
        //     fputcsv($output, $headers, ';');  // Utilisation du point-virgule comme séparateur
            
        //     $i = 1;
        //     foreach ($etudiants as $etudiant) {
        //         $row = [
        //             $i++,
        //             utf8_decode($etudiant['nom']),
        //             utf8_decode($etudiant['prenom']),
        //             $etudiant['cne'],
        //             $etudiant['etudiant_id']
        //         ];
        //         fputcsv($output, $row, ';');
        //     }
            
        //     fclose($output);
            
        // } catch (Exception $e) {
        //     header('Content-Type: text/html; charset=UTF-8');
        //     echo "Erreur : " . $e->getMessage();
        // }

        $filiere_id = isset($_GET['filiere_id']) ? $_GET['filiere_id'] : null;

        try {

            // Configuration pour UTF-8
            $pdo->exec("SET NAMES 'utf8'");

            // Requête SQL améliorée avec marqueur nommé
            $query = "
                SELECT 
                vm.id AS etudiant_id, 
                vm.nom, 
                vm.prenom, 
                vm.cne,
                vm.moyenne_generale
                FROM 
                v_etudiants_moyennes_detaillees vm
                INNER JOIN condidature c ON vm.id = c.id_etu
                INNER JOIN Filieres f ON c.id_fil = f.id
                WHERE 
                c.status = 'Valide'
                AND c.id_fil = :filiere_id
                ORDER BY 
                vm.moyenne_generale DESC;
            ";

            $statement = $pdo->prepare($query);

            if ($filiere_id) {
                $statement->bindParam(':filiere_id', $filiere_id, PDO::PARAM_INT);
            }

            $statement->execute();
            $etudiants = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($etudiants)) {
                throw new Exception("Aucun étudiant trouvé.");
            }

            // Configuration du fichier CSV
            $filename = 'Liste_des_admis_' . date('Y-m-d_H-i-s') . '.csv';

            // Headers pour le téléchargement et l'encodage UTF-8
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Ajout du BOM pour Excel
            echo "\xEF\xBB\xBF";

            $output = fopen('php://output', 'w');

            // En-têtes en français
            $headers = [
                '#',
                'Nom',
                'Prénom',
                'CNE',
                'N° Dossier'
            ];

            fputcsv($output, $headers, ';'); // Utilisation du point-virgule comme séparateur

            $i = 1;
            foreach ($etudiants as $etudiant) {
                $row = [
                $i++,
                utf8_decode($etudiant['nom']),
                utf8_decode($etudiant['prenom']),
                $etudiant['cne'],
                $etudiant['etudiant_id']
                ];
                fputcsv($output, $row, ';');
                        }
                        
                        fclose($output);
                        
        } catch (Exception $e) {
            header('Content-Type: text/html; charset=UTF-8');
            echo "Erreur : " . $e->getMessage();
        }
    }
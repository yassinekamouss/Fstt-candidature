<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    // Connexion à la base de données
   require_once('../../models/connection.php');

   try{
        // Statistiques pour le premier graphique (candidatures par type de filière)
        $stats = [];
        $types = ['LICENCE', 'MASTER', 'FI']; // FI pour Formation d'Ingénieur

        foreach ($types as $type) {
            $query = "SELECT f.libelle as label, COUNT(c.id) as y 
                     FROM Filieres f 
                     LEFT JOIN condidature c ON f.id = c.id_fil 
                     WHERE f.type = ? 
                     GROUP BY f.id, f.libelle";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([$type]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // Limiter les libellés à 10 caractères
            foreach ($results as &$result) {
                $result['label'] = strlen($result['label']) > 10 
                    ? substr($result['label'], 0, 10) . "..." 
                    : $result['label'];
            }
        
            // Filtrer les résultats avec y > 0
            $filteredResults = array_filter($results, function ($result) {
                return $result['y'] > 0;
            });
        
            $stats[strtolower($type)] = $filteredResults;
        }
        

        // Statistiques pour le deuxième graphique (répartition par filière d'ingénieur)
        $queryFI = "SELECT f.libelle as label, 
                    (COUNT(c.id) * 100.0 / (SELECT COUNT(*) FROM condidature WHERE id_fil IN 
                        (SELECT id FROM Filieres WHERE type = 'FI'))) as y
                    FROM Filieres f 
                    LEFT JOIN condidature c ON f.id = c.id_fil 
                    WHERE f.type = 'FI'
                    GROUP BY f.id, f.libelle";
        
        $stmtFI = $pdo->prepare($queryFI);
        $stmtFI->execute();
        $statsFI = $stmtFI->fetchAll(PDO::FETCH_ASSOC);

        // Statistiques pour le troisième graphique (candidatures validées/refusées)
        $queryValidation = "SELECT 
                            (SUM(CASE WHEN status = 'Valide' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as validees,
                            (SUM(CASE WHEN status = 'Refusé' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as refusees
                           FROM condidature";
        
        $stmtValidation = $pdo->prepare($queryValidation);
        $stmtValidation->execute();
        $statsValidation = $stmtValidation->fetch(PDO::FETCH_ASSOC);
        // var_dump($stats);
        echo json_encode(['filieres' => $stats,'ingenieur' => $statsFI,'validation' => $statsValidation]) ;
        exit ;
    }
    catch(PDOException $e){
        die("Erreur : ". $e->getMessage());
    }
?>

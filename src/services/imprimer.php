<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    require '../../vendor/autoload.php'; // Assurez-vous que Dompdf est bien installé via Composer

    use Dompdf\Dompdf;
    use Dompdf\Options;

    try{
        $preInscription = isset($_GET['pre-inscription']) ? $_GET['pre-inscription'] : ''; // Par défaut, vide si non défini
        $etudiant_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['etudiant_id'];

        // Activer la capture du contenu
        ob_start();
        include '../views/imprimer.php'; // Inclut le fichier externe
        $html = ob_get_clean(); // Récupère le contenu généré
    
        // Initialiser Dompdf
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // Support des caractères spéciaux
        $options->setChroot('') ; 
        $dompdf = new Dompdf($options);
    
        // Charger le contenu HTML dans Dompdf
        $dompdf->loadHtml($html);
        $dompdf->set_option('isRemoteEnabled' , true) ;
    
        // Options de rendu
        $dompdf->setPaper('A4', 'portrait');
    
        // Rendre le HTML en PDF
        $dompdf->render();
    
        // Afficher le PDF dans le navigateur (sans téléchargement direct)
        header("Content-Type: application/pdf");
        echo $dompdf->output();
    
    }catch(Exception $e){
        die("Erreur: ".$e->getMessage()) ;
    }



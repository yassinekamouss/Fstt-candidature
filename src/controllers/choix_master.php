<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if($_SERVER["REQUEST_METHOD"] != "POST" or !isset($_POST["choix_master_enr"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if(!isset($_POST["bac_3_type"]) or !isset($_POST["bac_3_annee_obtention"]) or !isset($_POST["bac_3_specialite"]) or !isset($_POST["bac_3_etablissement"]) or 
    !isset($_POST["bac_3_note_s5"]) or !isset($_POST["bac_3_note_s6"]) or !isset($_POST["choix1"]) or !isset($_POST["choix2"]) or !isset($_POST["choix3"]) or !isset($_FILES["bac_3_image"])){
        header("Location: ../views/choix_master.php");
        exit(0) ;
    }
    if(empty($_POST["bac_3_type"]) or empty($_POST["bac_3_annee_obtention"]) or empty($_POST["bac_3_specialite"]) or empty($_POST["bac_3_etablissement"]) or 
    empty($_POST["bac_3_note_s5"]) or empty($_POST["bac_3_note_s6"]) or empty($_POST["choix1"]) or empty($_POST["choix2"]) or empty($_POST["choix3"]) or empty($_FILES["bac_3_image"])){
        header("Location: ../views/choix_master.php");
        exit(0) ;
    }  
    //validation des données :
    // if(!filter_var($_POST['bac_3_note_s5'] , FILTER_VALIDATE_FLOAT) or !filter_var($_POST['bac_3_note_s6'] , FILTER_VALIDATE_FLOAT) 
    //     or !filter_var($_POST['bac_2_annee_obtention'] , FILTER_VALIDATE_INT)){
    //     header("Location: ../views/choix_master.php");
    //     exit ;
    // }

    if(isset($_SESSION["info_bac_3"])){
        unset($_SESSION["info_bac_3"]) ;
    }
    $_SESSION["info_bac_3"] = [
        'bac_3_type' => htmlspecialchars($_POST['bac_3_type']) ,
        'bac_3_annee_obtention' => htmlspecialchars($_POST['bac_3_annee_obtention']),
        'bac_3_specialite' => htmlspecialchars($_POST['bac_3_specialite']),
        'bac_3_etablissement' => htmlspecialchars($_POST['bac_3_etablissement']),
        'bac_3_note_s5' => htmlspecialchars($_POST['bac_3_note_s5']),
        'bac_3_note_s6' => htmlspecialchars($_POST['bac_3_note_s6']),
        'choix1' => htmlspecialchars($_POST['choix1']),
        'choix2' => htmlspecialchars($_POST['choix2']),
        'choix3' => htmlspecialchars($_POST['choix3']),
    ];

    // Configuration
    define('UPLOAD_DIR', '/var/www/site1.com/PHP/project/FINAL/uploads/temp');
    $maxFileSize = 2 * 1024 * 1024; // 2 Mo
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    
    // Vérification de l'upload
    $file = $_FILES['bac_3_image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Erreur lors du téléchargement : " . $file['error']);
    }

    $fileMimeType = mime_content_type($file['tmp_name']);
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileMimeType, $allowedMimeTypes) || !in_array($fileExtension, $allowedExtensions)) {
        die("Format de fichier non autorisé !");
    }

    if ($file['size'] > $maxFileSize) {
        die("Le fichier est trop volumineux. Taille maximale autorisée : 2 Mo.");
    }

    $fileName = uniqid('bac_3_', true) . '.' . $fileExtension;
    $uploadFilePath = UPLOAD_DIR . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        $_SESSION['info_bac_3']['bac_3_image'] = $uploadFilePath;

        header("Location: ../models/transaction_master.php");
        exit ;
    } else {
        echo "Une erreur est survenue lors du téléchargement.";
    }
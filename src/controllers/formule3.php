<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if($_SERVER["REQUEST_METHOD"] != "POST" or !isset($_POST["enr_form3"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if(!isset($_POST["bac_2_type"]) or !isset($_POST["bac_2_annee_obtention"]) or !isset($_POST["bac_2_specialite"]) or !isset($_POST["bac_2_etablissement"]) or 
    !isset($_POST["bac_2_note_s1"]) or !isset($_POST["bac_2_note_s2"]) or !isset($_POST["bac_2_note_s3"]) or !isset($_POST["bac_2_note_s4"]) or !isset($_FILES["bac_2_image"])){
        header("Location: ../views/formule3.php");
        exit(0) ;
    }
    if(empty($_POST["bac_2_type"]) or empty($_POST["bac_2_annee_obtention"]) or empty($_POST["bac_2_specialite"]) or empty($_POST["bac_2_etablissement"]) or 
    empty($_POST["bac_2_note_s1"]) or empty($_POST["bac_2_note_s2"]) or empty($_POST["bac_2_note_s3"]) or empty($_POST["bac_2_note_s4"]) or empty($_FILES["bac_2_image"])){
        header("Location: ../views/formule3.php");
        exit(0) ;
    }  
    
    if(isset($_SESSION["info_bac_2"])){
        unset($_SESSION["info_bac_2"]) ;
    }
    $_SESSION["info_bac_2"] = [
        'bac_2_type' => htmlspecialchars($_POST['bac_2_type']) ,
        'bac_2_annee_obtention' => htmlspecialchars($_POST['bac_2_annee_obtention']),
        'bac_2_specialite' => htmlspecialchars($_POST['bac_2_specialite']),
        'bac_2_etablissement' => htmlspecialchars($_POST['bac_2_etablissement']),
        'bac_2_note_s1' => htmlspecialchars($_POST['bac_2_note_s1']),
        'bac_2_note_s2' => htmlspecialchars($_POST['bac_2_note_s2']),
        'bac_2_note_s3' => htmlspecialchars($_POST['bac_2_note_s3']),
        'bac_2_note_s4' => htmlspecialchars($_POST['bac_2_note_s4'])
    ];

    // Configuration
    define('UPLOAD_DIR', '/var/www/site1.com/PHP/project/FINAL/uploads/temp');
    $maxFileSize = 2 * 1024 * 1024; // 2 Mo
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    
    // Vérification de l'upload
    $file = $_FILES['bac_2_image'];

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

    $fileName = uniqid('bac_2_', true) . '.' . $fileExtension;
    $uploadFilePath = UPLOAD_DIR . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        $_SESSION['info_bac_2']['bac_2_image'] = $uploadFilePath;
        header("Location: ../models/transaction.php");
        exit ;
    } else {
        echo "Une erreur est survenue lors du téléchargement.";
    }
?>

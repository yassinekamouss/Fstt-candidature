<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if($_SERVER["REQUEST_METHOD"] != "POST" or !isset($_POST["enr_form2"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if(!isset($_POST["serie_bac"]) or !isset($_POST["annee_obtention"]) or !isset($_POST["moyenne"]) or 
    !isset($_POST["etablis"]) or !isset($_FILES["bac_image"])){
        header("Location: ../views/formule2.php");
        exit(0) ;
    }
    if(empty($_POST["serie_bac"]) or empty($_POST["annee_obtention"]) or empty($_POST["moyenne"]) or 
    empty($_POST["etablis"]) or empty($_FILES["bac_image"])){
        header("Location: ../views/formule2.php");
        exit(0) ;
    }   

    if(isset($_SESSION["info_bac"])){
        unset($_SESSION["info_bac"]) ;
    }
    $_SESSION["info_bac"] = [
        'serie_bac' => htmlspecialchars($_POST["serie_bac"]),
        'annee_obtention' => htmlspecialchars($_POST["annee_obtention"]),
        'moyenne' => htmlspecialchars($_POST["moyenne"]),
        'etablis' => htmlspecialchars($_POST["etablis"])
    ];

    // Configuration
    define('UPLOAD_DIR', '/var/www/site1.com/PHP/project/FINAL/uploads/temp');
    $maxFileSize = 2 * 1024 * 1024; // 2 Mo
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    
    // Vérification de l'upload
    $file = $_FILES['bac_image'];

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

    $fileName = uniqid('bac_', true) . '.' . $fileExtension;
    $uploadFilePath = UPLOAD_DIR . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        $_SESSION['info_bac']['bac_image'] = $uploadFilePath;
        header("Location: ../views/formule3.php");
        exit ;
    } else {
        echo "Une erreur est survenue lors du téléchargement.";
    }
?>
<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if($_SERVER["REQUEST_METHOD"] != "POST" or !isset($_POST["enr_form1"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if(!isset($_POST["nom"]) or !isset($_POST["prenom"]) or !isset($_POST["cin"]) or !isset($_POST["cne"]) or 
    !isset($_POST["tel"]) or !isset($_POST["sex"]) or !isset($_POST["date_naissance"]) or 
    !isset($_POST["lieu_naissance"]) or !isset($_POST["adresse"]) or !isset($_FILES["cin_image"])){
        header("Location: ../views/formule1.php");
        exit(0) ;
    }
    if(empty($_POST["nom"]) or empty($_POST["prenom"]) or empty($_POST["cin"]) or empty($_POST["cne"]) or 
    empty($_POST["tel"]) or empty($_POST["sex"]) or empty($_POST["date_naissance"]) or 
    empty($_POST["lieu_naissance"]) or empty($_POST["adresse"]) or empty($_FILES["cin_image"])){
        header("Location: ../views/formule1.php");
        exit(0) ;
    }
    
    if (isset($_SESSION["info_personnel"])){
        unset($_SESSION["info_personnel"]) ;
    }
    $_SESSION["info_personnel"] = [
        'nom' => htmlspecialchars($_POST["nom"]),
        'prenom' => htmlspecialchars($_POST["prenom"]),
        'cin' => htmlspecialchars($_POST["cin"]),
        'cne' => htmlspecialchars($_POST["cne"]),
        'tel' => htmlspecialchars($_POST["tel"]),
        'sex' => htmlspecialchars($_POST["sex"]),
        'date_naissance' => htmlspecialchars($_POST["date_naissance"]),
        'lieu_naissance' => htmlspecialchars($_POST["lieu_naissance"]),
        'adresse' => htmlspecialchars($_POST["adresse"])
    ];

    // Configuration
    define('UPLOAD_DIR', '/var/www/site1.com/PHP/project/FINAL/uploads/temp');
    $maxFileSize = 2 * 1024 * 1024; // 2 Mo
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    
    // Vérification de l'upload
    $file = $_FILES['cin_image'];

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

    $fileName = uniqid('cin_', true) . '.' . $fileExtension;
    $uploadFilePath = UPLOAD_DIR . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        $_SESSION['info_personnel']['cin_image'] = $uploadFilePath;
        header("Location: ../views/formule2.php");
        exit ;
    } else {
        echo "Une erreur est survenue lors du téléchargement.";
    }
?>
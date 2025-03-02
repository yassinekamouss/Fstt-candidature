<?php
    session_start();
    $db_server = "localhost";
    $db_user = "root";
    $db_password = "Kamouss@123"; 
    $db_name =  "plateforme_universitaire";

    try {

        // Connexion à la base de données
        $pdo = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    } catch (Exception $e) {
        $_SESSION["errors_connection"] = "Problème interne. Veuillez réessayer plus tard.";
        header("Location: ../views/login.php");
    }

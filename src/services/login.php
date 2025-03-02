<?php

    session_start() ;
    if(isset($_SESSION["authenticated"]) and $_SESSION["authenticated"] === true and isset($_SESSION["etudiant_id"])){
        header("Location: ../views/home.php");
        exit(0) ;
    }
    if($_SERVER["REQUEST_METHOD"] != 'POST' or !isset($_POST["login"])){
        header("Location: ../views/login.php");
        exit(0) ;
    }
    //Connection avec BDD :
    require_once("../models/connection.php");
    
    if (!isset($_POST["email"]) or !isset($_POST["password"]) or empty($_POST["email"]) or empty($_POST["password"])){
    header("Location: ../views/login.php") ;
    exit;
    }
    //Récupérer les donnees :
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["login_errors"] = "Email incorrecte";
        header("Location: ../views/login.php");
        exit;
    }

    // vérifier que les info correspondre a un étudiant dans la bdd :
    try{

        // Préparer et exécuter la requête
        $statement = $pdo->prepare("SELECT id,email, password, email_verified FROM Etudiant WHERE email = :email");
        $statement->execute(["email" => $email]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    
        if (!$result){
            $_SESSION["login_errors"]  = "Identifiants incorrects.";
            header("Location: ../views/login.php");
            exit;
        }
        if (!password_verify($password , $result["password"])){
            $_SESSION["login_errors"] = "Identifiants incorrects.";
            header("Location: ../views/login.php");
            exit;
        }
        if (!$result["email_verified"]) {
            $_SESSION["login_errors"] = "Email n'est pas activer.";
            header("Location: ../views/login.php");
            exit;
        }

        // SI l'etudiant présenté dans la bdd :
        session_regenerate_id(true) ;
        $_SESSION["authenticated"] = true ;
        $_SESSION["etudiant_id"] = $result["id"];

        //Redériger vers page de home :
        header("Location: ../views/home.php") ;
        exit ;

    }catch(Exception $e){
        $_SESSION["login_errors"] = "Une erreur interne s'est produite. Veuillez réessayer plus tard.";
        header("Location: ../views/login.php");
    
    }finally{
        $pdo = null ;
    }

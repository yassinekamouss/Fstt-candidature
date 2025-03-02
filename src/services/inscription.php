<?php
    session_start();
    //Inclure la connection avec la BDD :
    require_once("../models/connection.php");
    require '../../vendor/autoload.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception ;
    
    unset($_SESSION["errors_inscription"]);

    if ($_SERVER['REQUEST_METHOD'] != 'POST' or !isset($_POST["signin"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if (!isset($_POST["email"]) or !isset($_POST["password"]) or !isset($_POST["confirm_password"])
        or empty($_POST["email"]) or empty($_POST["password"]) or empty($_POST["confirm_password"])){
        header("Location: ../../public/index.php");
        exit;
    }

    $email = htmlspecialchars($_POST["email"]) ;
    $password = htmlspecialchars($_POST["password"]);
    $confirm_password = htmlspecialchars($_POST["confirm_password"]);

    // vérifier la validité des données : 
    if (!filter_var($email , FILTER_VALIDATE_EMAIL)){
        $_SESSION["errors_inscription"]["email"] = "Email invalide.";
        header("Location: ../../public/index.php");
        exit;
    }
    if ($password !== $confirm_password){
        $_SESSION["errors_inscription"]["password"] = "Mots de passe ne correspondent pas";
        header("Location: ../../public/index.php");
        exit;
    }
    try{

        // Vérigier si l'email déjà existe : 
        $sql_check = "SELECT COUNT(*) FROM Etudiant WHERE email = :email";
        $statement_check = $pdo->prepare($sql_check);
        $statement_check->execute(["email" => $email]);
    
        if ($statement_check->fetchColumn() > 0) {
            $_SESSION["errors_inscription"]["email"] = "Email déjà utilisé.";
            header("Location: ../../public/index.php");
            exit;
        }

        //Enregistrer l'étudiant dans la BDD :
        $verification_token = bin2hex(random_bytes(16)); // Génère un token unique
        $sql = "INSERT INTO Etudiant (email , password , verification_token) VALUES(:email , :password , :token)";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            "email" => $email ,
            "password" => password_hash($password , PASSWORD_BCRYPT),
            "token" => $verification_token 
        ]);

        //Envoyer un mail de vérification :

        // Configuration du serveur SMTP de Gmail
        $mail = new PHPMailer(true) ;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com' ;
        $mail->SMTPAuth = true ;
        $mail->Username = 'yassinekamouss76@gmail.com' ;
        // $mail->Password = 'srwh aafi svur xswl' ;
        $mail->Password = 'gccu itvw tbqs rnfu' ;
        $mail->SMTPSecure = 'ssl' ;
        $mail->Port = 465 ;

        // Destinataire et expéditeur
        $mail->setFrom('yassinekamouss76@gmail.com' , 'FSTT');
        $mail->addAddress($email);

        // Contenu du message
        $mail->isHTML(true) ;
        $mail->Subject = 'Confirmation d\'inscription';
        $mailBodyContent = file_get_contents('../views/mail.html');
        $mailBodyContent = str_replace('{{user_email}}', $email , $mailBodyContent);

        $verification_link = "http://site1.com/PHP/project/FINAL/src/services/verify.php?token=$verification_token";
        $mailBodyContent = str_replace('{{verification_link}}', $verification_link, $mailBodyContent);
        
        $mail->Body = $mailBodyContent;

        // Envoi de l'email
        $mail->send();

        // Redériger l'etudiant vers la page d'index :
        $_SESSION['success_message'] = "Votre inscription a été réalisée avec succès, Voir votre boite mail.";  
        $pdo = null ;  
        header("Location: ../../public/index.php");
        exit;
    
    }catch(Exception $e){
        $_SESSION['failed_message'] = "Votre inscription n'a pas été réalisée avec succès !" . $e->getMessage();    
        // Rediriger vers la page d'inscription
        header('Location: ../../public/index.php');
        exit;
    }
<?php
    session_start();

    // Détruire toutes les variables de session
    session_unset();

    // Détruire la session elle-même
    session_destroy();

    // Rediriger l'utilisateur vers la page de connexion ou d'accueil
    header("Location: ../../public/index.php");
    exit();
?>
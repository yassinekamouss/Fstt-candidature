<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
?>
<nav class="fixed-top vh-100" style="background-color:#282828; width: 13%;">
    <div class="text-center mt-4">
        <img src="../../../assets/fstt_admin_logo_n-removebg-preview.png" alt="">
    </div>
    <hr class="text-light mx-3">
    <div class="text-light mx-auto" style="margin-top: 5rem;">
        <p class="fs-5 mx-3 p-2 rounded" style="cursor:pointer; background-color:#3a3a3a;"><a href="../public/index.php" class="text-decoration-none text-light"><i class="fa-solid fa-house"></i> Home</a></p>
        <p class="fs-5 mx-3 p-2 rounded" style="cursor:pointer;"><a href="../views/admins.php" class="text-decoration-none text-light"><i class="fa-solid fa-users-gear"></i> Utilisateurs</a></p>
        <p class="fs-5 mx-3 p-2 rounded" style="cursor:pointer;"><a href="../views/candidatures.php" class="text-decoration-none text-light"><i class="fa-solid fa-bars"></i> Candidatures</a></p>
        <p class="fs-5 mx-3 p-2 rounded" style="cursor:pointer;"><a href="../views/etudiants.php" class="text-decoration-none text-light"><i class="fa-solid fa-user-graduate"></i> Etudiants</a></p>
        <p class="fs-5 mx-3 p-2 rounded" style="cursor:pointer;"><a href="../views/filieres.php" class="text-decoration-none text-light"><i class="fa-solid fa-code-branch"></i> Filières</a></p>
    </div>
    <div style="margin-top: 20rem;">
        <p class="fs-5 mx-3 p-2 rounded text-light" style="cursor:pointer;"><a href="../services/logout.php" class="text-decoration-none text-light"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></p>
    </div>
</nav>
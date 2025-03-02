<?php   
    session_start() ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="icon" type="image/png" href="../../../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../vendor/fortawesome/font-awesome/css/all.min.css">
</head>
<body>
    <header class="container d-flex justify-content-between align-items-center mt-4">
        <div>
            <img src="../../../assets/fstt_logo.png" alt="Logo de fstt">
        </div>
        <div class="d-flex flex-column">
            <span class="text-center fs-5" >Faculté des Sciences et Techniques - Tanger</span>
            <span class="text-center">Administration des Pré-inscriptions</span>
            <span class="text-center">A.U : 2025-2026</span>
        </div>
        <div>
            <img src="../../../assets/logo-uae.png" alt="Logo de uae">
        </div>
    </header>
    <main class="container" style="margin-top: 70px;">
        <div class="mt-5 text-center">
            <h4>Bienvenue dans le Portail de Gestion Administrative</h4>
            <span class="mt-3" style="font-size: 17px;">Veuillez saisir vos identifiants administratifs pour accéder <br />à l'interface de gestion.</span>
        </div>
        <div class="d-flex justify-content-center mt-5">
            <div class="p-5 border" style="width: 600px;">
                <form action="../services/login.php" method="post">
                    <div class="mb-4">
                        <label for="email">Email : </label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label for="password">Password : </label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <?php if(isset($_SESSION["login_errors"])) : ?>
                            <span class="text-danger p-1"><?php
                                echo $_SESSION["login_errors"];
                                unset($_SESSION["login_errors"]); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <input type="submit" name="login" value="Se connecter" class="btn btn-secondary form-control fw-bold fs-5" />
                    </div>
                </form>
                <div class="d-flex justify-content-between mt-2" id="footer">
                    <span>mot de passe oublier</span>
                    <a href="../public/index.php">Récupérer</a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
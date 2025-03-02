<?php
    session_start() ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-inscription</title>
    <link rel="icon" type="image/png" href="../assets/iconFSTT.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/fortawesome/font-awesome/css/all.min.css">
    <script src="../assets/sweetalert2/sweetalert2.all.min.js"></script>
</head>
<body>
    <header class="container d-flex justify-content-between align-items-center mt-4">
        <div>
            <img src="../assets/fstt_logo.png" alt="Logo de fstt">
        </div>
        <div class="d-flex flex-column">
            <span class="text-center fs-5" >Faculté des Sciences et Techniques - Tanger</span>
            <span class="text-center">pre-inscription en ligne aux licences/master et Fi</span>
            <span class="text-center">A.U : 2025-2026</span>
        </div>
        <div>
            <img src="../assets/logo-uae.png" alt="Logo de uae">
        </div>
    </header>
    <main class="container" style="margin-top: 70px;">
        <div class="mt-5 text-center">
            <h4>Bienvenue dans le Portail de Pré-Inscription</h4>
            <span class="mt-3" style="font-size: 17px;">Veuillez saisir votre adresse e-mail et créer un mot de passe pour démarrer le <br /> processus de pré-inscription.</span>
        </div>
        <div class="mt-4 alert alert-warning d-flex align-items-center mx-auto" role="alert" style="width: 70%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <div>
                Assurez-vous de fournir une adresse e-mail valide que vous consultez régulièrement.
            </div>
        </div>
        <div class="mt-4 mx-auto" style="width: 40%;">
            <form action="../src/services/inscription.php" method="post">
                <div class="mb-3 flex-grow-1 me-2">
                    <label for="email">Email: </label><br>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control" aria-describedby="basic-addon1">
                    </div>
                    <?php if(isset($_SESSION["errors_inscription"]["email"])): ?>
                    <span class="text-danger p-1">
                        <?php echo $_SESSION["errors_inscription"]["email"] ;unset($_SESSION["errors_inscription"]);?>
                    </span>
                    <?php endif ?>
                </div>
                <div class="mb-3 flex-grow-1 me-2">
                    <label for="password">Mot de passe: </label><br>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon2">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control" aria-describedby="basic-addon2">
                    </div>
                </div>
                <div class="mb-3 flex-grow-1 me-2">
                    <label for="confirm_password">Confirmer mot de passe: </label><br>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon3">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="confirm_password" class="form-control" aria-describedby="basic-addon3">
                    </div>
                    <?php if(isset($_SESSION["errors_inscription"]["password"])): ?>
                    <span class="text-danger p-1">
                        <?php echo $_SESSION["errors_inscription"]["password"] ;unset($_SESSION["errors_inscription"]);?>
                    </span>
                    <?php endif ?>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <input type="submit" name="signin" value="Créer le compte" class="btn btn-success form-control fw-bold fs-6"  />
                </div>
            </form>
            <div class="mt-5 text-center">
                <span class="fw-bold">Si Vous etes déjà inscrit,</span><br>
                <span class="mt-5">Cliquez sur le bouton ci-dessous pour accéder à votre espace personnel</span> <br>
                <a href="../src/views/login.php" class="btn btn-sm btn-primary rounded-pill mt-3">Se connecter</a>
            </div>
        </div>
        <div class="mt-4 alert alert-info d-flex align-items-center mx-auto" role="alert" style="width: 70%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
                Une fois votre compte créé, vous recevrez un e-mail contenant un lien de vérification.
            </div>
        </div>
        <div class="mt-4 alert alert-danger d-flex align-items-center mx-auto" role="alert" style="width: 70%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Info:">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            <div>
            Si vous ne recevez pas l'e-mail dans les 10 minutes, vérifiez votre dossier spam ou contactez notre support technique.
            </div>
        </div>
    </main>
    <!-- <footer class=""> -->

    <?php if (isset($_SESSION["failed_message"])): ?>
        <script src="../assets/js/index.js" data-type="failed" data-message="<?= htmlspecialchars($_SESSION['failed_message'], ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php unset($_SESSION["failed_message"]);endif; ?>
    <?php if (isset($_SESSION["success_message"])): ?>
        <script src="../assets/js/index.js" data-type="success" data-message="<?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php unset($_SESSION["success_message"]);endif; ?>
</body>
</html>
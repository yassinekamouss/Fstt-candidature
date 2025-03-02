<?php
    session_start() ;
    if(!isset($_SESSION["authenticated_admin"]) or $_SESSION["authenticated_admin"] != true or !isset($_SESSION["admin_id"])){
        header("Location: ../services/login.php");
        exit(0) ;
    }
    require_once('../../models/connection.php') ;

    try{

        $id_candidature = htmlspecialchars($_GET['id']); // Récupérer l'ID de la candidature

        // Préparer la requête SQL pour récupérer à la fois les diplômes et la filière
        $sql = "
            SELECT 
                d.*, 
                f.libelle AS filiere_libelle, 
                f.type AS filiere_type, 
                c.id_etu AS etudiant_id
            FROM 
                diplome d
            JOIN 
                condidature c ON d.id_etu = c.id_etu
            JOIN 
                Filieres f ON c.id_fil = f.id
            WHERE 
                c.id = :id_candidature
        ";

        $stmt = $pdo->prepare($sql);

        // Lier le paramètre de la requête
        $stmt->bindParam(':id_candidature', $id_candidature, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();

        // Récupérer tous les résultats
        $diplomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Créer des variables pour chaque niveau spécifique
        $bac = [];
        $bac_2 = [];
        $bac_3 = [];
        $filiere = null;
        $etudiant_id = null ;

        // Parcourir chaque diplôme et les classer par niveau
        foreach ($diplomes as $diplome) {
            $diplome['image_url'] = str_replace('/var/www/', '', $diplome['image_url']);
            // Extraire les informations sur la filière une seule fois
            if (!$filiere) {
                $filiere = [
                    'libelle' => $diplome['filiere_libelle'],
                    'type' => $diplome['filiere_type']
                ];
            }
            if (!$etudiant_id) {
                $etudiant_id = $diplome['etudiant_id'];
            }

            // Supposons que le niveau est stocké dans un champ 'niveau' de la table
            $niveau = $diplome['niveau'];

            // Ajouter le diplôme à la bonne variable en fonction du niveau
            if ($niveau == 1) {
                $bac[] = $diplome;
            } elseif ($niveau == 2) {
                $bac_2[] = $diplome;
            } elseif ($niveau == 3) {
                $bac_3[] = $diplome;
            }
        }

    }catch(PDOException $e){
        die("Erreur: " .$e->getMessage()) ;
    }
   
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traitement de candidature</title>
    <link rel="icon" type="image/png" href="../../../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../vendor/fortawesome/font-awesome/css/all.min.css">
    <style>
        .title-section {
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body style="background-color: #EDEDED;">
    <div class="d-flex">
        <?php include_once("../template/sidebar.php") ?>
        <main style="width:87%;margin-left:13%;">
            <header class="shadow-sm bg-white p-3 w-100 d-flex justify-content-between align-items-center">
                <div class="">
                    <p class="text-dark fw-bold fst-italic fs-5" >Examen de la candidature de l'étudiant</p>
                </div>
                <div class="me-4 d-flex align-items-center">
                    <i class="fa-regular fa-circle-user fs-1 me-2"></i>
                    <div class="dropdown">
                        <a href="#" 
                        class="dropdown-toggle text-decoration-none text-dark fw-bold" 
                        role="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                            Yassine Kamouss
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="../services/logout.php" 
                                class="text-decoration-none text-dark p-2">
                                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <section class="px-5 mt-5 d-flex justify-content-center">
                <div class="p-3 rounded shadow bg-white" style="width: 75%;">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-documents-tab" data-bs-toggle="tab" data-bs-target="#nav-documents" type="button" role="tab" aria-controls="nav-documents" aria-selected="true">Documents</button>
                            <button class="nav-link" id="nav-candidatures-tab" data-bs-toggle="tab" data-bs-target="#nav-candidatures" type="button" role="tab" aria-controls="nav-candidatures" aria-selected="false">Candidature</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <!-- document charger -->
                        <div class="tab-pane fade show active p-4" id="nav-documents" role="tabpanel" aria-labelledby="nav-documents-tab">
                            <table class="table table-bordered table-hover mt-4">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Document</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Aperçu / Lien</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>Carte d'identité nationale (CIN)</td>
                                        <!-- <td><span class="badge rounded-pill bg-success">Chargé</span></td> -->
                                        <td>✅Chargé</td>
                                        <td><a href="#" class="text-info" target="_blank">Voir le document</a></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td>Certificat de Baccalauréat</td>
                                        <td>✅Chargé</td>
                                        <td><a href="http://<?= $bac[0]['image_url'] ?>" class="text-info" target="_blank">Voir le document</a></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">3</th>
                                        <td>Diplôme/(Attestation de réussite) BAC+2 et Relevé de notes</td>
                                        <td>✅Chargé</td>
                                        <td><a href="http://<?= $bac_2[0]['image_url'] ?>" class="text-info" target="_blank">Voir le document</a></td>
                                    </tr>
                                    <?php if (!empty($bac_3)): ?>
                                        <tr>
                                            <th scope="row">5</th>
                                            <td>Diplôme/(Attestation de réussite) BAC+3 et Relevé de notes</td>
                                            <td>✅Chargé</td>
                                            <td><a href="http://<?= $bac_3[0]['image_url'] ?>" class="text-info" target="_blank">Voir le document</a></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th scope="row">7</th>
                                        <td>Fichier de pré-inscription</td>
                                        <td>✅Chargé</td>
                                        <td><a href="../../services/imprimer.php?pre-inscription=<?= $filiere['type'] ?>&id=<?= $etudiant_id ?>" class="text-info" target="_blank">Voir le document</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="nav-candidatures" role="tabpanel" aria-labelledby="nav-candidatures-tab">
                            <div class="container">
                                <!-- Titre de la candidature -->
                                <div class="title-section">
                                    <h4>Candidature pour le programme</h4>
                                    <h6>Cycle <?php echo ($filiere['type'] === 'FI') ? "d'Ingénieur" : $filiere['type']; ?> - <?= $filiere['libelle']; ?> </h6>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="action-buttons mt-3 text-center">
                                    <button class="btn btn-success  me-3" onclick="valider(<?= $id_candidature?>)">Valider</button>
                                    <button class="btn btn-danger " onclick="refuser(<?= $id_candidature?>)">Refuser</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/candidature.js"></script>
</body>
</html>
<?php

    session_start();
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    try {
        require_once("../models/connection.php");

        $query = "
            SELECT 
                e.*,
                GROUP_CONCAT(DISTINCT 
                    JSON_OBJECT(
                        'id_diplome', d.id,
                        'type', d.type,
                        'specialite', d.specialite,
                        'etablissement', d.etablissement,
                        'mention', d.mention,
                        'annee_obtention', d.annee_obtention,
                        's1', nd.s1,
                        's2', nd.s2,
                        's3', nd.s3,
                        's4', nd.s4,
                        's5', nl.s5,
                        's6', nl.s6
                    )
                ) as diplomes,
                GROUP_CONCAT(DISTINCT 
                    JSON_OBJECT(
                        'filiere', f.libelle,
                        'ordre', c.ordre
                    )
                ) as filieres
            FROM Etudiant e
            LEFT JOIN diplome d ON e.id = d.id_etu
            LEFT JOIN note_deust nd ON d.id = nd.id_diplome
            LEFT JOIN note_licence nl ON d.id = nl.id_diplome
            LEFT JOIN condidature c ON e.id = c.id_etu
            LEFT JOIN Filieres f ON c.id_fil = f.id
            WHERE e.id = ?
            AND f.type = ?
            GROUP BY e.id
        ";

        $statement = $pdo->prepare($query);
        $statement->execute([$_SESSION['etudiant_id'] , 'MASTER']); // Remplacez $id_etudiant par l'ID souhaité
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Traitement des données
        $etudiant = [
            'nom' => $result['nom'],
            'prenom' => $result['prenom'],
            'cin' => $result['cin'],
            'cne' => $result['cne'],
            'date_naissance' => $result['date_naissance'],
            'lieu_naissance' => $result['lieu_naissance'],
            'phone' => $result['phone'],
            'adresse' => $result['adresse'],
            'sexe' => $result['sex'],
            'email' => $result['email']
        ];

        // Première étape : formatage correct des chaînes JSON
        $diplomesString = '[' . $result['diplomes'] . ']';
        $filieresString = '[' . $result['filieres'] . ']';

        // Maintenant on peut décoder
        $diplomes = json_decode($diplomesString, true);
        $filieres = json_decode($filieresString, true);

        // Séparer les diplômes par type
        $bac = null;
        $deust = null;
        $licence = null;

        foreach ($diplomes as $diplome) {
            $type = strtolower($diplome['type']);  // Normalise le type pour comparer en minuscules
            switch ($type) {
                case 'bac':
                    $bac = $diplome;
                    break;
                case (in_array($type, ['deust', 'deug', 'dut', 'bts', 'deup', 'dts'])):
                    $deust = $diplome;
                    break;
                case (in_array($type, ['lst', 'lp', 'lf'])):
                    $licence = $diplome;
                    break;
            }
        }


        // Trier les filières par ordre
        usort($filieres, function($a, $b) {
            return $a['ordre'] - $b['ordre'];
        });

        // var_dump($deust);
        // die();
        
    } catch (PDOException $e) {
        die("PDO Erreur : " . $e->getMessage());
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mes information</title>
    <link rel="icon" type="image/png" href="../assets/iconFSTT.png">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../vendor/fortawesome/font-awesome/css/all.min.css">
</head>
<body>
    <header class="container d-flex justify-content-between align-items-center mt-4">
        <div>
            <img src="../../assets/fstt_logo.png" alt="Logo de fstt">
        </div>
        <div class="d-flex flex-column">
            <span class="text-center fs-5" >Faculté des Sciences et Techniques - Tanger</span>
            <span class="text-center">pre-inscription en ligne aux licences/master et Fi</span>
            <span class="text-center">A.U : 2025-2026</span>
        </div>
        <div>
            <img src="../../assets/logo-uae.png" alt="Logo de uae">
        </div>
    </header>
    <main class="container" style="margin-top: 70px;">

        <!-- Iformation personnels  -->
        <div class="card mt-5">
            <form action="../controllers/edit.php" method="post">
                <div class="card-header d-flex justify-content-between">
                    <h5>Mes information personelles</h5>
                    <button type="submit" name="edite_info" class="btn btn-sm me-3"><i class="fa-solid fa-pen-to-square"></i> Modifier</button>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-control fw-bold" value="<?= $etudiant['nom'];?>">
                        </div>
                        <div class="col-6">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-control fw-bold" value="<?=$etudiant['prenom']?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="cin">CIN </label>
                            <input type="text" id="cin" name="cin" class="form-control fw-bold" size="8" value="<?=$etudiant['cin']?>">
                        </div>
                        <div class="col-6">
                            <label for="nom">CNE</label>
                            <input type="text" id="cne" name="cne" class="form-control fw-bold" size="10" value="<?=$etudiant['cne']?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="tel">N° Tel</label>
                            <input type="text" id="tel" name="tel" class="form-control fw-bold" size="10" pattern="\d*" value="<?=$etudiant['phone']?>">
                        </div>
                        <div class="col-6">
                            <label for="sex">Sex (H/F)</label>
                            <input type="text" class="form-control fw-bold" name="sex" id="sex" value="<?=$etudiant['sexe']?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="date_naissance">Date de naissenc</label>
                            <input type="date" id="date_naissance" name="date_naissance" class="form-control fw-bold" value="<?=$etudiant['date_naissance']?>">
                        </div>
                        <div class="col-6">
                            <label for="lieu_naissance">Lieu de naissenc</label>
                            <input disabled type="text" id="lieu_naissance" name="lieu_naissance" class="form-control fw-bold" value="<?=$etudiant['lieu_naissance']?>">
                        </div>
                    </div>
                    <div class="row mt-3 mb-4">
                        <div class="col-12">
                            <label for="adresse">Adresse</label>
                            <textarea class="form-control p-3 fw-bold" name="adresse" id="adresse"><?=$etudiant['adresse']?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Information de bac -->
        <div class="card mt-5">
            <form action="../controllers/edit.php" method="post">
                <div class="card-header d-flex justify-content-between">
                    <h5 >Mes informations de BAC</h5>
                    <button type="submit" name="edite_bac" class="btn btn-sm me-3"><i class="fa-solid fa-pen-to-square"></i> Modifier</button>
                </div>
                <div class="card-body">
                    <div class="row mt-4">
                        <div class="col-6">
                            <label for="serie_bac">Série de Bac</label>
                            <input type="text" class="form-control fw-bold" name="serie_bac" id="serie_bac" value="<?= $bac['specialite']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="annee_obtention">Année d'obtention</label>
                            <input type="text" name="annee_obtention" id="annee_obtention" class="form-control fw-bold" value="<?= $bac['annee_obtention']; ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="mension">Mension</label>
                            <input type="text" class="form-control fw-bold" name="mension" id="mension" value="<?= $bac['mention']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="moyenne">Moyenne</label>
                            <input type="text" id="moyenne" name="moyenne" class="form-control fw-bold" value="<?= $bac['moyenne']; ?>">
                        </div>
                    </div>
                    <div class="row mt-3 mb-4">
                        <div class="col-6">
                            <label for="etablis">Etablissement</label>
                            <input type="text" id="etablis" name="etablis" class="form-control fw-bold" value="<?= $bac['etablissement']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="type">Type</label>
                            <input type="text" class="form-control fw-bold" name="type" id="type" value="Public">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- information de deust -->
        <div class="card mt-5">
            <form action="../controllers/edit.php" method="post">
                <div class="card-header d-flex justify-content-between">
                    <h5 >Mes informations de BAC+2</h5>
                    <button type="submit" name="edite_bac_2" class="btn btn-sm me-3"><i class="fa-solid fa-pen-to-square"></i> Modifier</button>
                </div>
                <div class="card-body">
                <div class="row mt-3">
                            <div class="col-6">
                                <label for="bac_2_type">Type de Diplome </label>
                                <input type="text" class="form-control fw-bold" name="bac_2_type" id="bac_2_type" value="<?= $deust['type'] ?>">
                            </div>
                            <div class="col-6">
                                <label for="bac_2_annee_obtention">Année d'obtention </label>
                                <input type="text" name="bac_2_annee_obtention" id="bac_2_annee_obtention" class="form-control fw-bold" value="<?= $deust['annee_obtention'] ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="bac_2_specialite">Spécialité </label>
                                <input type="text" class="form-control fw-bold" name="bac_2_specialite" id="bac_2_specialite" value="<?= $deust['specialite'] ?>">
                            </div>
                            <div class="col-6">
                                <label for="bac_2_etablissement">Etablissement </label>
                                <input type="text" name="bac_2_etablissement" id="bac_2_etablissement" class="form-control fw-bold" value="<?= $deust['etablissement'] ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="bac_2_note_s1">Moyenne S1 </label>
                                <input type="text" name="bac_2_note_s1" id="bac_2_note_s1" class="form-control fw-bold" value="<?= $deust['s1'] ?>">
                            </div>
                            <div class="col-6">
                                <label for="bac_2_note_s2">Moyenne S2 </label>
                                <input type="text" name="bac_2_note_s2" id="bac_2_note_s2" class="form-control fw-bold" value="<?= $deust['s2'] ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="bac_2_note_s3">Moyenne S3 </label>
                                <input type="text" name="bac_2_note_s3" id="bac_2_note_s3" class="form-control fw-bold" value="<?= $deust['s3'] ?>">
                            </div>
                            <div class="col-6">
                                <label for="bac_2_note_s4">Moyenne S4 </label>
                                <input type="text" name="bac_2_note_s4" id="bac_2_note_s4" class="form-control fw-bold" value="<?= $deust['s4'] ?>">
                            </div>
                        </div>
                </div>
            </form>
        </div>
        <!-- information de licence -->
        <?php if(!empty($licence)): ?>
        <div class="card mt-5">
            <form action="../controllers/edit.php" method="post">
                <div class="card-header d-flex justify-content-between">
                    <h5 >Mes informations de Licence</h5>
                    <button type="submit" name="edite_licence" class="btn btn-sm me-3"><i class="fa-solid fa-pen-to-square"></i> Modifier</button>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="bac_3_type">Type de Diplome </label>
                            <input type="text" class="form-control fw-bold" name="bac_3_type" id="bac_3_type" value="<?= $licence['type'] ?>">
                        </div>
                        <div class="col-6">
                            <label for="bac_3_annee_obtention">Année d'obtention </label>
                            <input type="text" name="bac_3_annee_obtention" id="bac_3_annee_obtention" class="form-control fw-bold" value="<?= $licence['annee_obtention'] ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="bac_3_specialite">Spécialité </label>
                            <input type="text" class="form-control fw-bold" name="bac_3_specialite" id="bac_3_specialite" value="<?= $licence['specialite'] ?>">
                        </div>
                        <div class="col-6">
                            <label for="bac_3_etablissement">Etablissement </label>
                            <input type="text" name="bac_3_etablissement" id="bac_3_etablissement" class="form-control fw-bold" value="<?= $licence['etablissement'] ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="bac_3_note_s1">Moyenne S1 </label>
                            <input type="text" name="bac_3_note_s1" id="bac_3_note_s5" class="form-control fw-bold" value="<?= $licence['s5'] ?>">
                        </div>
                        <div class="col-6">
                            <label for="bac_3_note_s2">Moyenne S2 </label>
                            <input type="text" name="bac_3_note_s2" id="bac_3_note_s6" class="form-control fw-bold" value="<?= $licence['s6'] ?>">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </main>

    <div style="height: 100px;"></div>
</body>
</html>
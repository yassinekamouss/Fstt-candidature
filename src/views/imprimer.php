<?php
    session_start() ;
    if(!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"] != true or !isset($_SESSION["etudiant_id"])){
        header("Location: ../../public/index.php");
        exit(0) ;
    }
    if (!$etudiant_id) {
        die("Identifiant étudiant manquant.");
    }
    try {
        require_once("../models/connection.php");

        $query = "
          SELECT 
                JSON_OBJECT(
                    'id', e.id,
                    'nom', e.nom,
                    'prenom', e.prenom,
                    'adresse', e.adresse,
                    'date_naissance', e.date_naissance,
                    'lieu_naissance', e.lieu_naissance,
                    'phone', e.phone,
                    'cin', e.cin,
                    'cne', e.cne,
                    'sexe', e.sex,
                    'email', e.email,
                    'diplomes', (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id_diplome', d.id,
                                'type', d.type,
                                'specialite', d.specialite,
                                'etablissement', d.etablissement,
                                'annee_obtention', d.annee_obtention,
                                'niveau', d.niveau,
                                'note1', d.note1,
                                'note2', d.note2,
                                'note3', d.note3,
                                'note4', d.note4
                            )
                        )
                        FROM diplome d
                        WHERE d.id_etu = e.id
                    ),
                    'filieres', (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'filiere', f.libelle,
                                'ordre', c.ordre
                            )
                        )
                        FROM condidature c
                        JOIN Filieres f ON f.id = c.id_fil
                        WHERE c.id_etu = e.id 
                        AND f.type = ?  
                    )
                ) as student_data
            FROM Etudiant e
            WHERE e.id = ?;
        ";

        $statement = $pdo->prepare($query);
        $statement->execute([$preInscription, $etudiant_id]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Décoder le JSON
        $data = json_decode($result['student_data'], true);

        // Extraire les informations de l'étudiant
        $etudiant = [
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'cin' => $data['cin'],
            'cne' => $data['cne'],
            'date_naissance' => $data['date_naissance'],
            'lieu_naissance' => $data['lieu_naissance'],
            'phone' => $data['phone'],
            'adresse' => $data['adresse'],
            'sexe' => $data['sexe'],
            'email' => $data['email']
        ];

        // Les diplômes sont déjà dans un tableau
        $diplomes = $data['diplomes'] ?? [];

        // Séparer les diplômes par type
        $bac = null;
        $deust = null;
        $licence = null;

        foreach ($diplomes as $diplome) {
            $type = strtolower($diplome['type']);
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

        // Les filières sont déjà dans un tableau
        $filieres = $data['filieres'] ?? [];

        // Trier les filières par ordre
        usort($filieres, function($a, $b) {
            return $a['ordre'] - $b['ordre'];
        });
            
    } catch (PDOException $e) {
        die("PDO Erreur : " . $e->getMessage());
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
?>

<html>
<head>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <style>
        .header-container {
            width: 100%;
            text-align: center;
        }
        .header-item {
            display: inline-block;
            vertical-align: top;
            padding: 7px;
        }
        .logo {
            width: 150px;
        }
        .text {
            font-size: 14px;
            line-height: 1.5;
            text-align: center;
        }
        .fw-bold{
            font-weight : bold ;
        }
    </style>
</head>
<body>
    <header class="header-container" style="width: 100%;text-align: center;">
        <div class="header-item">
            <img src="http://site1.com/PHP/project/FINAL/assets/fstt_logo.png" alt="Logo FSTT" class="logo">
        </div>
        <div class="header-item text">
            <span class="fw-bold">Université Abdelmalek Essaadi</span><br>
            <span class="fw-bold">Faculté des Sciences et Techniques</span><br>
            
        </div>
        <div class="header-item">
            <img src="http://site1.com/PHP/project/FINAL/assets/logo-uae.png" alt="Logo de UAE" class="logo">
        </div>
    </header>
    <div style="text-align:center;">
        <span>fiche de condidature en 1ère année du cycle <?= $preInscription ;?> </span><br>
        <span class="fw-bold">N° Dossier : <?= str_pad($_SESSION['etudiant_id'], 4, '0', STR_PAD_LEFT); ?></span>
    </div>
    <div style="background-color:#E9E9E9;"><h5 style="padding:5px ;">INFORMATIONS PERSONNELLES</h5></div>
    <table style="border-collapse: collapse; width: 100%; text-align: left;margin-top:-15px ;">
        <tr>
            <th style="width: 15%;text-align:left;">Nom:</th>
            <td style="width: 25%;"> <?= htmlspecialchars($etudiant['nom']) ?></td>
            <th style="width: 15%;text-align:left;">Prénom:</th>
            <td style="width: 25%;"><?= htmlspecialchars($etudiant['prenom']) ?></td>
            <td rowspan="4" style="width: 20%; text-align: center; border: 1px solid black; padding: 10px;">
                <img src="logo-uae.png" alt="Photo" style="width: 100%; height: auto;">
            </td>
        </tr>
        <tr>
            <th style="text-align:left;">CNE:</th>
            <td><?= htmlspecialchars($etudiant['cne']) ?></td>
            <th style="text-align:left;">CIN:</th>
            <td><?= htmlspecialchars($etudiant['cin']) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Date & Lieu de Naissance:</th>
            <td><?= htmlspecialchars($etudiant['date_naissance']) ?> à <?= htmlspecialchars($etudiant["lieu_naissance"]) ?></td>
            <th style="text-align:left;">Sexe:</th>
            <td><?= htmlspecialchars($etudiant['sexe']) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Adresse:</th>
            <td colspan="3"><?= htmlspecialchars($etudiant['adresse']) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Tél:</th>
            <td><?= htmlspecialchars($etudiant['phone']) ?></td>
            <th style="text-align:left;">Email:</th>
            <td><?= htmlspecialchars($etudiant['email']) ?></td>
        </tr>
    </table>
    <div style="background-color:#E9E9E9;"><h5 style="padding:5px ;">BACCALAUREAT</h5></div>
    <table style="border-collapse: collapse; width: 100%; text-align: left;margin-top:-15px ;">
    <tr>
        <th style="text-align:left; padding: 5px;">Série:</th>
        <td style="padding: 5px;"><?= htmlspecialchars($bac['specialite']) ?></td>
        <th style="text-align:left; padding: 5px;">Année d'obtention:</th>
        <td style="padding: 5px;"><?= htmlspecialchars($bac['annee_obtention']) ?></td>
        <th style="text-align:left; padding: 5px;">Mention:</th>
        <td style="padding: 5px;"><?= htmlspecialchars($bac['note1']) ?></td>
    </tr>
    </table>    
    <div style="background-color:#E9E9E9;"><h5 style="padding:5px ;">DEUST OU EQUIVALENT</h5></div>
    <table style="border-collapse: collapse; width: 100%; text-align: left;margin-top:-15px ;">
        <tr>
            <th style="text-align:left; padding: 5px;">Diplome:</th>
            <td><?= strtoupper($deust['type']) ?></td>
            <th style="text-align:left; padding: 5px;">Etablissement:</th>
            <td><?= strtoupper($deust['etablissement']) ?></td>
        </tr>
    </table>
    <table border="1" style="border-collapse:collapse;width:100%;">
        <tr style="background-color:#8FC4FF;">
            <th>Semestre</th>
            <th>Etablissement</th>
            <th>Filière</th>
            <th>Moyenne</th>
            <th>A.Validation</th>
        </tr>
        <tr>
            <td>S1</td>
            <td><?= htmlspecialchars($deust['etablissement']) ?></td>
            <td><?= htmlspecialchars($deust['specialite']) ?></td>
            <td><?= htmlspecialchars($deust['note1']) ?></td>
            <td><?= htmlspecialchars($deust['annee_obtention']) ?></td>
        </tr>
        <tr>
            <td>S2</td>
            <td><?= htmlspecialchars($deust['etablissement']) ?></td>
            <td><?= htmlspecialchars($deust['specialite']) ?></td>
            <td><?= htmlspecialchars($deust['note2']) ?></td>
            <td><?= htmlspecialchars($deust['annee_obtention']) ?></td>
        </tr>
        <tr>
            <td>S3</td>
            <td><?= htmlspecialchars($deust['etablissement']) ?></td>
            <td><?= htmlspecialchars($deust['specialite']) ?></td>
            <td><?= htmlspecialchars($deust['note3']) ?></td>
            <td><?= htmlspecialchars($deust['annee_obtention']) ?></td>
        </tr>
        <tr>
            <td>S4</td>
            <td><?= htmlspecialchars($deust['etablissement']) ?></td>
            <td><?= htmlspecialchars($deust['specialite']) ?></td>
            <td><?= htmlspecialchars($deust['note4']) ?></td>
            <td><?= htmlspecialchars($deust['annee_obtention']) ?></td>
        </tr>
    </table>
    <?php if($preInscription === 'MASTER') :?>
    <div style="background-color:#E9E9E9;"><h5 style="padding:5px ;">LICENCE OU EQUIVALENT</h5></div>
    <table border="1" style="border-collapse:collapse;width:100%;">
        <tr style="background-color:#8FC4FF;">
            <th>Semestre</th>
            <th>Etablissement</th>
            <th>Filière</th>
            <th>Moyenne</th>
            <th>A.Validation</th>
        </tr>
        <tr>
            <td>S5</td>
            <td><?= htmlspecialchars($licence['etablissement']) ?></td>
            <td><?= htmlspecialchars($licence['specialite']) ?></td>
            <td><?= htmlspecialchars($licence['note1']) ?></td>
            <td><?= htmlspecialchars($licence['annee_obtention']) ?></td>
        </tr>
        <tr>
            <td>S6</td>
            <td><?= htmlspecialchars($licence['etablissement']) ?></td>
            <td><?= htmlspecialchars($licence['specialite']) ?></td>
            <td><?= htmlspecialchars($licence['note2']) ?></td>
            <td><?= htmlspecialchars($licence['annee_obtention']) ?></td>
        </tr>
    </table> 
    <?php endif; ?>
    <div style="background-color:#E9E9E9;"><h5 style="padding:5px ;">CHOIX DE FILIèRE</h5></div>
    <ul>
        <?php foreach ($filieres as $filiere): ?>
            <li><?= htmlspecialchars($filiere['filiere']) ?> (Choix <?= htmlspecialchars($filiere['ordre']) ?>)</li>
        <?php endforeach; ?>
    </ul>
    <span style="text-align:center;">A ............................... le ...................... Signature:</span>
</body>
</html>
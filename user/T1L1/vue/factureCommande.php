<?php
require_once '../model/auth.php';
include '../model/function.php';

// Récupération des paramètres depuis la requête GET
$id_commande = isset($_GET['id_commande']) ? $_GET['id_commande'] : null;

// Appel de la fonction getCommande pour obtenir les données de la commande
$commande = getCommande($id_commande);

// Vérification si la commande existe
if ($commande) {
    $nom_article = $commande['nom_article'];
    $nom_fournisseur = $commande['nom'];
    $prenom_fournisseur = $commande['prenom'];
    $quantite = $commande['quantite'];
    $prix_unitaire = $commande['prix_unitaire'];
    $date_commande = $commande['date_commande'];
    $adresse_fournisseur = $commande['adresse'];
    $telephone_fournisseur = $commande['telephone'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Les mêmes balises head que dans factureVente.php -->
    <meta charset="UTF-8">
    <title>Facture Lovely Indian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css">
    <style>
        body {
            background: #EEE;
        }

        .invoice {
            background: #fff;
            width: 970px !important;
            margin: 50px auto;
        }

        .invoice .invoice-header {
            padding: 25px 25px 15px;
        }

        .invoice .invoice-header h1 {
            margin: 0;
        }

        .invoice .invoice-header .media .media-body {
            font-size: 0.9em;
            margin: 0;
        }

        .invoice .invoice-body {
            border-radius: 10px;
            padding: 25px;
            background: #FFF;
        }

        .invoice .invoice-footer {
            padding: 15px;
            font-size: 0.9em;
            text-align: center;
            color: #999;
        }

        .logo {
            max-height: 70px;
            border-radius: 10px;
        }

        .dl-horizontal {
            margin: 0;
        }

        .dl-horizontal dt {
            float: left;
            width: 80px;
            overflow: hidden;
            clear: left;
            text-align: right;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dl-horizontal dd {
            margin-left: 90px;
        }

        .rowamount {
            padding-top: 15px !important;
        }

        .rowtotal {
            font-size: 1.3em;
        }

        .colfix {
            width: 12%;
        }

        .mono {
            font-family: monospace;
        }

        .print-button {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #fff;
            border: none;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .print-button:hover {
            background: #559;
            box-shadow: 0 0 30px 10px #333;
        }

        .print-button i {
            transition: all 2.5s;
            font-size: 70px;
            color: #777;
            animation: animate 2s infinite;
        }
        @keyframes animate {
            50% {color: #0ff;}
        }

        /* Nouveau style pour masquer le bouton d'impression lors de l'impression */
        @media print {
            .print-button {
                display: none;
            }
            .return-button {
                display: none;
            }
        }

        /* Style pour le bouton de retour */
        .return-button {
            position: fixed;
            bottom: 70px;
            right: 30px;
            background-color: #fff;
            border: none;
            width: 120px;
            height: 40px;
            border-radius: 5px;
            padding: 8px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: all 2.5s;
            animation: animate2 4s infinite;
        }

        @keyframes animate2 {
            50% {color: #0ff; background-color: #113}
        }

        .return-button i {
            font-size: 20px;
            color: #777;
        }
    </style>
</head>
<body>

    <!-- Les mêmes boutons de print et de retour que dans factureVente.php -->
    <button class="print-button" onclick="printInvoice()">
        <i class='bx bx-printer'></i>
    </button>

    <a class="return-button" href="./vente.php">
        <i class='bx bx-arrow-back'></i> Retour
    </a>


    <div class="container invoice">
        <div class="invoice-header">
            <h1>Facture <small>Lovely Indian</small></h1>
            <h4 class="text-muted">NO: #C2024<?= $id_commande ?> | Date: <?= $date_commande ?></h4>
        </div>

        <div class="invoice-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Informations fournisseur</h3>
                </div>
                <div class="panel-body">
                    <ul class="custom-list">
                        <li><strong style="margin-right: 2%;">Nom du fournisseur:</strong><?= $nom_fournisseur ?></li>
                        <li><strong style="margin-right: 2%;">Prénom du fournisseur:</strong><?= $prenom_fournisseur ?></li>
                        <li><strong style="margin-right: 2%;">Numéro de téléphone:</strong><?= $telephone_fournisseur ?></li>
                        <li><strong style="margin-right: 2%;">Adresse fournisseur:</strong><?= $adresse_fournisseur ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tableau des détails de la commande -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Détails de la Commande</h3>
            </div>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th>Prix Total</th>
                        <th>Methode paiement</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $quantite ?></td>
                        <td><?= $prix_unitaire ?></td>
                        <td><?= $quantite * $prix_unitaire ?></td>
                        <td>
                            <select name="" id="" style="border: none; padding: 5px 7px;">
                                <option value="">Espèces</option>
                                <option value="">Chèque</option>
                                <option value="">MTN MoMo</option>
                                <option value="">Orange Money</option>
                            </select>
                        </td>
                    </tr>
                    <!-- Ajoutez d'autres lignes si nécessaire -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Les mêmes scripts que dans factureVente.php -->
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script>
        function printInvoice() {
            // Masquer le bouton d'impression et le bouton de retour avant l'impression
            document.querySelector('.print-button').style.display = 'none';
            document.querySelector('.return-button').style.display = 'none';

            // Appeler la fonction d'impression native du navigateur
            window.print();

            // Rétablir l'affichage du bouton d'impression et du bouton de retour après l'impression
            document.querySelector('.print-button').style.display = 'block';
            document.querySelector('.return-button').style.display = 'block';
        }
    </script>

</body>
</html>

<?php
} else {
    echo "Commande non trouvée.";
}
?>

<?php
require_once 'auth.php';
include 'connexion.php';

// Vérification de la présence des paramètres dans l'URL
if (!empty($_GET['id_vente']) && !empty($_GET['id_article']) && !empty($_GET['quantite'])) {
    
    // Revenir en arrière dans la quantité de l'article vendu
    $sqlRetourStock = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
    $reqRetourStock = $connexion->prepare($sqlRetourStock);

    // Exécution de la requête de mise à jour
    $resultRetourStock = $reqRetourStock->execute(array($_GET['quantite'], $_GET['id_article']));

    // Vérification des erreurs éventuelles
    if (!$resultRetourStock) {
        print_r($reqRetourStock->errorInfo());
        die("Erreur lors de la mise à jour de la quantité de l'article.");
    }

    // Supprimer la vente de la table des ventes
    $sqlSupprimerVente = "DELETE FROM vente WHERE id = ?";
    $reqSupprimerVente = $connexion->prepare($sqlSupprimerVente);

    // Exécution de la requête de suppression
    $resultSupprimerVente = $reqSupprimerVente->execute(array($_GET['id_vente']));

    // Vérification des erreurs éventuelles
    if (!$resultSupprimerVente) {
        print_r($reqSupprimerVente->errorInfo());
        die("Erreur lors de la suppression de la vente.");
    }
}

// Redirection vers la page de gestion des ventes après le traitement
header('Location: ../vue/vente.php');
?>

<?php
require_once 'auth.php';
include 'connexion.php';

// Vérification de la présence des paramètres dans l'URL
if (!empty($_GET['idCommande']) && !empty($_GET['idArticle']) && !empty($_GET['quantite'])) {
    
    // Revenir en arrière dans la quantité de l'article commandé
    $sqlRetourStock = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
    $reqRetourStock = $connexion->prepare($sqlRetourStock);

    // Exécution de la requête de mise à jour
    $resultRetourStock = $reqRetourStock->execute(array($_GET['quantite'], $_GET['idArticle']));

    // Vérification des erreurs éventuelles
    if (!$resultRetourStock) {
        print_r($reqRetourStock->errorInfo());
    }

    // Supprimer la commande de la table des commandes
    $sqlSupprimerCommande = "DELETE FROM commande WHERE id = ?";
    $reqSupprimerCommande = $connexion->prepare($sqlSupprimerCommande);

    // Exécution de la requête de suppression
    $resultSupprimerCommande = $reqSupprimerCommande->execute(array($_GET['idCommande']));

    // Vérification des erreurs éventuelles
    if (!$resultSupprimerCommande) {
        print_r($reqSupprimerCommande->errorInfo());
    }
}

// Redirection vers la page de gestion des commandes après le traitement
header('Location: ../vue/commande.php');
?>

<?php
require_once 'auth.php';
include 'connexion.php';
include_once 'fonction.php'; // Correction du nom du fichier inclus

// Vérification de la présence des données nécessaires dans le formulaire
if (
    !empty($_POST['id_article']) 
    && !empty($_POST['id_fournisseur']) 
    && !empty($_POST['quantite']) 
    && !empty($_POST['prix']) 
) {
    // Préparation de la requête d'insertion dans la table "commande"
    $sqlCommande = "INSERT INTO commande(id_article, id_fournisseur, quantite, prix) VALUES (?, ?, ?, ?)";
    $requeteCommande = $connexion->prepare($sqlCommande);

    // Exécution de la requête d'insertion avec les données du formulaire
    $requeteCommande->execute(array(
        $_POST['id_article'],
        $_POST['id_fournisseur'],
        $_POST['quantite'],
        $_POST['prix']
    ));

    // Vérification si l'insertion a réussi
    if ($requeteCommande->rowCount() != 0) {

        // Mise à jour de la quantité dans la table "article"
        $sqlUpdateArticle = "UPDATE article SET quantite=quantite+? WHERE id=?";
        $requeteUpdateArticle = $connexion->prepare($sqlUpdateArticle);

        // Exécution de la requête de mise à jour avec les données du formulaire
        $requeteUpdateArticle->execute(array(
            $_POST['quantite'],
            $_POST['id_article']
        ));

        // Message de succès en session
        $_SESSION['message']['texte'] = "Commande effectuée avec succès!"; // Correction du mot "text" en "texte"
        $_SESSION['message']['type'] = "succes"; // Correction du mot "success" en "succes"
                
    } else {
        // Message d'échec en session
        $_SESSION['message']['texte'] = "Impossible d'effectuer cette commande :("; // Correction du mot "text" en "texte"
        $_SESSION['message']['type'] = "danger";
    }
} else {
    // Message d'erreur en session
    $_SESSION['message']['texte'] = "Une erreur s'est produite lors de cette commande!"; // Correction du mot "text" en "texte"
    $_SESSION['message']['type'] = "danger";
}

// Redirection vers la page de vue après le traitement
header('Location: ../vue/commande.php');

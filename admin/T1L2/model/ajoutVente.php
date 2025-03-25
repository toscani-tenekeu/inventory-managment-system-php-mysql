<?php
require_once 'auth.php';
include 'connexion.php';
include_once 'function.php';

if (
    !empty($_POST['id_article']) 
    && !empty($_POST['id_client']) 
    && !empty($_POST['quantite']) 
    && !empty($_POST['prix']) 
) {
    // Obtention des informations sur l'article
    $article = getArticle($_POST['id_article']);

    // Vérification si l'article existe et est un tableau
    if (!empty($article) && is_array($article)) {
        // Vérification de la disponibilité en stock
        if ($_POST['quantite'] > $article['quantite']) {
            $_SESSION['message']['text'] = "Not enough in stock for this sale!";
            $_SESSION['message']['type'] = "warning";
        } else {
            // Préparation de la requête d'insertion dans la table "vente"
            $sqlVente = "INSERT INTO vente(id_article, id_client, quantite, prix) VALUES (?, ?, ?, ?)";
            $reqVente = $connexion->prepare($sqlVente);

            // Exécution de la requête d'insertion avec les données du formulaire
            $reqVente->execute(array(
                $_POST['id_article'],
                $_POST['id_client'],
                $_POST['quantite'],
                $_POST['prix']
            ));

            // Vérification si l'insertion a réussi
            if ($reqVente->rowCount() != 0) {
                // Mise à jour de la quantité dans la table "article"
                $sqlUpdateArticle = "UPDATE article SET quantite=quantite-? WHERE id=?";
                $reqUpdateArticle = $connexion->prepare($sqlUpdateArticle);

                // Exécution de la requête de mise à jour avec les données du formulaire
                $reqUpdateArticle->execute(array(
                    $_POST['quantite'],
                    $_POST['id_article']
                ));

                $_SESSION['message']['text'] = "Sale completed successfully!";
                $_SESSION['message']['type'] = "success";
            } else {
                $_SESSION['message']['text'] = "Unable to process this sale :(";
                $_SESSION['message']['type'] = "danger";
            }
        }
    } else {
        $_SESSION['message']['text'] = "Article not found!";
        $_SESSION['message']['type'] = "danger";
    }
} else {
    $_SESSION['message']['text'] = "An error occurred during this sale!";
    $_SESSION['message']['type'] = "danger";
}

// Redirection vers la page de vue après le traitement
header('Location: ../vue/vente.php');
?>
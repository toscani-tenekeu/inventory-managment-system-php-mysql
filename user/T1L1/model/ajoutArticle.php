<?php
require_once './auth.php';
include 'connexion.php';


// Définir la requête SQL
$sql = "";

if (
    !empty($_POST['nom_article'])
    && !empty($_POST['categorie'])
    && !empty($_POST['prix_unitaire'])
    && !empty($_POST['date_fabrication'])
    && !empty($_POST['date_expiration'])
) {
    // Requête d'insertion dans la base de données
    $sql = "INSERT INTO article(nom_article, categorie, quantite, prix_unitaire, date_fabrication, date_expiration) VALUES (?, ?, 0, ?, ?, ?)";
    $req = $connexion->prepare($sql);

    // Exécution de la requête avec les paramètres
    $req->execute(array(
        $_POST['nom_article'],
        $_POST['categorie'], // Utilisez la colonne nom_categorie ici
        $_POST['prix_unitaire'],
        $_POST['date_fabrication'],
        $_POST['date_expiration']
    ));

    // Vérification de l'ajout de l'article
    if ($req->rowCount() != 0) {
        $_SESSION['message']['text'] = "Article ajouté avec succès!";
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['text'] = "Une erreur s'est produite lors de l'ajout de l'article :(";
        $_SESSION['message']['type'] = "danger";
    }
} else {
    $_SESSION['message']['text'] = "Une information obligatoire non renseignée";
    $_SESSION['message']['type'] = "danger";
}

// Redirection vers la page des articles
header('Location: ../vue/article.php');
?>

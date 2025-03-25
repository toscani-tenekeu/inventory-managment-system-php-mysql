<?php
require_once 'auth.php';
include 'connexion.php';

if (
    !empty($_POST['nom_categorie']) 
    && !empty($_POST['description_categorie'])
) {
    // Requête d'insertion dans la table "categorie"
    $sql = "INSERT INTO categorie (nom_categorie, description_categorie) VALUES (?, ?)";
    $req = $connexion->prepare($sql);

    // Exécution de la requête avec les paramètres
    $req->execute(array(
        $_POST['nom_categorie'],
        $_POST['description_categorie']
    ));

    // Vérification de l'ajout de la catégorie
    if ($req->rowCount() != 0) {
        $_SESSION['message']['text'] = "Catégorie ajoutée avec succès!";
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['text'] = "Une erreur s'est produite lors de l'ajout de la catégorie :(";
        $_SESSION['message']['type'] = "warning";
    }
} else {
    $_SESSION['message']['text'] = "Une information obligatoire non renseignée";
    $_SESSION['message']['type'] = "danger";
}

// Redirection vers la page des catégories
header('Location: ../vue/categorie.php');
?>

<?php
require_once 'auth.php';
include 'connexion.php';

// Utilisation de clés associatives pour accéder aux valeurs du tableau $_POST
if (
    !empty($_POST['nom']) 
    && !empty($_POST['prenom']) 
    && !empty($_POST['telephone']) 
    && !empty($_POST['adresse']) 
) {
    // Préparation de la requête d'insertion dans la table "fournisseur"
    $sql = "INSERT INTO fournisseur(nom, prenom, telephone, adresse) VALUES (:nom, :prenom, :telephone, :adresse)";
    $requete = $connexion->prepare($sql);

    // Exécution de la requête d'insertion avec des paramètres nommés
    $requete->execute(array(
        ':nom' => $_POST['nom'],
        ':prenom' => $_POST['prenom'],
        ':telephone' => $_POST['telephone'],
        ':adresse' => $_POST['adresse']
    ));

   // Check if the insertion was successful
    if ($requete->rowCount() != 0) {
        $_SESSION['message']['text'] = "Supplier added successfully!";
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['text'] = "An error occurred while adding the supplier :(";
        $_SESSION['message']['type'] = "warning";
    } 
    } else {
        $_SESSION['message']['text'] = "A required piece of information is missing";
        $_SESSION['message']['type'] = "danger";
    }


// Redirection vers la page de vue après le traitement
header('Location: ../vue/fournisseur.php');

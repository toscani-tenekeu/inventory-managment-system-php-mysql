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

    // Vérification si l'insertion a réussi
    if ($requete->rowCount() != 0) {
        $_SESSION['message']['texte'] = "Fournisseur ajouté avec succès!"; // Correction du mot "text" en "texte"
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['texte'] = "Une erreur s'est produite lors de l'ajout du fournisseur :("; // Correction du mot "text" en "texte"
        $_SESSION['message']['type'] = "warning";
    }
} else {
    $_SESSION['message']['texte'] = "Une information obligatoire non renseignée"; // Correction du mot "text" en "texte"
    $_SESSION['message']['type'] = "danger";
}

// Redirection vers la page de vue après le traitement
header('Location: ../vue/fournisseur.php');

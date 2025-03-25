<?php
require_once 'auth.php';
include 'connexion.php';

if (
    !empty($_POST['nom']) 
    && !empty($_POST['prenom']) 
    && !empty($_POST['telephone']) 
    && !empty($_POST['adresse']) 
) {
    // Requête d'insertion dans la table "client"
    $sql = "INSERT INTO client(nom, prenom, telephone, adresse) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);

    // Exécution de la requête avec les paramètres
    $req->execute(array(
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['adresse']
    ));

    // Vérification de l'ajout du client
    if ($req->rowCount() != 0) {
        $_SESSION['message']['text'] = "Client ajouté avec succès!";
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['text'] = "Une erreur s'est produite lors de l'ajout du client :(";
        $_SESSION['message']['type'] = "danger";
    }
} else {
    $_SESSION['message']['text'] = "Une information obligatoire non renseignée";
    $_SESSION['message']['type'] = "danger";
}

// Redirection vers la page des clients
header('Location: ../vue/client.php');
?>

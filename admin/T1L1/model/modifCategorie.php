<?php
    require_once 'auth.php';
    include 'connexion.php';

    // Vérification si les champs obligatoires sont renseignés
    if (
        !empty($_POST['nom_categorie']) 
        && !empty($_POST['description_categorie']) 
        && !empty($_POST['id'])
    ) {
        // Requête SQL pour mettre à jour la catégorie
        $sql = "UPDATE categorie SET nom_categorie = ?, description_categorie = ? WHERE id = ? ";
        $req = $connexion->prepare($sql);

        // Exécution de la requête avec les valeurs fournies
        $req->execute(array(
            $_POST['nom_categorie'],
            $_POST['description_categorie'],
            $_POST['id']
        ));

        // Vérification si la mise à jour a été effectuée avec succès
        if ($req->rowCount() != 0) {
            // Message de succès en cas de modification réussie
            $_SESSION['message']['text'] = "Catégorie modifiée avec succès !";
            $_SESSION['message']['type'] = "success";
        } else {
            // Message d'avertissement si aucune modification n'a été effectuée
            $_SESSION['message']['text'] = "Aucune modification effectuée. Vérifiez que les données sont différentes.";
            $_SESSION['message']['type'] = "warning";
        }
    } else {
        // Message d'erreur en cas d'information obligatoire non renseignée
        $_SESSION['message']['text'] = "Certaines informations obligatoires ne sont pas renseignées.";
        $_SESSION['message']['type'] = "danger";
    }

    // Redirection vers la page de gestion des catégories
    header('Location: ../vue/categorie.php');
?>

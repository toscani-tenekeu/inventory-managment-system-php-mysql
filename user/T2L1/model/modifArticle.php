<?php
    require_once 'auth.php';
    include 'connexion.php';

    // Vérification si les champs obligatoires sont renseignés
    if (
        !empty($_POST['nom_article']) 
        && !empty($_POST['categorie']) 
        && !empty($_POST['prix_unitaire']) 
        && !empty($_POST['date_fabrication'])
        && !empty($_POST['date_expiration'])
        && !empty($_POST['id'])
    ) {
        // Requête SQL pour mettre à jour l'article avec une quantité fixée à 0
        $sql = "UPDATE article SET nom_article = ?, categorie = ?, quantite = 0, prix_unitaire = ?, date_fabrication = ?, date_expiration = ? WHERE id = ? ";
        $req = $connexion->prepare($sql);

        // Exécution de la requête avec les valeurs fournies
        $req->execute(array(
            $_POST['nom_article'],
            $_POST['categorie'],
            $_POST['prix_unitaire'],
            $_POST['date_fabrication'],
            $_POST['date_expiration'],
            $_POST['id']
        ));

        // Vérification si la mise à jour a été effectuée avec succès
        if ($req->rowCount() != 0) {
            // Message de succès en cas de modification réussie
            $_SESSION['message']['text'] = "Article modifié avec succès !";
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

    // Redirection vers la page de gestion des articles
    header('Location: ../vue/article.php');
?>

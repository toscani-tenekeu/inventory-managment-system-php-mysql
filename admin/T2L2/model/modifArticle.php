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
            // Success message in case of successful modification
            $_SESSION['message']['text'] = "Article modified successfully!";
            $_SESSION['message']['type'] = "success";
        } else {
            // Warning message if no modification was made
            $_SESSION['message']['text'] = "No modification made. Make sure the data is different.";
            $_SESSION['message']['type'] = "warning";
        } 
        } else {
            // Error message in case of missing required information
            $_SESSION['message']['text'] = "Some required information is not provided.";
            $_SESSION['message']['type'] = "danger";
        }
        

    // Redirection vers la page de gestion des articles
    header('Location: ../vue/article.php');
?>

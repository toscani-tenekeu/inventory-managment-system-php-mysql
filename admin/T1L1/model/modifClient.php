<?php
    require_once 'auth.php';
    include 'connexion.php';

    // Vérification si les champs obligatoires sont renseignés
    if (
        !empty($_POST['nom']) 
        && !empty($_POST['prenom']) 
        && !empty($_POST['telephone']) 
        && !empty($_POST['adresse']) 
        && !empty($_POST['id'])
    ) {
        // Requête SQL pour mettre à jour le client
        $sql = "UPDATE client SET nom = ?, prenom = ?, telephone = ?, adresse = ? WHERE id = ? ";
        $req = $connexion->prepare($sql);

        // Exécution de la requête avec les valeurs fournies
        $req->execute(array(
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['telephone'],
            $_POST['adresse'],
            $_POST['id']
        ));

        // Vérification si la mise à jour a été effectuée avec succès
        if ($req->rowCount() != 0) {
            // Message de succès en cas de modification réussie
            $_SESSION['message']['text'] = "Client modifié avec succès !";
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

    // Redirection vers la page de gestion des clients
    header('Location: ../vue/client.php');
?>

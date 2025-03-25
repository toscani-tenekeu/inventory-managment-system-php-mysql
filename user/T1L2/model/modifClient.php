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

   // Check if the update was successful
if ($req->rowCount() != 0) {
    // Success message in case of successful modification
    $_SESSION['message']['text'] = "Client modified successfully!";
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


    // Redirection vers la page de gestion des clients
    header('Location: ../vue/client.php');
?>

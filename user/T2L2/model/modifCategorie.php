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

     // Check if the update was successful
if ($req->rowCount() != 0) {
    // Success message in case of successful modification
    $_SESSION['message']['text'] = "Category modified successfully!";
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


    // Redirection vers la page de gestion des catégories
    header('Location: ../vue/categorie.php');
?>

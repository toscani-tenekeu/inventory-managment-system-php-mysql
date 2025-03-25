<?php
require_once 'auth.php';
include 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifiez si l'ID de l'utilisateur et les autres champs nécessaires sont présents dans le formulaire
    if (isset($_POST['userId'], $_POST['prenom'], $_POST['email'])) {
        $userId = $_POST['userId'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];

        // Requête de mise à jour de l'utilisateur
        $sqlUpdateUtilisateur = "UPDATE utilisateur SET prenom = :prenom, email = :email WHERE id = :id";
        $queryUpdateUtilisateur = $connexion->prepare($sqlUpdateUtilisateur);
        $queryUpdateUtilisateur->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $queryUpdateUtilisateur->bindParam(':email', $email, PDO::PARAM_STR);
        $queryUpdateUtilisateur->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($queryUpdateUtilisateur->execute()) {
            // Affichez une alerte JavaScript pour la confirmation
            echo '<script>';
            echo 'if (confirm("Modification successful. Click OK to return to the list of users.")){';

            echo 'window.location.href = "../vue/utilisateurs.php";';
            echo '}';
            echo '</script>';
        } else {
            // Gestion des erreurs de mise à jour
            echo "Error updating the user.
            ";
        }
    } else {
        // Données du formulaire manquantes
        echo "Missing form data.";
    }
} else {
    // Redirigez si la requête n'est pas de type POST
    header('Location: ./utilisateurs.php');
    exit();
}
?>

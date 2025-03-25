<?php
require_once 'auth.php';
include 'connexion.php';

// Vérifiez si un ID d'utilisateur est transmis via GET
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Requête pour supprimer l'utilisateur
    $sqlSupprimerUtilisateur = "DELETE FROM utilisateur WHERE id = :id";
    $querySupprimerUtilisateur = $connexion->prepare($sqlSupprimerUtilisateur);
    $querySupprimerUtilisateur->bindParam(':id', $userId, PDO::PARAM_INT);

    if ($queryDeleteUser->execute()) {
        // Redirect to the list of users after deletion
        echo '<script>';
        echo 'if(confirm("Deletion successful. Click OK to return to the list of users.")){';
        echo 'window.location.href = "../vue/utilisateurs.php";';
        echo '}';
        echo '</script>';
        exit();
    } else {
        // Handling deletion errors
        echo "Error deleting the user.";
    }
    
} else {
    // Redirigez si l'ID n'est pas présent
    header('Location: ./utilisateurs.php');
    exit();
}
?>

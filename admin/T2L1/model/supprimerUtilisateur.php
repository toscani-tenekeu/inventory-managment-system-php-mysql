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

    if ($querySupprimerUtilisateur->execute()) {
        // Redirigez vers la liste des utilisateurs après la suppression
        echo '<script>';
        echo 'if(confirm("Suppression réussie. Cliquez sur OK pour revenir à la liste des utilisateurs.")){';
        echo 'window.location.href = "../vue/utilisateurs.php";';
        echo '}';
        echo '</script>';
        exit();
    } else {
        // Gestion des erreurs de suppression
        echo "Erreur lors de la suppression de l'utilisateur.";
    }
} else {
    // Redirigez si l'ID n'est pas présent
    header('Location: ./utilisateurs.php');
    exit();
}
?>

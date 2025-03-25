<?php
require_once 'auth.php';
include 'connexion.php';

if (isset($_GET['id_vente'])) {
    $idVente = $_GET['id_vente'];

    try {
        // Requête de suppression
        $sql = "DELETE FROM vente WHERE id = :idVente";
        $req = $connexion->prepare($sql);

        // Liaison du paramètre
        $req->bindParam(':idVente', $idVente, PDO::PARAM_INT);

        // Exécution de la requête
        $req->execute();

        // Notification de suppression réussie (vous pouvez personnaliser cette notification)
        echo '<script>alert("Vente supprimée avec succès!");</script>';
    } catch (PDOException $e) {
        // En cas d'erreur, affichez un message d'erreur (vous pouvez personnaliser cette notification)
        echo '<script>alert("Erreur lors de la suppression: '.$e->getMessage().'");</script>';
    }
} else {
    // Si l'ID de Vente n'est pas spécifié, affichez un avertissement (vous pouvez personnaliser cette notification)
    echo '<script>alert("ID de vente non spécifié!");</script>';
}

// Redirection vers la page de Vente
header('Location: ../vue/vente.php');
?>

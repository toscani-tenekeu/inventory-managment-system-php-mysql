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

    // Successful deletion notification (you can customize this notification)
echo '<script>alert("Sale deleted successfully!");</script>';
} catch (PDOException $e) {
    // In case of an error, display an error message (you can customize this notification)
    echo '<script>alert("Error during deletion: '.$e->getMessage().'");</script>';
}
} else {
    // If the Sale ID is not specified, display a warning (you can customize this notification)
    echo '<script>alert("Sale ID not specified!");</script>';
}


// Redirection vers la page de Vente
header('Location: ../vue/vente.php');
?>

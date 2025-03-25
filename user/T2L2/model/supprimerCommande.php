<?php
    require_once 'auth.php';
    include 'connexion.php';

    if (isset($_GET['idCommande'])) {
        $idCommande = $_GET['idCommande'];

        try {
            // Requête de suppression
            $sql = "DELETE FROM commande WHERE id = :idCommande";
            $req = $connexion->prepare($sql);
            
            // Liaison du paramètre
            $req->bindParam(':idCommande', $idCommande, PDO::PARAM_INT);

            // Exécution de la requête
            $req->execute();

        // Successful deletion notification (you can customize this notification)
echo '<script>alert("Order deleted successfully!");</script>';
} catch (PDOException $e) {
    // In case of an error, display an error message (you can customize this notification)
    echo '<script>alert("Error during deletion: ' . $e->getMessage() . '");</script>';
}
} else {
    // If the Order ID is not specified, display a warning (you can customize this notification)
    echo '<script>alert("Order ID not specified!");</script>';
}


    // Redirection vers la page de Commande
    header('Location: ../vue/Commande.php');


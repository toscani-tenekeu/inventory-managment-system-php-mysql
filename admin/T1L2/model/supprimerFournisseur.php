<?php
    require_once 'auth.php';
    include 'connexion.php';

    if (isset($_GET['idFournisseur'])) {
        $idFournisseur = $_GET['idFournisseur'];

        try {
            // Requête de suppression
            $sql = "DELETE FROM fournisseur WHERE id = :idFournisseur";
            $req = $connexion->prepare($sql);
            
            // Liaison du paramètre
            $req->bindParam(':idFournisseur', $idFournisseur, PDO::PARAM_INT);

            // Exécution de la requête
            $req->execute();

      // Successful deletion notification (you can customize this notification)
echo '<script>alert("Supplier deleted successfully!");</script>';
} catch (PDOException $e) {
    // In case of an error, display an error message (you can customize this notification)
    echo '<script>alert("Error during deletion: ' . $e->getMessage() . '");</script>';
}
} else {
    // If the Supplier ID is not specified, display a warning (you can customize this notification)
    echo '<script>alert("Supplier ID not specified!");</script>';
}

    // Redirection vers la page de Fournisseur
    header('Location: ../vue/fournisseur.php');


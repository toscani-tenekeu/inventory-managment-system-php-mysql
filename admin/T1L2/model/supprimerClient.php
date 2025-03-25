<?php
    require_once 'auth.php';
    include 'connexion.php';

    if (isset($_GET['idClient'])) {
        $idClient = $_GET['idClient'];

        try {
            // Requête de suppression
            $sql = "DELETE FROM client WHERE id = :idClient";
            $req = $connexion->prepare($sql);
            
            // Liaison du paramètre
            $req->bindParam(':idClient', $idClient, PDO::PARAM_INT);

            // Exécution de la requête
            $req->execute();

       // Successful deletion notification (you can customize this notification)
echo '<script>alert("Client deleted successfully!");</script>';
} catch (PDOException $e) {
    // In case of an error, display an error message (you can customize this notification)
    echo '<script>alert("Error during deletion: ' . $e->getMessage() . '");</script>';
}
} else {
    // If the Client ID is not specified, display a warning (you can customize this notification)
    echo '<script>alert("Client ID not specified!");</script>';
}


    // Redirection vers la page de Client
    header('Location: ../vue/client.php');


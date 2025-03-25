<?php
    require_once 'auth.php';
    include 'connexion.php';

    if (isset($_GET['idCategorie'])) {
        $idCategorie = $_GET['idCategorie'];

        try {
            // Requête de suppression
            $sql = "DELETE FROM categorie WHERE id = :idCategorie";
            $req = $connexion->prepare($sql);
            
            // Liaison du paramètre
            $req->bindParam(':idCategorie', $idCategorie, PDO::PARAM_INT);

            // Exécution de la requête
            $req->execute();
            // Successful deletion notification (you can customize this notification)
            echo '<script>alert("Category deleted successfully!");</script>';
            } catch (PDOException $e) {
                // In case of an error, display an error message (you can customize this notification)
                echo '<script>alert("Error during deletion: ' . $e->getMessage() . '");</script>';
            }
            } else {
                // If the Category ID is not specified, display a warning (you can customize this notification)
                echo '<script>alert("Category ID not specified!");</script>';
        }


    // Redirection vers la page de categorie
    header('Location: ../vue/categorie.php');


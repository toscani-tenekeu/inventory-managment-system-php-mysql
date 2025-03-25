<?php
    require_once 'auth.php';
    include 'connexion.php';

    if (isset($_GET['idArticle'])) {
        $idArticle = $_GET['idArticle'];

        try {
            // Requête de suppression
            $sql = "DELETE FROM article WHERE id = :idArticle";
            $req = $connexion->prepare($sql);
            
            // Liaison du paramètre
            $req->bindParam(':idArticle', $idArticle, PDO::PARAM_INT);

            // Exécution de la requête
            $req->execute();

            // Successful deletion notification (you can customize this notification)
            echo '<script>alert("Article deleted successfully!");</script>';
            } catch (PDOException $e) {
                // In case of an error, display an error message (you can customize this notification)
                echo '<script>alert("Error during deletion: ' . $e->getMessage() . '");</script>';
            }
            } else {
                // If the Article ID is not specified, display a warning (you can customize this notification)
                echo '<script>alert("Article ID not specified!");</script>';
        }


    // Redirection vers la page de Article
    header('Location: ../vue/article.php');


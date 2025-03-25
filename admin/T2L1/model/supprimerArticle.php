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

            // Notification de suppression réussie (vous pouvez personnaliser cette notification)
            echo '<script>alert("Article supprimée avec succès!");</script>';
        } catch (PDOException $e) {
            // En cas d'erreur, affichez un message d'erreur (vous pouvez personnaliser cette notification)
            echo '<script>alert("Erreur lors de la suppression: '.$e->getMessage().'");</script>';
        }
    } else {
        // Si l'ID de Article n'est pas spécifié, affichez un avertissement (vous pouvez personnaliser cette notification)
        echo '<script>alert("ID de article non spécifié!");</script>';
    }

    // Redirection vers la page de Article
    header('Location: ../vue/article.php');


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

            // Notification de suppression réussie (vous pouvez personnaliser cette notification)
            echo '<script>alert("categorie supprimée avec succès!");</script>';
        } catch (PDOException $e) {
            // En cas d'erreur, affichez un message d'erreur (vous pouvez personnaliser cette notification)
            echo '<script>alert("Erreur lors de la suppression: '.$e->getMessage().'");</script>';
        }
    } else {
        // Si l'ID de categorie n'est pas spécifié, affichez un avertissement (vous pouvez personnaliser cette notification)
        echo '<script>alert("ID de categorie non spécifié!");</script>';
    }

    // Redirection vers la page de categorie
    header('Location: ../vue/categorie.php');


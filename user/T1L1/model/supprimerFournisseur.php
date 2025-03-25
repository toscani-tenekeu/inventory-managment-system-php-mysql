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

            // Notification de suppression réussie (vous pouvez personnaliser cette notification)
            echo '<script>alert("Fournisseur supprimée avec succès!");</script>';
        } catch (PDOException $e) {
            // En cas d'erreur, affichez un message d'erreur (vous pouvez personnaliser cette notification)
            echo '<script>alert("Erreur lors de la suppression: '.$e->getMessage().'");</script>';
        }
    } else {
        // Si l'ID de Fournisseur n'est pas spécifié, affichez un avertissement (vous pouvez personnaliser cette notification)
        echo '<script>alert("ID du fournisseur non spécifié!");</script>';
    }

    // Redirection vers la page de Fournisseur
    header('Location: ../vue/fournisseur.php');


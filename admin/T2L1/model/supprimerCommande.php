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

            // Notification de suppression réussie (vous pouvez personnaliser cette notification)
            echo '<script>alert("Commande supprimée avec succès!");</script>';
        } catch (PDOException $e) {
            // En cas d'erreur, affichez un message d'erreur (vous pouvez personnaliser cette notification)
            echo '<script>alert("Erreur lors de la suppression: '.$e->getMessage().'");</script>';
        }
    } else {
        // Si l'ID de Commande n'est pas spécifié, affichez un avertissement (vous pouvez personnaliser cette notification)
        echo '<script>alert("ID de la commande non spécifié!");</script>';
    }

    // Redirection vers la page de Commande
    header('Location: ../vue/Commande.php');


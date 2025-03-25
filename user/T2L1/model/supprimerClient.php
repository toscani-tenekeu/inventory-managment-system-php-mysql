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

            // Notification de suppression réussie (vous pouvez personnaliser cette notification)
            echo '<script>alert("Client supprimée avec succès!");</script>';
        } catch (PDOException $e) {
            // En cas d'erreur, affichez un message d'erreur (vous pouvez personnaliser cette notification)
            echo '<script>alert("Erreur lors de la suppression: '.$e->getMessage().'");</script>';
        }
    } else {
        // Si l'ID de Client n'est pas spécifié, affichez un avertissement (vous pouvez personnaliser cette notification)
        echo '<script>alert("ID du client non spécifié!");</script>';
    }

    // Redirection vers la page de Client
    header('Location: ../vue/client.php');


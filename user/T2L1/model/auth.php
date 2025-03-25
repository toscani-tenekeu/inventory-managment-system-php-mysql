<?php
    // Démarrez la session
    session_start();

    // Vérifiez si l'utilisateur est authentifié
    if (!isset($_SESSION['prenom'])) {
        // Redirigez vers la page de connexion
        header('Location: ../vue/connexionInscription.php');
        exit();
    }
?>

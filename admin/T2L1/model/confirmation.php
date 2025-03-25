<?php
// Confirmation.php
session_start();
// Votre contenu de confirmation
echo "<script>alert('Inscription réussie! Vous pouvez maintenant vous connecter.');</script>";

// Optionnel : Connectez automatiquement l'utilisateur
$_SESSION["utilisateur_connecte"] = true;

// Redirection après l'affichage du message
header("Location: ../vue/connexionInscription.php");
exit();
?>

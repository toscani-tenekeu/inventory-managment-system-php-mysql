<?php
// Confirmation.php
session_start();
// Votre contenu de confirmation
echo "<script>alert('Registration successful! You can now log in.');</script>";

// Optionnel : Connectez automatiquement l'utilisateur
$_SESSION["utilisateur_connecte"] = true;

// Redirection après l'affichage du message
header("Location: ../vue/connexionInscription.php");
exit();
?>

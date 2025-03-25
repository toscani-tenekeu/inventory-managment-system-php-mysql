<?php
// Vérifie si la session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$nom_serveur = "sql307.infinityfree.com";
$nom_bd = "if0_38562644_li_stock";
$nom_utilisateur = "if0_38562644";
$mot_de_passe = "password4321go";

try {
    // Création d'une connexion PDO
    $connexion = new PDO("mysql:host=$nom_serveur;dbname=$nom_bd", $nom_utilisateur, $mot_de_passe);

    // Définir le mode d'erreur pour PDO
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Capture et affichage des erreurs de connexion
    die("Erreur de connexion : " . $e->getMessage());
}
?>

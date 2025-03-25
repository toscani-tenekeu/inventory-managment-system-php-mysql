<?php

$servername = "sql307.infinityfree.com";
$dbname = "if0_38562644_li_stock";
$username = "if0_38562644";
$password = "password4321go";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données du formulaire

    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    // Préparation de la requête SQL
    $stmt = $conn->prepare("INSERT INTO utilisateur (prenom, email, mot_de_passe) VALUES (:prenom, :email, :mot_de_passe)");

    // Liaison des paramètres
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mot_de_passe', $mot_de_passe);

    // Exécution de la requête
    $stmt->execute();

    // Redirigez vers la liste des utilisateurs après la suppression
    echo '<script>';
    echo 'if (confirm("Registration successful. Press OK and then login to connect.")) {';
    echo 'window.location.href = "../vue/connexionInscription.php";';
    echo '}';
    echo '</script>';
} catch (PDOException $e) {
    echo "Registration error:" . $e->getMessage();
}

// Fermeture de la connexion
$conn = null;
?>

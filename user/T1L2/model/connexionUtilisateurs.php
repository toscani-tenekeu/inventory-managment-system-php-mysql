<?php
// Démarrez la session PHP
session_start();

$servername = "sql307.infinityfree.com";
$dbname = "if0_38562644_li_stock";
$username = "if0_38562644";
$password = "password4321go";

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
// definition de l'erreur PDO produite lors de la connexion
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération des données du formulaire
$email = $_POST['email'];
$mot_de_passe = $_POST['mot_de_passe'];

try {
    // Préparation de la requête SQL
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email=:email");
    
    // Liaison des paramètres
    $stmt->bindParam(':email', $email);

    // Exécution de la requête
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Utilisateur trouvé, vérification du mot de passe
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
            // Stockez le prénom et l'ID de l'utilisateur dans la session
            $_SESSION['prenom'] = $row['prenom'];
            $_SESSION['id'] = $row['id'];

            // Vérification du rôle de l'utilisateur
            $role = $row['role'];
            if ($role === 'admin') {
                // Redirection vers le tableau de bord admin
                echo "<script>alert('This is an administrator account, you will be redirected to the corresponding view!'); window.location.href = '../../../admin/T2L1/vue/dashboard.php';</script>";
            } else {
                // Redirection par defaut pour les utilisateurs standard
                header("Location: ../vue/dashboard.php");
            }
        } else {
            echo "<script>alert('Incorrect password
            ');</script>";
            header('Location: ../vue/connexionInscription.php'); 
        }
    } else {
        echo "<script>alert('User not found');</script>";
        header('Location: ../vue/connexionInscription.php'); 
    }
} catch (PDOException $e) {
    echo "Connection error:" . $e->getMessage();
    header('Location: ../vue/connexionInscription.php'); 

}

// Fermeture de la connexion
$conn = null;
?>
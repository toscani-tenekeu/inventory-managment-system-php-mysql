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
                header("Location: ../vue/dashboard.php");
                exit;
            } else if ($role === 'utilisateur') {
                // Redirection par defaut pour les utilisateurs standard
                echo "<script>alert('The account you are trying to log into is a standard account! !\\n You will be redirected to the standard user view.'); window.location.href = '../../../user/T1L2/vue/dashboard.php'; </script>";
            } else {
                echo "<script>alert('Incorrect account role verification error!');</script>";
            }
        } else {
            echo "<script>alert('Incorrect Password'); window.location.href = '../vue/connexionInscription.php'; </script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location.href = '../vue/connexionInscription.php'; </script>";
    }
} catch (PDOException $e) {
   echo "<script>alert('Something went wrong during the connection !')</script>";
}

// Fermeture de la connexion
$conn = null;
?>
<?php
require_once '../model/auth.php';
include '../model/connexion.php';

// Vérifiez si un ID d'utilisateur est transmis via GET
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Requête pour récupérer les informations de l'utilisateur
    $sqlUtilisateur = "SELECT id, prenom, email FROM utilisateur WHERE id = :id";
    $queryUtilisateur = $connexion->prepare($sqlUtilisateur);
    $queryUtilisateur->bindParam(':id', $userId, PDO::PARAM_INT);
    $queryUtilisateur->execute();
    $utilisateur = $queryUtilisateur->fetch(PDO::FETCH_ASSOC);
} else {
    // Redirigez si l'ID n'est pas présent
    header('Location: ./utilisateurs.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Modification</title>
    <link rel="stylesheet" href="../public/css/stylesSecondaires.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .container {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .back-to-users {
            display: block;
            margin-bottom: 20px;
            color: #fff;
            background-color: #333;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
        }

        input {
            padding: 8px;
            margin-bottom: 16px;
        }

        button {
            background-color: #333;
            color: #fff;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>User Modification</h1>
    </header>

    <div class="container">
        <a href="./utilisateurs.php" class="back-to-users">&#x2190; Back to User List</a>

        <form action="../model/modifUtilisateur.php" method="POST">
            <!-- Champ caché pour transmettre l'ID de l'utilisateur lors de la soumission du formulaire -->
            <input type="hidden" name="userId" value="<?= $utilisateur['id']; ?>">

            <label for="prenom">Fist Name:</label>
            <input type="text" id="prenom" name="prenom" value="<?= $utilisateur['prenom']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= $utilisateur['email']; ?>" required>

            <!-- Ajoutez d'autres champs si nécessaire, comme le mot de passe -->

            <button type="submit">Apply Changes</button>
        </form>
    </div>

</body>
</html>

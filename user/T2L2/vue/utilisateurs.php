<?php
require_once '../model/auth.php';
include '../model/connexion.php';

// Requête pour récupérer les utilisateurs
$sqlUtilisateurs = "SELECT id, prenom, email, role FROM utilisateur";
$queryUtilisateurs = $connexion->prepare($sqlUtilisateurs);
$queryUtilisateurs->execute();
$resultatsUtilisateurs = $queryUtilisateurs->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User List</title>
  <link rel="stylesheet" href="../public/css/stylesSecondaires.css">
  <style>
    a {
     text-decoration: none;
      margin-right: 10px; 
    }
    .delete-user{
      color: #f00;
    }
    .edit-user{
      color: #FF7700 !important;
    }
  </style>
</head>
<body>
  <header>
    <h1>User List</h1>
  </header>

  <div class="container">
    <a href="./dashboard.php" class="back-to-dashboard">&#x2190; Back to Dashboard</a>

    <ul class="user-list">
      <?php foreach ($resultatsUtilisateurs as $utilisateur): ?>
        <li>
          <div class="user-details">
            <h2><?= $utilisateur['prenom']; ?></h2>
            <p>Email: <?= $utilisateur['email']; ?></p>
            <p>Role: <?= $utilisateur['role']; ?></p>
            <!-- Ajout des liens "Supprimer" et "Modifier" avec des paramètres d'URL pour l'identifiant de l'utilisateur -->
            <a href="../model/supprimerUtilisateur.php?id=<?= $utilisateur['id']; ?>" class="delete-user">Delete</a>
            <a href="./modifUtilisateur.php?id=<?= $utilisateur['id']; ?>" class="edit-user">Edit</a>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

</body>
</html>

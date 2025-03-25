<?php
// Connexion à la base de données
require '../model/connexion.php';

// Récupération de la saisie utilisateur
$saisie = $_POST['saisie'];

// Requête pour récupérer les données de la table 'article'
$sqlArticle = "SELECT id, nom_article AS nom, categorie, quantite, prix_unitaire, date_fabrication, date_expiration FROM article WHERE nom_article LIKE :saisie OR quantite LIKE :saisie OR prix_unitaire LIKE :saisie";
$queryArticle = $connexion->prepare($sqlArticle);
$queryArticle->bindValue(':saisie', '%' . $saisie . '%', PDO::PARAM_STR);
$queryArticle->execute();
$resultatsArticle = $queryArticle->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer les données de la table 'categorie'
$sqlCategorie = "SELECT id, nom_categorie AS nom, description_categorie FROM categorie WHERE nom_categorie LIKE :saisie OR description_categorie LIKE :saisie";
$queryCategorie = $connexion->prepare($sqlCategorie);
$queryCategorie->bindValue(':saisie', '%' . $saisie . '%', PDO::PARAM_STR);
$queryCategorie->execute();
$resultatsCategorie = $queryCategorie->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer les données de la table 'client'
$sqlClient = "SELECT id, nom, prenom, telephone, adresse FROM client WHERE nom LIKE :saisie OR prenom LIKE :saisie OR telephone LIKE :saisie OR adresse LIKE :saisie";
$queryClient = $connexion->prepare($sqlClient);
$queryClient->bindValue(':saisie', '%' . $saisie . '%', PDO::PARAM_STR);
$queryClient->execute();
$resultatsClient = $queryClient->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer les données de la table 'fournisseur'
$sqlFournisseur = "SELECT id, nom, prenom, telephone, adresse FROM fournisseur WHERE nom LIKE :saisie OR prenom LIKE :saisie OR telephone LIKE :saisie OR adresse LIKE :saisie";
$queryFournisseur = $connexion->prepare($sqlFournisseur);
$queryFournisseur->bindValue(':saisie', '%' . $saisie . '%', PDO::PARAM_STR);
$queryFournisseur->execute();
$resultatsFournisseur = $queryFournisseur->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer les données de la table 'vente'
$sqlVente = "SELECT id, quantite, prix AS prix_unitaire, date_vente FROM vente WHERE quantite LIKE :saisie OR prix LIKE :saisie OR date_vente LIKE :saisie";
$queryVente = $connexion->prepare($sqlVente);
$queryVente->bindValue(':saisie', '%' . $saisie . '%', PDO::PARAM_STR);
$queryVente->execute();
$resultatsVente = $queryVente->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer les données de la table 'commande'
$sqlCommande = "SELECT id, quantite, prix AS prix_unitaire, date_commande FROM commande WHERE quantite LIKE :saisie OR prix LIKE :saisie OR date_commande LIKE :saisie";
$queryCommande = $connexion->prepare($sqlCommande);
$queryCommande->bindValue(':saisie', '%' . $saisie . '%', PDO::PARAM_STR);
$queryCommande->execute();
$resultatsCommande = $queryCommande->fetchAll(PDO::FETCH_ASSOC);

// ... Répétez le processus pour d'autres tables ...

// Affichage des résultats sous forme de tableaux stylés
function afficherTableau1($resultats, $nomTable) {
    if (!empty($resultats)) {
        echo "<h2>Résultats de la table $nomTable</h2>";
        echo '<table border="1">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Date Fabrication</th>
                    <th>Date Expiration</th>
                </tr>';

        foreach ($resultats as $row) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['nom'] . '</td>';
            echo '<td>' . $row['categorie'] . '</td>';
            echo '<td>' . $row['quantite'] . '</td>';
            echo '<td>' . $row['prix_unitaire'] . '</td>';
            echo '<td>' . $row['date_fabrication'] . '</td>';
            echo '<td>' . $row['date_expiration'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo "Aucun résultat trouvé pour la table $nomTable.<br>";
    }
}

function afficherTableau2($resultats, $nomTable) {
  if (!empty($resultats)) {
      echo "<h2>Résultats de la table $nomTable</h2>";
      echo '<table border="1">
              <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th>Description</th>
              </tr>';

      foreach ($resultats as $row) {
          echo '<tr>';
          echo '<td>' . $row['id'] . '</td>';
          echo '<td>' . $row['nom'] . '</td>';
          echo '<td>' . $row['description_categorie'] . '</td>';
          echo '</tr>';
      }

      echo '</table>';
  } else {
      echo "Aucun résultat trouvé pour la table $nomTable.<br>";
  }
}

function afficherTableau3($resultats, $nomTable) {
  if (!empty($resultats)) {
      echo "<h2>Résultats de la table $nomTable</h2>";
      echo '<table border="1">
              <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th>Prenom</th>
                  <th>Telephone</th>
                  <th>Adresse</th>
              </tr>';

      foreach ($resultats as $row) {
          echo '<tr>';
          echo '<td>' . $row['id'] . '</td>';
          echo '<td>' . $row['nom'] . '</td>';
          echo '<td>' . $row['prenom'] . '</td>';
          echo '<td>' . $row['telephone'] . '</td>';
          echo '<td>' . $row['adresse'] . '</td>';
          echo '</tr>';
      }

      echo '</table>';
  } else {
      echo "Aucun résultat trouvé pour la table $nomTable.<br>";
  }
}

function afficherTableau4($resultats, $nomTable) {
  if (!empty($resultats)) {
      echo "<h2>Résultats de la table $nomTable</h2>";
      echo '<table border="1">
              <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th>Prenom</th>
                  <th>Telephone</th>
                  <th>Adresse</th>
              </tr>';

      foreach ($resultats as $row) {
          echo '<tr>';
          echo '<td>' . $row['id'] . '</td>';
          echo '<td>' . $row['nom'] . '</td>';
          echo '<td>' . $row['prenom'] . '</td>';
          echo '<td>' . $row['telephone'] . '</td>';
          echo '<td>' . $row['adresse'] . '</td>';
          echo '</tr>';
      }

      echo '</table>';
  } else {
      echo "Aucun résultat trouvé pour la table $nomTable.<br>";
  }
}

function afficherTableau5($resultats, $nomTable) {
  if (!empty($resultats)) {
      echo "<h2>Résultats de la table $nomTable</h2>";
      echo '<table border="1">
              <tr>
                  <th>ID</th>
                  <th>Qte</th>
                  <th>Prix unitaire</th>
                  <th>Date de vente</th>
              </tr>';

      foreach ($resultats as $row) {
          echo '<tr>';
          echo '<td>' . $row['id'] . '</td>';
          echo '<td>' . $row['quantite'] . '</td>';
          echo '<td>' . $row['prix_unitaire'] . '</td>';
          echo '<td>' . $row['date_vente'] . '</td>';
          echo '</tr>';
      }

      echo '</table>';
  } else {
      echo "Aucun résultat trouvé pour la table $nomTable.<br>";
  }
}

function afficherTableau6($resultats, $nomTable) {
  if (!empty($resultats)) {
      echo "<h2>Résultats de la table $nomTable</h2>";
      echo '<table border="1">
              <tr>
                  <th>ID</th>
                  <th>Qte</th>
                  <th>Prix unitaire</th>
                  <th>Date de commande</th>
              </tr>';

      foreach ($resultats as $row) {
          echo '<tr>';
          echo '<td>' . $row['id'] . '</td>';
          echo '<td>' . $row['quantite'] . '</td>';
          echo '<td>' . $row['prix_unitaire'] . '</td>';
          echo '<td>' . $row['date_commande'] . '</td>';
          echo '</tr>';
      }

      echo '</table>';
  } else {
      echo "Aucun résultat trouvé pour la table $nomTable.<br>";
  }
}

// Appel des fonctions d'affichage avec les résultats correspondants
afficherTableau1($resultatsArticle, 'article');
afficherTableau2($resultatsCategorie, 'categorie');
afficherTableau3($resultatsClient, 'client');
afficherTableau4($resultatsFournisseur, 'fournisseur');
afficherTableau5($resultatsVente, 'vente');
afficherTableau6($resultatsCommande, 'commande');


?>

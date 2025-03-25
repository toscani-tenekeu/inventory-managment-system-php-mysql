<?php
require_once '../model/auth.php';
include '../model/connexion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search</title>
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

    main {
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    input[type="text"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
      outline: none;
    }

    section {
      margin-bottom: 20px;
    }

    h2 {
            font-size: 1.5em;
            color: #0f0;
    }

    /* Style du bouton retour-dashboard */
    .retour-dashboard {
        position: relative;
        left: -40%;
        padding: 10px 20px;
        background-color: #eee;
        color: #777;
        text-decoration: none;
        border-radius: 20px;
        transition: background-color 0.3s, box-shadow 0.3s;
        margin-top: 20px;
    }

    /* Effet de survol */
    .retour-dashboard:hover {
        background-color: #555;
        color: #000;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>
<body>
  <header>
    <h1>Inventory Management <em style="color: #777;">Lovely Indian</em>  | Search</h1>

    <!-- Bouton pour retourner à la page dashboard -->
    <a href="./dashboard.php" class="retour-dashboard">Back</a>
  </header>

  <main>
    <input type="text" id="terme" placeholder="Entrez votre recherche" oninput="rechercher()">
    <ul id="resultats"></ul>

    <script>
        function rechercher() {
            var saisie = document.getElementById('terme').value;

            // Vérifier si la saisie est vide
            if (saisie.trim() === '') {
                // Si la saisie est vide, ne rien faire
                $('#resultats').html('');
                return;
            }

            $.ajax({
                type: 'POST',
                url: 'recherche.php',
                data: { saisie: saisie },
                success: function(resultat) {
                    $('#resultats').html(resultat);
                }
            });
        }
    </script>

  </main>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</body>
</html>

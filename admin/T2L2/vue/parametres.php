<?php
require_once '../model/auth.php';
require_once '../model/connexion.php';

// Vérifier si des données ont été soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs des listes déroulantes
    $langue = $_POST['langue'];
    $theme = $_POST['theme'];

    // Faire quelque chose avec les données (par exemple, les enregistrer en base de données)

    // Redirection vers la page appropriée
    header('Location: appliquerParametre.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../public/css/stylesSecondaires.css">
    <style>
        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        /* Style pour le bouton */
        #appliquer-changements {
            padding: 10px;
            background-color: #456A55;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #appliquer-changements:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Reste du contenu du fichier original -->
    <header>
        <h1>Settings</h1>
    </header>

    <div class="container">
        <a href="./dashboard.php" class="back-to-dashboard">&#x2190; Back to Dashboard</a>

        <section>
            <h2>Theme</h2>
            <form method="post" action="../model/appliquerParametre.php">
                <label for="theme">Choose Theme :</label>
                <select id="theme" name="theme">
                    <option value="toscani">Toscani Theme (Default)</option>
                    <option value="lovely-indian">Theme de Lovely Indian</option>
                </select>

                <h2>Language</h2>
                <label for="langue">Choose language :</label>
                <select id="langue" name="langue">
                    <option value="francais">French (Default)</option>
                    <option value="anglais">English</option>
                </select>
                <br><br>
                <!-- Bouton "Appliquer les changements" -->
                <button type="submit" id="appliquer-changements">Apply Changes</button>
            </form>
        </section>

        <!-- Ajoutez d'autres sections de paramètres selon vos besoins -->

    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</body>
</html>

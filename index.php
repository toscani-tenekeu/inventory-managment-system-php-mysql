<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Lien du style bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Home Page</title>
</head>
<body>
    <script>
        // Demande le role de l'utilsateur 
        let choix = prompt("Etes-vous adminitrateur ou utilisateur standard ? (a/u)");
        if ((choix.toLowerCase() == "a") || (choix.toLowerCase() == "admin") || (choix.toLowerCase() == "administrateur")) {
            window.location.href = "./admin/T1L1/vue/connexionInscription.php";
        } else if ((choix.toLowerCase() == "u") || (choix.toLowerCase() == "utilisateur")) {
            window.location.href = "./user/T1L1/vue/connexionInscription.php";   
        } else {
            alert('Votre choix n\'est pas prise en charge, vous serez rediriger vers la page par defaut !');
            window.location.href = "./user/T1L1/vue/connexionInscription.php"; 
        }
    </script>
    

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
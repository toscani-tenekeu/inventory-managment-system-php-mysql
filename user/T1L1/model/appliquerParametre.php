<?php
// Inclure les fichiers nécessaires
require_once 'auth.php';
require_once 'connexion.php';

// Vérifier si des données ont été soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs des listes déroulantes
    $langue = $_POST['langue'];
    $theme = $_POST['theme'];

    // Préparer la requête SQL pour l'insertion des données dans la table parametre
    $insertQuery = "INSERT INTO parametre (langue, theme) VALUES (?, ?)";
    $stmt = $connexion->prepare($insertQuery);

    // Exécuter la requête avec les valeurs récupérées
    $stmt->execute([$langue, $theme]);

    // Vérifier les conditions et effectuer les redirections appropriées
    if ($langue === 'francais' && $theme === 'toscani') {

        echo "<script>alert('Paramètre appliqué avec succès !'); window.location.href='../vue/dashboard.php';</script>";
        exit();

    } elseif ($langue === 'anglais' && $theme === 'lovely-indian') {

        echo "<script>alert('Paramètre appliqué avec succès !'); window.location.href='../../T2L2/vue/dashboard.php';</script>";
        exit();

    } elseif ($langue === 'francais' && $theme === 'lovely-indian') {

        echo "<script>alert('Paramètre appliqué avec succès !'); window.location.href='../../T2L1/vue/dashboard.php';</script>";
        exit();

    } elseif ($langue === 'anglais' && $theme === 'toscani') {

        echo "<script>alert('Paramètre appliqué avec succès !'); window.location.href='../../T1L2/vue/dashboard.php';</script>";
        exit();
    }
    // Ajouter d'autres conditions selon vos besoins

    // Redirection par défaut si aucune condition n'est satisfaite
    ?>
    <script>
        alert('Echec lors de l\'application des modifications, veuillez recommencer !');
        window.location.href = "../vue/parametres.php";
    </script>
    <?php
    exit();
} else {
    // Redirection si la page est accédée directement sans soumission de formulaire
    header('Location: ../../T1L1/vue/connexionInscription.php');
    exit();
}
?>

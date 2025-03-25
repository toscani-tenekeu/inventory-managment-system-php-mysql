<?php
    require_once '../model/auth.php';
    // Inclusion du fichier de connexion à la base de données
    include 'entete.php';

    // Vérifie si un identifiant d'article est présent dans l'URL
    if (!empty($_GET['id'])) {
        // Récupère les informations de l'article en fonction de l'identifiant
        $article = getArticle($_GET['id']);
    }
?>

<!-- Structure de la page -->
<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <!-- Formulaire pour ajouter ou modifier un article -->
            <form action="<?= !empty($_GET['id']) ? "../model/modifArticle.php" : "../model/ajoutArticle.php" ?>" method="post">
                <label for="nom_article">Article Name</label>
                <input value="<?= !empty($_GET['id']) ? $article['nom_article'] : "" ?>" type="text" name="nom_article" id="nom_article" placeholder="Enter name here">

                <!-- Champ caché pour l'identifiant de l'article (utilisé pour la modification) -->
                <input value="<?= !empty($_GET['id']) ? $article['id'] : "" ?>" type="hidden" name="id" id="id">

                <label for="categorie">Article category</label>
                <select name="categorie" id="categorie">
                    <?php
                        // Récupère toutes les catégories existantes
                        $categories = getCategorie();

                        // Affiche chaque catégorie dans la liste déroulante
                        if (!empty($categories) && is_array($categories)) {
                            foreach ($categories as $key => $value) {
                    ?>
                        <option <?= !empty($_GET['id']) && $article['id_categorie'] == $value['id'] ? "selected" : "" ?> value="<?= $value['nom_categorie'] ?>"><?= $value['nom_categorie'] ?></option>
                    <?php
                            }
                        }
                    ?>
                </select>

                <label for="date_fabrication">Manufacturing Date</label>
                <input value="<?= !empty($_GET['id']) ? $article['date_fabrication'] : "" ?>" type="datetime-local" name="date_fabrication" id="date_fabrication">

                <label for="date_expiration">>Expiration Daten</label>
                <input value="<?= !empty($_GET['id']) ? $article['date_expiration'] : "" ?>" type="datetime-local" name="date_expiration" id="date_expiration">

                <!-- Les champs quantité et prix_unitaire sont présents même lors de la modification -->

                <label for="prix_unitaire">Unit price</label>
                <input type="number" name="prix_unitaire" id="prix_unitaire" placeholder="Please enter the unit price" value="<?= !empty($_GET['id']) ? $article['prix_unitaire'] : "" ?>">

                <!-- Bouton pour soumettre le formulaire -->
                <button type="submit">Submit</button>

                <br><br>

                <?php
                    // Affiche un message d'alerte en fonction du succès ou de l'échec de l'opération
                    if (!empty($_SESSION['message']['text'])) {
                ?>
                    <div class="alert <?= $_SESSION['message']['type'] ?>">
                        <?= $_SESSION['message']['text'] ?>
                    </div>
                <?php } ?>
            </form>
        </div>

        <div class="box">
            <!-- Tableau affichant tous les articles existants -->
            <table class="mtable">
                <tr>
                    <th>Article Name</th>
                    <th>Category</th>
                    <th>Manufacturing Date</th>
                    <th>Expiration Date</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Action</th>
                </tr>

                <?php
                    // Récupère tous les articles existants
                    $articles = getArticle();

                    // Affiche chaque article dans le tableau
                    if (!empty($articles) && is_array($articles)) {
                        foreach ($articles as $key => $value) {
                ?>
                            <tr>
                                <td><?= $value['nom_article'] ?></td>
                                <td><?= $value['categorie'] ?></td>
                                <td><?= date('d/m/y H:i:s', strtotime($value['date_fabrication']))  ?></td>
                                <td><?= date('d/m/y H:i:s', strtotime($value['date_expiration'])) ?></td>
                                <td><?= $value['quantite'] ?></td>
                                <td><?= $value['prix_unitaire'] ?></td>
                                <td>
                                    <!-- Icônes pour éditer et supprimer l'article -->
                                    <a href="?id=<?= $value['id'] ?>"><i class="bx bx-edit-alt" style="font-size: 30px;"></i></a>
                                </td>
                            </tr>
                <?php
                        }
                    }
                ?>
            </table>
        </div>
    </div>
</div>

<?php
    // Inclusion du pied de page
    include 'pied.php';
?>

<!-- Script JavaScript pour la confirmation de suppression -->
<script>
    function confirmerSuppressionArt(idArticle) {
        var confirmation = confirm("Do you really want to delete this article?\nThis action is irreversible!");

        if (confirmation) {
            try {
                // Redirection vers le script de suppression avec l'identifiant de l'article
                window.location.href = "../model/supprimerArticle.php?idArticle=" + idArticle;
            } catch (e) {
                console.error("Redirection error: ", e);
                alert("Redirection error: " + e.message);
            }
        }
    }
</script>

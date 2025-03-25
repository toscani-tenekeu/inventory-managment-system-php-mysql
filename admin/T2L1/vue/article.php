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
                <label for="nom_article">Nom de l'article</label>
                <input value="<?= !empty($_GET['id']) ? $article['nom_article'] : "" ?>" type="text" name="nom_article" id="nom_article" placeholder="Veuillez saisir le nom">

                <!-- Champ caché pour l'identifiant de l'article (utilisé pour la modification) -->
                <input value="<?= !empty($_GET['id']) ? $article['id'] : "" ?>" type="hidden" name="id" id="id">

                <label for="categorie">Catégorie</label>
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

                <label for="date_fabrication">Date de fabrication</label>
                <input value="<?= !empty($_GET['id']) ? $article['date_fabrication'] : "" ?>" type="datetime-local" name="date_fabrication" id="date_fabrication">

                <label for="date_expiration">Date d'expiration</label>
                <input value="<?= !empty($_GET['id']) ? $article['date_expiration'] : "" ?>" type="datetime-local" name="date_expiration" id="date_expiration">

                <!-- Les champs quantité et prix_unitaire sont présents même lors de la modification -->

                <label for="prix_unitaire">Prix unitaire</label>
                <input type="number" name="prix_unitaire" id="prix_unitaire" placeholder="Veuillez saisir le prix unitaire" value="<?= !empty($_GET['id']) ? $article['prix_unitaire'] : "" ?>">

                <!-- Bouton pour soumettre le formulaire -->
                <button type="submit">Valider</button>

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
                    <th>Nom de l'article</th>
                    <th>Catégorie</th>
                    <th>Date de fabrication</th>
                    <th>Date d'expiration</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
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
                                    <a onclick="confirmerSuppressionArt(<?= $value['id'] ?>)" style="color: #f00;" title="Supprimer l'article"><i class="bx bx-trash" style="font-size: 30px;"></i></a>
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
        var confirmation = confirm("Voulez-vous vraiment supprimer cet article ?\nCette action est irréversible!");

        if (confirmation) {
            try {
                // Redirection vers le script de suppression avec l'identifiant de l'article
                window.location.href = "../model/supprimerArticle.php?idArticle=" + idArticle;
            } catch (e) {
                console.error("Erreur de redirection : ", e);
                alert("Erreur de redirection : " + e.message);
            }
        }
    }
</script>

<?php 
     require_once '../model/auth.php';
    // Inclusion du fichier d'entête
    include 'entete.php';

    // Vérifie si un identifiant de catégorie est présent dans l'URL
    if (!empty ($_GET['id'])) {
        // Récupère les informations de la catégorie en fonction de l'identifiant
        $categorie = getCategorie($_GET['id']);
    }
?>

<!-- Structure de la page -->
<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <!-- Formulaire pour ajouter ou modifier une catégorie -->
            <form action="<?= !empty ($_GET['id']) ? "../model/modifCategorie.php" : "../model/ajoutCategorie.php" ?>" method="post">

                <!-- Champ caché pour l'identifiant de la catégorie (utilisé pour la modification) -->
                <input value="<?= !empty ($_GET['id']) ? $categorie['id'] : "" ?>" type="hidden" name="id" id="id">

                <label for="nom_categorie">Nom de la catégorie</label>
                <input value="<?= !empty ($_GET['id']) ? $categorie['nom_categorie'] : "" ?>" type="text" name="nom_categorie" id="nom_categorie" placeholder="Veuillez saisir le nom">

                <label for="description_categorie">Description de la catégorie</label>
                <textarea name="description_categorie" id="description_categorie" cols="5" rows="5"><?= !empty ($_GET['id']) ? $categorie['description_categorie'] : "" ?></textarea>
                <style>
                    textarea:focus{
                        outline: 2px solid #2bd47d;
                    }
                </style>

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
                <?php   
                    }
                ?>
            </form>
        </div>

        <div class="box">
            <!-- Tableau affichant toutes les catégories existantes -->
            <table class="mtable">
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>

                <?php
                    // Récupère toutes les catégories existantes
                    $categories = getCategorie();

                    // Affiche chaque catégorie dans le tableau
                    if (!empty($categories) && is_array($categories)) {
                        foreach ($categories as $key => $value) {
                ?>
                            <tr>
                                <td><?= $value['nom_categorie'] ?></td>
                                <td><?= $value['description_categorie'] ?></td>
                                <td>
                                    <!-- Icônes pour éditer et supprimer la catégorie -->
                                    <a href="?id=<?= $value['id'] ?>"><i class="bx bx-edit-alt" style="font-size: 30px;"></i></a>
                                    <a onclick="confirmerSuppressionCat(<?= $value['id'] ?>)" style="color: #f55;" title="Supprimer la catégorie"><i class="bx bx-trash" style="font-size: 30px; color: #f00;"></i></a>
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
    function confirmerSuppressionCat(idCategorie) {
        // Demander confirmation à l'utilisateur via la boîte de dialogue JavaScript
        var confirmation = confirm("Voulez-vous vraiment supprimer cette catégorie ?\nCette action est irréversible!");

        // Si l'utilisateur clique sur "OK" dans la boîte de dialogue
        if (confirmation) {
            // Rediriger vers le script de suppression avec l'ID de catégorie
            window.location.href = "../model/supprimerCategorie.php?idCategorie=" + idCategorie;
        }
    }
</script>

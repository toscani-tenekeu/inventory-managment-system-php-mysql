<?php 
     require_once '../model/auth.php';
    // Inclusion du fichier d'entête
    include 'entete.php';

    // Vérifie si un identifiant de commande est présent dans l'URL
    if (!empty ($_GET['id'])) {
        // Récupère les informations de la commande en fonction de l'identifiant
        $commande = getCommande($_GET['id']);
    }
?>

<!-- Structure de la page -->
<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <!-- Formulaire pour ajouter ou modifier une commande -->
            <form action="<?= !empty ($_GET['id']) ? "../model/modifCommande.php" : "../model/ajoutCommande.php" ?>" method="post">
                <!-- Champ caché pour l'identifiant de la commande (utilisé pour la modification) -->
                <input value="<?= !empty ($_GET['id']) ? $commande['id'] : "" ?>" type="hidden" name="id" id="id">

                <label for="id_article">Article</label>
                <select onchange="setPrix()" name="id_article" id="id_article">
                    <?php
                        // Récupère tous les articles
                        $articles = getArticle();

                        // Affiche chaque article dans la liste déroulante
                        if (!empty($articles) && is_array($articles)) {
                            foreach ($articles as $key => $value) {
                                ?>
                                <option data-prix="<?= $value['prix_unitaire'] ?>" value="<?= $value['id'] ?>"><?= $value['nom_article']." _ "."quantité en stock"."  ".$value['quantite'] ?></option>
                                <?php
                            }
                        }
                    ?>
                </select>

                <label for="id_fournisseur">Fournisseur</label>
                <select name="id_fournisseur" id="id_fournisseur">
                    <?php
                        // Récupère tous les fournisseurs
                        $fournisseurs = getFournisseur();

                        // Affiche chaque fournisseur dans la liste déroulante
                        if (!empty($fournisseurs) && is_array($fournisseurs)) {
                            foreach ($fournisseurs as $key => $value) {
                                ?>
                                <option value="<?= $value['id'] ?>"><?= $value['nom']." ".$value['prenom']?></option>
                                <?php
                            }
                        }
                    ?>
                </select>

                <label for="quantite">Quantité</label>
                <input onchange="setPrix()" value="<?= !empty ($_GET['id']) ? $commande['quantite'] : "" ?>" type="number" name="quantite" id="quantite" placeholder="Veuillez saisir la quantité">

                <label for="prix">Prix</label>
                <input value="<?= !empty ($_GET['id']) ? $commande['prix'] : "" ?>" type="number" name="prix" id="prix" placeholder="Veuillez saisir le prix">

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
            <!-- Tableau affichant toutes les commandes existantes -->
            <table class="mtable">
                <tr>
                    <th>Article</th>
                    <th>Fournisseur</th>
                    <th>Quantité</th>
                    <th>Prix</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

                <?php
                    // Récupère toutes les commandes existantes
                    $commandes = getCommande();

                    // Affiche chaque commande dans le tableau
                    if (!empty($commandes) && is_array($commandes)) {
                        foreach ($commandes as $key => $value) {
                ?>
                            <tr>
                                <td><?= $value['nom_article'] ?></td>
                                <td><?= $value['nom']." ".$value['prenom'] ?></td>
                                <td><?= $value['quantite'] ?></td>
                                <td><?= $value['prix'] ?></td>
                                <td><?= date('d/m/y H:i:s', strtotime($value['date_commande']))  ?></td> 
                                <td>
                                    <!-- Icônes pour générer un reçu, supprimer ou annuler la commande -->
                                    <a href="./factureCommande.php?id_commande=<?= $value['id'] ?>" title="Générer le reçu">
                                        <i class="bx bx-printer" style="font-size: 30px;"></i>
                                    </a>
                                    <a onclick="confirmerSuppressionCom(<?= $value['id'] ?>, <?= $value['idArticle'] ?>, <?= $value['quantite'] ?>)" style="color: #f00;" title="supprimer la commande"><i class="bx bx-trash" style="font-size: 30px;"></i></a>
                                    <a onclick="confirmerAnnulationCommande(<?= $value['id'] ?>, <?= $value['idArticle'] ?>, <?= $value['quantite'] ?>)" style="color: #0f0;" title="annuler la commande"><i class="bx bx-refresh" style="font-size: 30px;"></i></a>
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

<!-- Script JavaScript pour la confirmation de suppression et annulation -->
<script>
    function confirmerSuppressionCom(idCommande, idArticle, quantite) {
        var confirmation = confirm("Voulez-vous vraiment supprimer cette commande ?\nCette action est irréversible!");
        if (confirmation) {
            try {
                // Rediriger vers le script de suppression avec l'ID de la commande
                window.location.href = "../model/supprimerCommande.php?idCommande=" + idCommande;
            } catch (e) {
                console.error("Erreur de redirection : ", e);
                alert("Erreur de redirection : " + e.message);
            }
        }
    }

    function confirmerAnnulationCommande(idCommande, idArticle, quantite) {
        var confirmation = confirm("Voulez-vous vraiment annuler cette commande?\nCette action est irréversible!");
        if (confirmation) {
            // Rediriger vers le script d'annulation avec l'ID de la commande, l'ID de l'article et la quantité
            window.location.href = "../model/annulerCommande.php?idCommande="+idCommande+"&idArticle="+idArticle+"&quantite="+quantite;
        }
    }

    function setPrix() {
        // Fonction pour calculer automatiquement le prix en fonction de la quantité choisie et du prix unitaire de l'article
        var article = document.querySelector('#id_article');
        var quantite = document.querySelector('#quantite');
        var prix = document.querySelector('#prix');

        // Récupérer le prix unitaire de l'article sélectionné
        var prixUnitaire = article.options[article.selectedIndex].getAttribute('data-prix');

        // Calculer le nouveau prix en fonction de la quantité choisie et du prix unitaire
        prix.value = Number(quantite.value) * parseFloat(prixUnitaire);
    }
</script>

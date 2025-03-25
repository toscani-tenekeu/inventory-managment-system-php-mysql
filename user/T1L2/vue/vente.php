<?php
 require_once '../model/auth.php';
include 'entete.php';

// Récupération des informations de vente si l'ID est fourni dans l'URL
if (!empty($_GET['id'])) {
    $vente = getVente($_GET['id']);
}
?>

<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <form action="<?= !empty($_GET['id']) ? "../model/modifVente.php" : "../model/ajoutVente.php" ?>" method="post">
                <input value="<?= !empty($_GET['id']) ? $vente['id'] : "" ?>" type="hidden" name="id_vente" id="id_vente">

                <label for="id_article">Article</label>
                <select onchange="setPrix()" name="id_article" id="id_article">
                    <?php
                    $articles = getArticle();

                    if (!empty($articles) && is_array($articles)) {
                        foreach ($articles as $article) {
                            $selected = (!empty($_GET['id']) && $vente['id_article'] == $article['id']) ? "selected" : "";
                            ?>
                            <option data-prix="<?= $article['prix_unitaire'] ?>" value="<?= $article['id'] ?>" <?= $selected ?>>
                                <?= $article['nom_article'] . " _ " . "quantité en stock" . "  " . $article['quantite'] ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>

                <label for="id_client">Client</label>
                <select name="id_client" id="id_client">
                    <?php
                    $clients = getClient();

                    if (!empty($clients) && is_array($clients)) {
                        foreach ($clients as $client) {
                            $selected = (!empty($_GET['id']) && $vente['id_client'] == $client['id']) ? "selected" : "";
                            ?>
                            <option value="<?= $client['id'] ?>" <?= $selected ?>>
                                <?= $client['nom'] . " " . $client['prenom'] ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>

                <label for="quantite">Quantity</label>
                <input onchange="setPrix()" value="<?= !empty($_GET['id']) ? $vente['quantite'] : "" ?>" type="number" name="quantite" id="quantite" placeholder="Please enter the quantity">

                <label for="prix">Price</label>
                <input value="<?= !empty($_GET['id']) ? $vente['prix'] : "" ?>" type="number" name="prix" id="prix" placeholder="Please enter the price">

                <button type="submit">Submit</button>
                <br><br>

                <?php
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
            <table class="mtable">
                <tr>
                    <th>Article</th>
                    <th>Client</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

                <?php
                $ventes = getVente();

                if (!empty($ventes) && is_array($ventes)) {
                    foreach ($ventes as $vente) {
                        ?>
                        <tr>
                            <td><?= $vente['nom_article'] ?></td>
                            <td><?= $vente['nom_client'] . " " . $vente['prenom_client'] ?></td>
                            <td><?= $vente['quantite'] ?></td>
                            <td><?= $vente['prix'] ?></td>
                            <td><?= date('d/m/y H:i:s', strtotime($vente['date_vente'])) ?></td>
                            <td>
    
                                <a href="./factureVente.php?id_vente=<?= $vente['id_vente'] ?>&id_article=<?= $vente['id_article'] ?>&quantite=<?= $vente['quantite'] ?>&nom_client=<?= $vente['nom_client'] ?>&prenom_client=<?= $vente['prenom_client'] ?>&prix=<?= $vente['prix'] ?>&date_vente=<?= $vente['date_vente'] ?>&adresse_client=<?= $vente['adresse_client'] ?>&telephone_client=<?= $vente['telephone_client'] ?>" title="Générer le reçu">
                                    <i class="bx bx-printer" style="font-size: 30px;"></i>
                                </a>

                                <a onclick="confirmerAnnulationVente(<?= $vente['id_vente'] ?>, <?= $vente['id_article'] ?>, <?= $vente['quantite'] ?>)" title="Cancel sale">
                                    <i class="bx bx-refresh" style="font-size: 30px; color: #0f0;"></i>
                                </a>
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
include 'pied.php';
?>

<script>
    function confirmerSuppressionVente(id_vente) {
        // Demander confirmation à l'utilisateur via la boîte de dialogue JavaScript
        var confirmation = confirm("Are you sure you want to delete this sale?\nThis action is irreversible!");

        // Si l'utilisateur clique sur "OK" dans la boîte de dialogue
        if (confirmation) {
            // Rediriger vers le script de suppression avec l'ID de vente
            window.location.href = "../model/supprimerVente.php?id_vente=" + id_vente;
        }
    }

    function confirmerAnnulationVente(id_vente, id_article, quantite) {
        var confirmation = confirm("Are you sure you want to cancel this sale?\nThis action is irreversible!");
        if (confirmation) {
            window.location.href = "../model/annulerVente.php?id_vente=" + id_vente + "&id_article=" + id_article + "&quantite=" + quantite;
        }
    }
</script>

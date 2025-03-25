<?php 
    require_once '../model/auth.php';
    include 'entete.php';

    if (!empty ($_GET['id'])) {
        $fournisseur = getFournisseur($_GET['id']);
    }
?>

<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <form action="<?= !empty ($_GET['id']) ? "../model/modifFournisseur.php" : "../model/ajoutFournisseur.php" ?>" method="post">

                <label for="nom">Nom du fournisseur</label>
                <input value="<?= !empty ($_GET['id']) ? $fournisseur['nom'] : "" ?>" type="text" name="nom" id="nom" placeholder="Veuillez saisir le nom">
                <input value="<?= !empty ($_GET['id']) ? $fournisseur['id'] : "" ?>" type="hidden" name="id" id="id">

                <label for="prenom">Prenom du fournisseur</label>
                <input value="<?= !empty ($_GET['id']) ? $fournisseur['prenom'] : "" ?>" type="text" name="prenom" id="prenom" placeholder="Veuillez saisir le Prenom">
               

                <label for="telephone">N* de telephone</label>
                <input value="<?= !empty ($_GET['id']) ? $fournisseur['telephone'] : "" ?>" type="text" name="telephone" id="telephone" placeholder="Veuillez saisir le N* de telephone">

                <label for="adresse">Adresse</label>
                <input value="<?= !empty ($_GET['id']) ? $fournisseur['adresse'] : "" ?>" type="text" name="adresse" id="adresse" placeholder="Veuillez saisir l'adresse">

                <button type="submit">Valider</button>
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
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>telephone</th>
                    <th>adresse</th> 
                    <th>Action
                    </th>
                </tr>
                <?php
                    $fournisseurs = getFournisseur();

                    if (!empty($fournisseurs) && is_array($fournisseurs)) {
                        foreach ($fournisseurs as $key => $value) {
                    ?>
                        <tr>
                            <td><?= $value['nom'] ?></td>
                            <td><?= $value['prenom'] ?></td>
                            <td><?= $value['telephone'] ?></td>
                            <td><?= $value['adresse'] ?></td>
                            <td>
                                <a href="?id=<?= $value['id'] ?>"><i class="bx bx-edit-alt" style="font-size: 30px;"></i></a>
                                <a onclick="confirmerSuppressionFourn(<?= $value['id'] ?>)" style="color: #f00;" title="Supprimer ce fournisseur"><i class="bx bx-trash" style="font-size: 30px;"></i></a>
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
    function confirmerSuppressionFourn(idFournisseur) {
        var confirmation = confirm("Voulez-vous vraiment supprimer ce fournisseur ?\nCette action est irréversible!");
        if (confirmation) {
            try {
                window.location.href = "../model/supprimerFournisseur.php?idFournisseur=" + idFournisseur;
            } catch (e) {
                console.error("Erreur de redirection : ", e);
                alert("Erreur de redirection : " + e.message);
            }
        }
    }
</script>

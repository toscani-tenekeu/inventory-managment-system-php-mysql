<?php 
     require_once '../model/auth.php';
    // Inclusion du fichier d'entête
    include 'entete.php';

    // Vérifie si un identifiant de client est présent dans l'URL
    if (!empty ($_GET['id'])) {
        // Récupère les informations du client en fonction de l'identifiant
        $client = getClient($_GET['id']);
    }
?>

<!-- Structure de la page -->
<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <!-- Formulaire pour ajouter ou modifier un client -->
            <form action="<?= !empty ($_GET['id']) ? "../model/modifClient.php" : "../model/ajoutClient.php" ?>" method="post">

                <!-- Champ caché pour l'identifiant du client (utilisé pour la modification) -->
                <input value="<?= !empty ($_GET['id']) ? $client['id'] : "" ?>" type="hidden" name="id" id="id">

                <label for="nom">Nom du client</label>
                <input value="<?= !empty ($_GET['id']) ? $client['nom'] : "" ?>" type="text" name="nom" id="nom" placeholder="Veuillez saisir le nom">

                <label for="prenom">Prenom du client</label>
                <input value="<?= !empty ($_GET['id']) ? $client['prenom'] : "" ?>" type="text" name="prenom" id="prenom" placeholder="Veuillez saisir le prénom">

                <label for="telephone">N° de téléphone</label>
                <input value="<?= !empty ($_GET['id']) ? $client['telephone'] : "" ?>" type="text" name="telephone" id="telephone" placeholder="Veuillez saisir le N° de téléphone">

                <label for="adresse">Adresse</label>
                <input value="<?= !empty ($_GET['id']) ? $client['adresse'] : "" ?>" type="text" name="adresse" id="adresse" placeholder="Veuillez saisir l'adresse">

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
            <!-- Tableau affichant tous les clients existants -->
            <table class="mtable">
                <tr>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>Téléphone</th>
                    <th>Adresse</th> 
                    <th>Action</th>
                </tr>

                <?php
                    // Récupère tous les clients existants
                    $clients = getClient();

                    // Affiche chaque client dans le tableau
                    if (!empty($clients) && is_array($clients)) {
                        foreach ($clients as $key => $value) {
                ?>
                            <tr>
                                <td><?= $value['nom'] ?></td>
                                <td><?= $value['prenom'] ?></td>
                                <td><?= $value['telephone'] ?></td>
                                <td><?= $value['adresse'] ?></td>
                                <td>
                                    <!-- Icônes pour éditer et supprimer le client -->
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
    function confirmerSuppressionClient(idClient) {
        var confirmation = confirm("Voulez-vous vraiment supprimer ce client ?\nCette action est irréversible!");
        if (confirmation) {
            try {
                // Rediriger vers le script de suppression avec l'ID du client
                window.location.href = "../model/supprimerClient.php?idClient=" + idClient;
            } catch (e) {
                console.error("Erreur de redirection : ", e);
                alert("Erreur de redirection : " + e.message);
            }
        }
    }
</script>

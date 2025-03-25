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

                <label for="nom">Client Name</label>
                <input value="<?= !empty ($_GET['id']) ? $client['nom'] : "" ?>" type="text" name="nom" id="nom" placeholder="Please enter the name">

                <label for="prenom">Client First Name</label>
                <input value="<?= !empty ($_GET['id']) ? $client['prenom'] : "" ?>" type="text" name="prenom" id="prenom" placeholder="Please enter the First Name">

                <label for="telephone">Phone Number</label>
                <input value="<?= !empty ($_GET['id']) ? $client['telephone'] : "" ?>" type="text" name="telephone" id="telephone" placeholder="Please enter the Phone Number">

                <label for="adresse">Address</label>
                <input value="<?= !empty ($_GET['id']) ? $client['adresse'] : "" ?>" type="text" name="adresse" id="adresse" placeholder="Please enter the Address">

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
                    <th>Name</th>
                    <th>First Name</th>
                    <th>Phone</th>
                    <th>Address</th> 
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
                                    <a onclick="confirmerSuppressionClient(<?= $value['id'] ?>)" style="color: #f00;" title="Delete client"><i class="bx bx-trash" style="font-size: 30px;"></i></a>
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
        var confirmation = confirm("Are you sure you want to delete this client?\nThis action is irreversible!");
        if (confirmation) {
            try {
                // Rediriger vers le script de suppression avec l'ID du client
                window.location.href = "../model/supprimerClient.php?idClient=" + idClient;
            } catch (e) {
                console.error("Redirection error: ", e);
                alert("Redirection error " + e.message);
            }
        }
    }
</script>

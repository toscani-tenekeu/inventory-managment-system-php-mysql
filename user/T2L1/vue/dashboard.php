<?php 
     require_once '../model/auth.php';
    include 'entete.php';
?>

<div class="home-content">
        <div class="overview-boxes">
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Commande</div>
              <div class="number"><?php echo getAllCommande()['nbre']; ?></div>
              <div class="indicator">
                <i class="bx bx-up-arrow-alt"></i>
                <span class="text">Depuis hier</span>
              </div>
            </div>
            <i class="bx bx-cart-alt cart"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Vente</div>
              <div class="number"><?php echo getAllVente()['nbre']; ?></div>
              <div class="indicator">
                <i class="bx bx-up-arrow-alt"></i>
                <span class="text">Depuis hier</span>
              </div>
            </div>
            <i class="bx bxs-cart-add cart two"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Articles en stocks</div>
              <div class="number"><?php echo getAllArticle()['nbre']; ?></div>
              <div class="indicator">
                <i class="bx bx-up-arrow-alt"></i>
                <span class="text">Depuis hier</span>
              </div>
            </div>
            <i class="bx bx-cart cart three"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Chiffre d'affaire</div>
              <div class="number"><?php echo number_format(getCA()['montant'],0,","," ")." FCFA"; ?></div>
              <div class="indicator">
                <i class="bx bx-down-arrow-alt down"></i>
                <span class="text">Aujourd'hui</span>
              </div>
            </div>
            <i class="bx bxs-cart-download cart four"></i>
          </div>
        </div>

        <div class="sales-boxes">
          <div class="recent-sales box">
            <div class="title">Vente recentes</div>
              <?php
                    $ventes = getLastVente();
              ?>
            <div class="sales-details">
              <ul class="details">
                <li class="topic">Date</li>
                <br>
                <?php 
                  foreach ($ventes as $key => $value) {
                    ?>
                    <li><a href="#"><?php echo date('d M Y', strtotime($value['date_vente'])) ?></a></li>
                    <?php
                  }
                ?>
              </ul>
              <ul class="details">
                <li class="topic">Client</li>
                <br>
                <?php 
                  foreach ($ventes as $key => $value) {
                    ?>
                    <li><a href="#"><?php echo $value['nom']." ".$value['prenom'] ?></a></li>
                    <?php
                  }
                ?>
              </ul>
              <ul class="details">
                <li class="topic">Produit</li>
                <br>
                <?php 
                  foreach ($ventes as $key => $value) {
                    ?>
                    <li><a href="#"><?php echo $value['nom_article'] ?></a></li>
                    <?php
                  }
                ?>
              </ul>
              <ul class="details">
                <li class="topic">Prix</li>
                <br>
                <?php 
                  foreach ($ventes as $key => $value) {
                    ?>
                    <li><a href="#"><?php echo number_format($value['prix'],0,""," ")." F" ?></a></li>
                    <?php
                  }
                ?>
              </ul>
            </div>
            <div class="button">
              <a href="./dashb0ard.php">Voir Tout</a>
            </div>
          </div>
          <div class="top-sales box">
            <div class="title">Articles les plus vendu</div>
            <?php

              $article = getMostVente();
              foreach ($article as $key => $value) {
                ?>
                <li>
                  <a href="#">
                    <!--<img src="images/sunglasses.jpg" alt="">-->
                    <span class="product"><?php echo $value['nom_article'] ?></span>
                  </a>
                  <span class="price"><?php echo number_format($value['prix'],0,""," ")." F" ?></span>
                </li>
                <?php
              }

            ?>
          </div>
        </div>
      </div>
    </section>

<?php 
    include 'pied.php';
?>



<?php 
    // Inclusion du fichier d'en-tête
    include 'entete.php';
?>

<div class="home-content">
    <div class="overview-boxes">
        <!-- Boîte d'aperçu pour le nombre de commandes -->
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Commandes</div>
                <div class="number"><?php echo getAllCommande()['nbre']; ?></div>
                <div class="indicator">
                    <i class="bx bx-up-arrow-alt"></i>
                    <span class="text">Depuis hier</span>
                </div>
            </div>
            <i class="bx bx-cart-alt cart"></i>
        </div>

        <!-- Boîte d'aperçu pour le nombre de ventes -->
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Ventes</div>
                <div class="number"><?php echo getAllVente()['nbre']; ?></div>
                <div class="indicator">
                    <i class="bx bx-up-arrow-alt"></i>
                    <span class="text">Depuis hier</span>
                </div>
            </div>
            <i class="bx bxs-cart-add cart two"></i>
        </div>

        <!-- Boîte d'aperçu pour le nombre d'articles en stock -->
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Articles en stock</div>
                <div class="number"><?php echo getAllArticle()['nbre']; ?></div>
                <div class="indicator">
                    <i class="bx bx-up-arrow-alt"></i>
                    <span class="text">Depuis hier</span>
                </div>
            </div>
            <i class="bx bx-cart cart three"></i>
        </div>

        <!-- Boîte d'aperçu pour le chiffre d'affaires -->
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Chiffre d'affaires</div>
                <div class="number"><?php echo number_format(getCA()['montant'], 0, ",", " ") . " FCFA"; ?></div>
                <div class="indicator">
                    <i class="bx bx-down-arrow-alt down"></i>
                    <span class="text">Aujourd'hui</span>
                </div>
            </div>
            <i class="bx bxs-cart-download cart four"></i>
        </div>
    </div>

    <!-- Boîtes pour les ventes récentes et les articles les plus vendus -->
    <div class="sales-boxes">
        <!-- Boîte pour les ventes récentes -->
        <div class="recent-sales box">
            <div class="title">Ventes récentes</div>
            <?php
                // Récupération des ventes récentes
                $ventes = getLastVente();
            ?>
            <div class="sales-details">
                <!-- Liste des dates -->
                <ul class="details">
                    <li class="topic">Date</li>
                    <br>
                    <?php 
                        // Boucle pour afficher les dates des ventes
                        foreach ($ventes as $key => $value) {
                            ?>
                            <li><a href="#"><?php echo date('d M Y', strtotime($value['date_vente'])) ?></a></li>
                            <?php
                        }
                    ?>
                </ul>
                <!-- Liste des clients -->
                <ul class="details">
                    <li class="topic">Client</li>
                    <br>
                    <?php 
                        // Boucle pour afficher les noms des clients
                        foreach ($ventes as $key => $value) {
                            ?>
                            <li><a href="#"><?php echo $value['nom']." ".$value['prenom'] ?></a></li>
                            <?php
                        }
                    ?>
                </ul>
                <!-- Liste des produits -->
                <ul class="details">
                    <li class="topic">Produit</li>
                    <br>
                    <?php 
                        // Boucle pour afficher les noms des produits
                        foreach ($ventes as $key => $value) {
                            ?>
                            <li><a href="#"><?php echo $value['nom_article'] ?></a></li>
                            <?php
                        }
                    ?>
                </ul>
                <!-- Liste des prix -->
                <ul class="details">
                    <li class="topic">Prix</li>
                    <br>
                    <?php 
                        // Boucle pour afficher les prix
                        foreach ($ventes as $key => $value) {
                            ?>
                            <li><a href="#"><?php echo number_format($value['prix'], 0, "", " ")." F" ?></a></li>
                            <?php
                        }
                    ?>
                </ul>
            </div>
            <!-- Bouton pour voir tout les ventes récentes -->
            <div class="button">
                <a href="./dashboard.php">Voir Tout</a>
            </div>
        </div>

        <!-- Boîte pour les articles les plus vendus -->
        <div class="top-sales box">
            <div class="title">Articles les plus vendus</div>
            <?php
                // Récupération des articles les plus vendus
                $articles = getMostVente();
                foreach ($articles as $key => $value) {
                    ?>
                    <li>
                        <a href="#">
                            <!--<img src="images/sunglasses.jpg" alt="">-->
                            <span class="product"><?php echo $value['nom_article'] ?></span>
                        </a>
                        <span class="price"><?php echo number_format($value['prix'], 0, "", " ")." F" ?></span>
                    </li>
                    <?php
                }
            ?>
        </div>
    </div>
</div>
</section>

<?php 
    // Inclusion du fichier de pied de page
    include 'pied.php';
?>

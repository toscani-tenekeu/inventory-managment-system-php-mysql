<?php 
     require_once '../model/auth.php';
    include 'entete.php';
?>

<div class="home-content">
        <div class="overview-boxes">
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Orders</div>
              <div class="number"><?php echo getAllCommande()['nbre']; ?></div>
              <div class="indicator">
                <i class="bx bx-up-arrow-alt"></i>
                <span class="text">Since Yesterday</span>
              </div>
            </div>
            <i class="bx bx-cart-alt cart"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Sales</div>
              <div class="number"><?php echo getAllVente()['nbre']; ?></div>
              <div class="indicator">
                <i class="bx bx-up-arrow-alt"></i>
                <span class="text">Since Yesterday</span>
              </div>
            </div>
            <i class="bx bxs-cart-add cart two"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Items in Stock</div>
              <div class="number"><?php echo getAllArticle()['nbre']; ?></div>
              <div class="indicator">
                <i class="bx bx-up-arrow-alt"></i>
                <span class="text">Since Yesterday</span>
              </div>
            </div>
            <i class="bx bx-cart cart three"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Turnover</div>
              <div class="number"><?php echo number_format(getCA()['montant'],0,","," ")." FCFA"; ?></div>
              <div class="indicator">
                <i class="bx bx-down-arrow-alt down"></i>
                <span class="text">Today</span>
              </div>
            </div>
            <i class="bx bxs-cart-download cart four"></i>
          </div>
        </div>

        <div class="sales-boxes">
          <div class="recent-sales box">
            <div class="title">Recent Sales</div>
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
                <li class="topic">Product</li>
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
                <li class="topic">Price</li>
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
              <a href="./dashb0ard.php">See More</a>
            </div>
          </div>
          <div class="top-sales box">
            <div class="title">Best Selling Items </div>
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




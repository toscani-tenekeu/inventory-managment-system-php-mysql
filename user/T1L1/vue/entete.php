<?php
   require_once '../model/auth.php';
  require_once '../model/function.php';
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <title>
      <?php
        echo ucfirst(str_replace(".php","",basename($_SERVER['PHP_SELF'])));
      ?>
    </title>
    <link rel="stylesheet" href="../public/css/stylesPrincipales.css" />
  
    <!-- Font Awesome Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-lZ77FhIF7GbbM/EjsI93FuiioGe1R6a3hVB/jE9W2xzXSvKKUUtTX8C2uMh1Z2RS" crossorigin="anonymous">
    <!-- -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Boxicons CDN Link -->
    <link
      href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <!-- hover effect on logo -->
    <style>
      i:hover{
        cursor: pointer;
        color: #0ff !important;
      }
      .logo_name:hover{
        cursor: pointer;
        color: #7ff !important;
      }
    </style>
    <div class="sidebar">
      <div class="logo-details" style="margin-bottom: 20%;">
        <i style="font-size: 80px;">L</i><i class="bx bx-italic" style="font-size: 80px; margin-left: -10%; margin-top: 10%;"></i>
        <span class="logo_name">Stock</span>
      </div>
      <ul class="nav-links">
        <li>
          <a href="dashboard.php" class="<?php echo ((basename($_SERVER['PHP_SELF'])=="dashboard.php") || (basename($_SERVER['PHP_SELF'])=="dashb0ard.php")) ? "active":"" ?>">
            <i class="bx bx-grid-alt"></i>
            <span class="links_name">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="fournisseur.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="fournisseur.php" ? "active":"" ?>">
            <i class="bx bx-user"></i>
            <span class="links_name">Fournisseur</span>
          </a>
        </li>
        <li>
          <a href="client.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="client.php" ? "active":"" ?>">
            <i class="bx bx-user-check"></i>
            <span class="links_name">Client</span>
          </a>
        </li>
        <li>
          <a href="categorie.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="categorie.php" ? "active":"" ?>">
            <i class="bx bx-desktop"></i>
            <span class="links_name">Categorie</span>
          </a>
        </li>
        <li>
          <a href="article.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="article.php" ? "active":"" ?>">
            <i class="bx bx-box"></i>
            <span class="links_name">Article</span>
          </a>
        </li> 
        <li>
          <a href="commande.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="commande.php" ? "active":"" ?>">
            <i class="bx bx-list-ul"></i>
            <span class="links_name">Commandes</span>
          </a>
        </li>
        <li>
          <a href="vente.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="vente.php" ? "active":"" ?>">
            <i class="bx bx-coin-stack"></i>
            <span class="links_name">Vente</span>
          </a>
        </li>
        <li>
          <a href="./resultatsRecherche.php">
            <i class="bx bx-search"></i>
            <span class="links_name">Recherche</span>
          </a>
        </li>
        <li class="log_out">
          <a href="./connexionInscription.php">
            <i class="bx bx-log-out"></i>
            <span class="links_name">Déconnexion</span>
          </a>
        </li>
      </ul>
    </div>
    <section class="home-section">
      <!-- Style sur le toogle button et admin name -->
      <style>
        .sidebar-button:hover, .bx, select{
          cursor: pointer !important;
        }
        .dashboard:hover{
          color: #0ff !important;
        }
      </style>
      <nav>
        <div class="sidebar-button">
          <i class="bx bx-menu sidebarBtn"></i>
          <span class="dashboard">
            <?php
              echo ucfirst(str_replace(".php","",basename($_SERVER['PHP_SELF'])));
            ?>
          </span>
        </div>
        
        <div class="profile-details" style="background-color: transparent; border: none;">
          <img src="../public/img/user.png" alt="" style="border-radius: 50%;">
            <span style="color: #ccc; margin: 2px 15px;">
            <?php
              // Vérifiez si le prénom est présent dans la session
              if (isset($_SESSION['prenom'])) {
                  $prenom = $_SESSION['prenom'];
                  echo "$prenom"; // Affiche le prénom de l'utilisateur
              } else {
                  header('Location: ./connexionInscription.php'); // Message de secours si le prénom n'est pas défini
              }
            ?>
            </span>
        </div>
      </nav>



      <?php
  require_once '../model/function.php';
?>

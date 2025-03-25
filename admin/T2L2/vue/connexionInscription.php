<?php
session_start();
?>

<!DOCTYPE html>
<!-- Source de ce formulaire de connexion -->
<!-- Coding by CodingLab || www.codinglabweb.com -->
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login & Registration Form</title>
    <link rel="stylesheet" href="../public/css/stylesCxionInscrpt.css" />
    <!-- Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
  </head>
  <body>
    <!-- Header -->
    <header class="header">
      <nav class="nav">
        <a href="#" class="nav_logo">Inventory management | <span style="color: #555;">By toscani</span></a>
        <button class="button" id="form-open">Login</button>
      </nav>
    </header>

    <!-- Home -->
    <section class="home">
      <div class="form_container">
        <i class="uil uil-times form_close"></i>
        <!-- Login From -->
        <div class="form login_form">
            <form action="../model/connexionUtilisateurs.php" method="POST">
                <h2>Login</h2>

                <div class="input_box">
                    <input type="email" name="email" placeholder="Enter your email" required />
                    <i class="uil uil-envelope-alt email"></i>
                </div>

                <div class="input_box">
                    <input type="password" name="mot_de_passe" placeholder="Enter your password" required />
                    <i class="uil uil-lock password"></i>
                    <i class="uil uil-eye-slash pw_hide"></i>
                </div>

                <div class="option_field">
                    <span class="checkbox">
                        <input type="checkbox" id="check" />
                        <label for="check">Remember me</label>
                    </span>
                    <a href="#" class="forgot_pw">Forgot password?</a>
                </div>

                <button type="submit" class="button">Login Now</button>

                <div class="login_signup">Don't have an account? <a href="#" id="signup">Signup</a></div>
            </form>
        </div>


        <!-- Signup Form -->
        <div class="form signup_form">
          <form action="../model/inscriptionUtilisateurs.php" method="POST">
            <h2>Signup</h2>

            <div class="input_box">
              <input type="text" name="prenom" placeholder="Enter your first name" required />
              <i class="uil uil-user password"></i>
            </div>

            <div class="input_box">
              <input type="email" name="email" placeholder="Enter your email" required />
              <i class="uil uil-envelope-alt email"></i>
            </div>

            <div class="input_box">
              <input type="password" name="mot_de_passe" placeholder="Create password" required />
              <i class="uil uil-lock password"></i>
              <i class="uil uil-eye-slash pw_hide"></i>
            </div>

            <div class="input_box">
              <input type="password" name="confirm_mot_de_passe" placeholder="Confirm password" required />
              <i class="uil uil-lock password"></i>
              <i class="uil uil-eye-slash pw_hide"></i>
            </div>

            <button type="submit" class="button">Signup Now</button>

            <div class="login_signup">Already have an account? <a href="#" id="login">Login</a></div>
          </form>
        </div>
      </div>
    </section>

    <script src="../public/js/scriptsCxionInscript.js"></script>
  </body>
</html>

<?php
if (isset($_GET['erreur']) && $_GET['erreur'] == 1) {
  echo "<p style='color: red;'>Incorrect credentials. Please try again.</p>";
}
?>

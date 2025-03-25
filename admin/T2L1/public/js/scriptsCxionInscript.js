// Sélection des éléments du DOM
const formOpenBtn = document.querySelector("#form-open"),
  home = document.querySelector(".home"),
  formContainer = document.querySelector(".form_container"),
  formCloseBtn = document.querySelector(".form_close"),
  signupBtn = document.querySelector("#signup"),
  loginBtn = document.querySelector("#login"),
  pwShowHide = document.querySelectorAll(".pw_hide");

// Gestionnaire d'événement pour afficher le formulaire
formOpenBtn.addEventListener("click", () => home.classList.add("show"));

// Gestionnaire d'événement pour masquer le formulaire
formCloseBtn.addEventListener("click", () => home.classList.remove("show"));

// Gestionnaire d'événement pour afficher ou masquer le mot de passe
pwShowHide.forEach((icon) => {
  icon.addEventListener("click", () => {
    // Récupération de l'élément input du mot de passe associé à l'icône
    let getPwInput = icon.parentElement.querySelector("input");

    // Vérification du type de l'input du mot de passe et modification en conséquence
    if (getPwInput.type === "password") {
      getPwInput.type = "text";
      icon.classList.replace("uil-eye-slash", "uil-eye");
    } else {
      getPwInput.type = "password";
      icon.classList.replace("uil-eye", "uil-eye-slash");
    }
  });
});

// Gestionnaire d'événement pour activer le formulaire d'inscription
signupBtn.addEventListener("click", (e) => {
  e.preventDefault();
  formContainer.classList.add("active");
});

// Gestionnaire d'événement pour désactiver le formulaire d'inscription
loginBtn.addEventListener("click", (e) => {
  e.preventDefault();
  formContainer.classList.remove("active");
});

// js/register.js
document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll(".tab-btn");
  const formContainer = document.getElementById("form-container");
  const image = document.getElementById("register-illustration");
  const messageContainer = document.getElementById("message-container");

  function loadForm(fileName) {
    const fullUrl = "components/" + fileName;
    const imagePath = fileName.includes("aprendiz")
      ? "imgs/registo.png"
      : "imgs/registo_2.png";
    fetch(fullUrl)
      .then((res) =>
        res.ok
          ? res.text()
          : Promise.reject("Ficheiro não encontrado: " + fullUrl)
      )
      .then((html) => {
        if (messageContainer) messageContainer.innerHTML = "";
        formContainer.innerHTML = html;
        if (image) image.src = imagePath;
      })
      .catch((error) => {
        console.error(error);
        formContainer.innerHTML = `<div class="alert alert-danger">Erro ao carregar o formulário.</div>`;
      });
  }

  buttons.forEach((btn) => {
    btn.addEventListener("click", function () {
      buttons.forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      loadForm(this.dataset.target);
    });
  });

  loadForm("form_aprendiz.php");
});

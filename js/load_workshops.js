document.addEventListener("DOMContentLoaded", function () {
  const resultContainer = document.getElementById("workshop-list");
  const loadMoreBtn = document.getElementById("load-more");
  const categoryButtons = document.querySelectorAll(".category-btn");

  let offset = 0;
  let limit = 8;
  let currentCategory = "";

  function fetchWorkshops(reset = false) {
    const url = `ajax/get_workshops.php?offset=${offset}&limit=${limit}&category=${encodeURIComponent(
      currentCategory
    )}`;

    fetch(url)
      .then((res) => res.text())
      .then((html) => {
        if (reset) {
          resultContainer.innerHTML = html;
        } else {
          resultContainer.insertAdjacentHTML("beforeend", html);
        }

        // Se não houver mais conteúdo, esconder botão
        if (html.trim() === "") {
          loadMoreBtn.style.display = "none";
        } else {
          loadMoreBtn.style.display = "inline-block";
        }
      });
  }

  // Botão "Carregar mais"
  loadMoreBtn.addEventListener("click", () => {
    offset += limit;
    fetchWorkshops();
  });

  // Botões de categoria
  categoryButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      categoryButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentCategory = btn.dataset.category;
      offset = 0;
      fetchWorkshops(true);
    });
  });

  // Carregar primeiro grupo ao abrir
  fetchWorkshops();
});

document.addEventListener("DOMContentLoaded", function () {
  const resultContainer = document.getElementById("workshop-list");
  const loadMoreBtn = document.getElementById("load-more");
  const categoryButtons = document.querySelectorAll(".category-btn");

  // Garante que o script só corre se os elementos existirem na página
  if (!resultContainer || !loadMoreBtn) {
    return;
  }

  let offset = 0;
  const limit = 8;
  let currentCategory = "";

  function fetchWorkshops(reset = false) {
    // CAMINHO CORRIGIDO: Agora aponta para a pasta /api/
    const url = `api/get_workshops.php?offset=${offset}&limit=${limit}&category=${encodeURIComponent(
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

        // Se a resposta HTML estiver vazia, significa que não há mais workshops para carregar
        if (html.trim() === "") {
          loadMoreBtn.style.display = "none";
        } else {
          loadMoreBtn.style.display = "inline-block";
        }
      })
      .catch((error) => {
        console.error("Erro ao carregar workshops:", error);
        resultContainer.innerHTML =
          "<p>Ocorreu um erro ao carregar os workshops.</p>";
      });
  }

  // Listener para o botão "Carregar mais"
  loadMoreBtn.addEventListener("click", () => {
    offset += limit;
    fetchWorkshops();
  });

  // Listener para os botões de categoria
  categoryButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      categoryButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      currentCategory = btn.dataset.category;
      offset = 0; // Reinicia o offset ao mudar de categoria
      fetchWorkshops(true); // O 'true' faz com que a lista seja substituída em vez de adicionada
    });
  });

  // Carrega o primeiro grupo de workshops quando a página abre
  fetchWorkshops(true);
});

document.addEventListener("DOMContentLoaded", () => {
  const contentContainer = document.getElementById("dynamic-content");
  if (!contentContainer) return;

  // Função para carregar o HTML dos formulários
  function loadContent(formFileName) {
    const fullUrl = `components/${formFileName}`;

    fetch(fullUrl)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Formulário não encontrado: ${fullUrl}`);
        }
        return response.text();
      })
      .then((html) => {
        contentContainer.innerHTML = html;
        attachFormListeners();
      })
      .catch((error) => {
        console.error("Erro ao carregar formulário:", error);
        contentContainer.innerHTML = `<div class="alert alert-danger">Ocorreu um erro ao carregar esta secção. Verifique a consola (F12).</div>`;
      });
  }

  // Listener de cliques para os botões do menu
  contentContainer.addEventListener("click", (e) => {
    const targetButton = e.target.closest(".profile-menu-btn");
    if (targetButton && targetButton.dataset.form) {
      loadContent(targetButton.dataset.form);
    }
  });

  // Função que é chamada DEPOIS de um formulário ser carregado na página
  function attachFormListeners() {
    const form = contentContainer.querySelector("form");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const actionUrl = this.action;

        fetch(actionUrl, { method: "POST", body: formData })
          .then((response) => {
            // Se a resposta não for OK (ex: erro 404 ou 500), lança um erro
            if (!response.ok) {
              throw new Error(
                `Erro de rede ou do servidor: ${response.status}`
              );
            }
            return response.json();
          })
          .then((data) => {
            if (data.status === "success") {
              contentContainer.innerHTML = `
                                <div class="success-container">
                                    <div class="success-icon">✓</div>
                                    <h3>${data.message}</h3>
                                    <p><a href="perfil.php">Voltar ao Perfil</a></p>
                                </div>`;
              // Opcional: Forçar recarregamento da página para ver a pfp atualizada na nav
              setTimeout(() => window.location.reload(), 2000);
            } else {
              const errorDiv = form.querySelector(".error-message");
              if (errorDiv) {
                errorDiv.textContent = data.message;
                errorDiv.style.display = "block";
              }
            }
          })
          .catch((error) => {
            // NOVO: Bloco .catch() para apanhar erros de rede ou JSON inválido
            console.error("Erro na submissão do formulário:", error);
            const errorDiv = form.querySelector(".error-message");
            if (errorDiv) {
              errorDiv.textContent =
                "Ocorreu um erro de comunicação. Tente novamente.";
              errorDiv.style.display = "block";
            }
          });
      });

      // Lógica para a pré-visualização da imagem de perfil (sem alterações)
      const pfpInput = form.querySelector("#pfp-input");
      const pfpPreview = form.querySelector("#pfp-preview");
      if (pfpInput && pfpPreview) {
        pfpPreview.closest("label").addEventListener("click", (e) => {
          e.preventDefault();
          pfpInput.click();
        });
        pfpInput.addEventListener("change", (e) => {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
              pfpPreview.src = event.target.result;
            };
            reader.readAsDataURL(file);
          }
        });
      }
    }
  }

  const pfpEditIcon = document.getElementById("pfp-edit-icon-trigger");
  if (pfpEditIcon) {
    pfpEditIcon.addEventListener("click", () => {
      loadContent("form_edit_data.php");
    });
  }
});

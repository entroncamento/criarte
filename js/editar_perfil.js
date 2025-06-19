document.addEventListener("DOMContentLoaded", () => {
  const contentContainer = document.getElementById("dynamic-content");
  if (!contentContainer) return; // Sai se não estiver na página de edição de perfil

  // Função para carregar o HTML dos formulários
  function loadContent(formFileName) {
    fetch(`components/${formFileName}`)
      .then((response) => {
        if (!response.ok)
          throw new Error(`Formulário não encontrado: ${formFileName}`);
        return response.text();
      })
      .then((html) => {
        contentContainer.innerHTML = html;
        // Depois de carregar o formulário, adiciona os listeners a ele
        attachFormSubmitListener();
      })
      .catch((error) => {
        console.error("Erro ao carregar formulário:", error);
        contentContainer.innerHTML = `<div class="alert alert-danger">Ocorreu um erro ao carregar esta secção.</div>`;
      });
  }

  // Função que é chamada DEPOIS de um formulário ser carregado na página
  function attachFormSubmitListener() {
    const form = contentContainer.querySelector("form");
    if (form) {
      // Interceta a submissão do formulário para a fazer com AJAX
      form.addEventListener("submit", function (e) {
        e.preventDefault(); // Impede o recarregamento da página
        const formData = new FormData(this);
        const actionUrl = this.action; // A action vem do HTML do formulário

        fetch(actionUrl, { method: "POST", body: formData })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              // Mostra a mensagem de sucesso
              contentContainer.innerHTML = `
                                <div class="success-container">
                                    <div class="success-icon">✓</div>
                                    <h3>${data.message}</h3>
                                    <p><a href="perfil.php">Voltar ao Perfil</a></p>
                                </div>`;
            } else {
              // Mostra a mensagem de erro
              const errorDiv = form.querySelector(".error-message");
              if (errorDiv) {
                errorDiv.textContent = data.message;
                errorDiv.style.display = "block";
              }
            }
          });
      });

      // Lógica para a pré-visualização da imagem de perfil
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

  // Adiciona o listener de clique aos botões do menu inicial
  contentContainer.addEventListener("click", (e) => {
    const targetButton = e.target.closest(".profile-menu-btn");
    if (targetButton && targetButton.dataset.form) {
      loadContent(targetButton.dataset.form);
    }
  });

  // Adiciona um listener ao ícone de lápis na foto de perfil
  const pfpEditIcon = document.getElementById("pfp-edit-icon-trigger");
  if (pfpEditIcon) {
    pfpEditIcon.addEventListener("click", () => {
      loadContent("form_edit_data.php");
    });
  }
});

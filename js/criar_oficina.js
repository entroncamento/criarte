// js/criar_oficina.js

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-oficina");
  const imageInput = document.getElementById("imagem");
  const previewContainer = document.getElementById("preview-container");
  const uploadLabel = document.querySelector('label[for="imagem"]');
  const materiaisBuilder = document.getElementById(
    "materiais-builder-container"
  );
  const materiaisOutput = document.getElementById("materiais-json-output");
  const popup = document.getElementById("popup-confirm");

  // Função: cria uma secção de materiais
  function criarSecao(nome = "Nova Secção") {
    const secao = document.createElement("div");
    secao.classList.add("materiais-secao", "mb-3");

    const inputNome = document.createElement("input");
    inputNome.type = "text";
    inputNome.placeholder = "Nome da Secção";
    inputNome.className = "form-control mb-2 secao-nome";
    inputNome.value = nome;

    const lista = document.createElement("ul");
    lista.className = "lista-materiais list-group";

    const btnAdd = document.createElement("button");
    btnAdd.type = "button";
    btnAdd.className = "btn btn-sm btn-outline-primary mt-1";
    btnAdd.innerHTML = '<i class="fas fa-plus"></i> Adicionar Material';

    btnAdd.addEventListener("click", () => {
      const li = document.createElement("li");
      li.className = "list-group-item p-1";
      const input = document.createElement("input");
      input.type = "text";
      input.className = "form-control";
      li.appendChild(input);
      lista.appendChild(li);
    });

    secao.appendChild(inputNome);
    secao.appendChild(lista);
    secao.appendChild(btnAdd);
    materiaisBuilder.appendChild(secao);
  }

  // Botão de adicionar secção
  document.getElementById("btn-add-section").addEventListener("click", () => {
    criarSecao();
  });

  // Antes de submeter: gerar JSON de materiais
  form.addEventListener("submit", function (e) {
    const dados = {};
    const secoes = materiaisBuilder.querySelectorAll(".materiais-secao");
    secoes.forEach((secao) => {
      const nome = secao.querySelector(".secao-nome").value.trim();
      if (!nome) return;
      const materiais = [];
      secao.querySelectorAll(".list-group-item input").forEach((input) => {
        if (input.value.trim()) materiais.push(input.value.trim());
      });
      if (materiais.length > 0) dados[nome] = materiais;
    });
    materiaisOutput.value = JSON.stringify(dados);

    if (
      imageInput.files.length === 0 &&
      !form.querySelector('[name="gerar_ai"]')
    ) {
      e.preventDefault();
      if (popup) popup.style.display = "flex";
    }
  });

  // Confirmar popup IA
  window.confirmarGerarImagem = function (confirmado) {
    popup.style.display = "none";
    if (confirmado) {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "gerar_ai";
      input.value = "1";
      form.appendChild(input);
      form.submit();
    }
  };

  // Preview da imagem
  imageInput.addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      if (!file.type.startsWith("image/")) {
        alert("Por favor, selecione um ficheiro de imagem válido.");
        imageInput.value = "";
        return;
      }
      const reader = new FileReader();
      reader.onload = function (event) {
        previewContainer.innerHTML = "";
        const img = document.createElement("img");
        img.src = event.target.result;
        img.className = "image-upload-preview";
        previewContainer.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });
});

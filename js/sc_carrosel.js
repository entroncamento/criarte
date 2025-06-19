document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("carousel-container");
  const leftBtn = document.getElementById("carousel-left");
  const rightBtn = document.getElementById("carousel-right");
  const card = container?.querySelector(".card-custom");

  if (!container || !card) return;

  const cardWidth = card.offsetWidth + 20; // largura + gap
  let scrollAmount = 0;
  let interval;
  let isDragging = false;

  function autoScroll() {
    if (isDragging) return;

    if (scrollAmount + container.clientWidth >= container.scrollWidth - 1) {
      scrollAmount = 0;
    } else {
      scrollAmount += cardWidth;
    }

    container.scrollTo({
      left: scrollAmount,
      behavior: "smooth",
    });
  }

  function startAutoplay() {
    interval = setInterval(autoScroll, 3000);
  }

  function stopAutoplay() {
    clearInterval(interval);
  }

  // Autoplay inicia
  startAutoplay();

  // Pausa se rato estiver por cima
  container.addEventListener("mouseenter", stopAutoplay);
  container.addEventListener("mouseleave", startAutoplay);

  // Scroll com clique e arrasto
  let isDown = false;
  let startX;
  let scrollLeft;

  container.addEventListener("mousedown", (e) => {
    isDown = true;
    isDragging = true;
    container.classList.add("dragging");
    startX = e.pageX - container.offsetLeft;
    scrollLeft = container.scrollLeft;
    stopAutoplay();
  });

  container.addEventListener("mouseleave", () => {
    isDown = false;
    isDragging = false;
    container.classList.remove("dragging");
  });

  container.addEventListener("mouseup", () => {
    isDown = false;
    isDragging = false;
    container.classList.remove("dragging");
    startAutoplay();
  });

  container.addEventListener("mousemove", (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - container.offsetLeft;
    const walk = (x - startX) * 1.5;
    container.scrollLeft = scrollLeft - walk;
  });

  // Botões esquerda/direita
  if (leftBtn) {
    leftBtn.addEventListener("click", () => {
      scrollAmount = Math.max(0, container.scrollLeft - cardWidth);
      container.scrollTo({ left: scrollAmount, behavior: "smooth" });
    });
  }

  if (rightBtn) {
    rightBtn.addEventListener("click", () => {
      scrollAmount = container.scrollLeft + cardWidth;
      container.scrollTo({ left: scrollAmount, behavior: "smooth" });
    });
  }
});

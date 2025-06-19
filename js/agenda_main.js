document.addEventListener("DOMContentLoaded", () => {
  const agendaSection = document.querySelector(".agenda-section");
  if (!agendaSection) return;

  let currentStartDate = new Date();

  function formatDate(date) {
    return date.toISOString().split("T")[0];
  }

  function loadAgenda(startDate) {
    // Pede a vista de 'semana' e o contexto 'público' à nossa API
    const apiUrl = `api/get_agenda_days.php?start=${formatDate(
      startDate
    )}&view=week&context=public`;

    fetch(apiUrl)
      .then((res) => {
        if (!res.ok) {
          throw new Error(`Erro na API: ${res.status}`);
        }
        return res.json();
      })
      .then((data) => {
        const dayContainer = document.querySelector(".day-selector");
        if (!dayContainer) return;
        dayContainer.innerHTML = "";

        // Mostra o mês e ano no título
        document.querySelector(".month-label").textContent = data.currentMonth;

        // Cria os botões dos dias
        data.days.forEach((dayData) => {
          const div = document.createElement("div");
          div.className = "day-item";
          div.dataset.date = dayData.date;
          div.innerHTML = `<div class="day-number">${dayData.day}</div><div class="day-name">${dayData.weekday}</div>`;

          div.onclick = () => {
            sessionStorage.setItem("selectedDate", dayData.date);
            showEventsForDate(dayData.date, data.events || {});
          };
          dayContainer.appendChild(div);
        });

        const savedDate = sessionStorage.getItem("selectedDate");
        const todayStr = formatDate(new Date());
        const isDateVisible = data.days.some((d) => d.date === savedDate);
        const dateToShow = isDateVisible ? savedDate : todayStr;

        showEventsForDate(dateToShow, data.events || {});
      })
      .catch((error) => {
        console.error("Falha ao carregar a agenda:", error);
        document.getElementById("agenda-lista").innerHTML =
          '<div class="agenda-card">Não foi possível carregar os eventos.</div>';
      });
  }

  function showEventsForDate(date, allEvents) {
    const eventContainer = document.getElementById("agenda-lista");
    const titleContainer = document.getElementById("agenda-dia-selecionado");
    if (!eventContainer || !titleContainer) return;

    const isToday = date === formatDate(new Date());
    titleContainer.textContent = isToday
      ? "Hoje"
      : new Date(date + "T00:00:00").toLocaleDateString("pt-PT", {
          day: "numeric",
          month: "long",
        });

    const eventsForDay = allEvents[date] || [];
    if (eventsForDay.length === 0) {
      eventContainer.innerHTML =
        '<div class="agenda-card">Sem workshops para este dia.</div>';
    } else {
      eventContainer.innerHTML = "";
      eventsForDay.forEach((eventTitle) => {
        const div = document.createElement("div");
        div.className = "agenda-card";
        div.textContent = eventTitle;
        eventContainer.appendChild(div);
      });
    }

    document
      .querySelectorAll(".day-item")
      .forEach((d) => d.classList.remove("selected"));
    const dayElementToSelect = document.querySelector(
      `.day-item[data-date='${date}']`
    );
    if (dayElementToSelect) {
      dayElementToSelect.classList.add("selected");
    }
  }

  function adjustWeek(days) {
    currentStartDate.setDate(currentStartDate.getDate() + days);
    loadAgenda(currentStartDate);
  }

  document.getElementById("btn-week-prev").onclick = () => adjustWeek(-7);
  document.getElementById("btn-week-next").onclick = () => adjustWeek(7);
  document.getElementById("btn-today").onclick = () => {
    currentStartDate = new Date();
    sessionStorage.setItem("selectedDate", formatDate(currentStartDate));
    loadAgenda(currentStartDate);
  };

  loadAgenda(currentStartDate);
});

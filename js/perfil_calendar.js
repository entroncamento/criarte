document.addEventListener("DOMContentLoaded", () => {
  // CORREÇÃO: O seletor agora procura por '.page-wrapper .agenda', que é o contentor correto no seu perfil.php limpo
  const calendarContainer = document.querySelector(".page-wrapper .agenda");
  if (!calendarContainer) {
    return; // Se não estivermos numa página com esta agenda, o script para.
  }

  let currentMonthOffset = 0;
  let selectedDate = new Date().toISOString().split("T")[0];
  const calendarBody = document.getElementById("calendar-body");
  const eventList = document.getElementById("event-list");
  const monthLabel = document.getElementById("month-label");

  // Função para renderizar o calendário com os dados recebidos
  function renderCalendar(data) {
    // Define o título do mês/ano
    const refDate = new Date(data.days[15].date + "T00:00:00");
    const month = refDate.toLocaleString("pt-PT", { month: "long" });
    const year = refDate.getFullYear();
    monthLabel.textContent = `${
      month.charAt(0).toUpperCase() + month.slice(1)
    } ${year}`;

    let tableHtml = ""; // Constrói o HTML numa string para ser mais eficiente
    let week = [];

    // Adiciona células vazias para os dias antes do início do mês
    const firstDayOfWeek = new Date(data.days[0].date + "T00:00:00").getDay();
    for (let i = 0; i < firstDayOfWeek; i++) {
      week.push("<td></td>");
    }

    // Preenche os dias do calendário
    data.days.forEach((day) => {
      const isSelected = day.date === selectedDate ? "selected" : "";
      const hasEvent = data.events && data.events[day.date] ? "event-day" : "";
      const dayHtml = `
                <td>
                    <div class="day-btn ${isSelected} ${hasEvent}" data-date="${day.date}">
                        ${day.day}
                    </div>
                </td>`;
      week.push(dayHtml);

      // Quando a semana está completa (7 dias), cria a linha da tabela
      if (week.length === 7) {
        tableHtml += `<tr>${week.join("")}</tr>`;
        week = []; // Esvazia a semana para a próxima linha
      }
    });

    // "Desenha" o calendário na página de uma só vez
    calendarBody.innerHTML = tableHtml;
    // Adiciona os eventos de clique aos novos botões
    attachDayClickListeners(data.events);
    // Mostra os eventos para o dia já selecionado
    showEvents(
      selectedDate,
      data.events && data.events[selectedDate] ? data.events[selectedDate] : []
    );
  }

  // Adiciona a funcionalidade de clique a cada dia do calendário
  function attachDayClickListeners(events) {
    document.querySelectorAll(".day-btn").forEach((btn) => {
      btn.onclick = () => {
        selectedDate = btn.dataset.date;
        showEvents(
          btn.dataset.date,
          events && events[btn.dataset.date] ? events[btn.dataset.date] : []
        );
        document
          .querySelectorAll(".day-btn")
          .forEach((b) => b.classList.remove("selected"));
        btn.classList.add("selected");
      };
    });
  }

  // Busca os dados do calendário à API
  function fetchCalendar() {
    const target = new Date();
    target.setDate(1);
    target.setMonth(target.getMonth() + currentMonthOffset);
    const formatted = target.toISOString().split("T")[0];

    // A API agora usa o contexto 'user' por defeito, que é o correto para o perfil
    fetch(`api/get_agenda_days.php?start=${formatted}&view=month`)
      .then((res) => res.json())
      .then((data) => renderCalendar(data))
      .catch((error) => console.error("Erro ao carregar o calendário:", error));
  }

  // Mostra os eventos para o dia selecionado
  function showEvents(date, eventos) {
    eventList.innerHTML = "";
    if (!eventos || eventos.length === 0) {
      eventList.innerHTML =
        '<div class="agenda-card">Sem workshops neste dia.</div>';
    } else {
      eventos.forEach((e) => {
        eventList.innerHTML += `<div class="agenda-card">${e}</div>`;
      });
    }
  }

  // Configuração inicial e botões de navegação
  fetchCalendar();
  document.getElementById("prev-month").onclick = () => {
    currentMonthOffset--;
    fetchCalendar();
  };
  document.getElementById("next-month").onclick = () => {
    currentMonthOffset++;
    fetchCalendar();
  };
});

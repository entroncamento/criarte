document.addEventListener("DOMContentLoaded", () => {
  // Procura o contentor da agenda. Se não o encontrar, o script não executa.
  const calendarContainer = document.querySelector(".page-wrapper .agenda");
  if (!calendarContainer) return;

  let currentMonthOffset = 0;
  let selectedDate = new Date().toISOString().split("T")[0];
  const calendarBody = document.getElementById("calendar-body");
  const eventList = document.getElementById("event-list");
  const monthLabel = document.getElementById("month-label");

  function renderCalendar(data) {
    const refDate = new Date(data.days[15].date + "T00:00:00");
    const month = refDate.toLocaleString("pt-PT", { month: "long" });
    const year = refDate.getFullYear();
    monthLabel.textContent = `${
      month.charAt(0).toUpperCase() + month.slice(1)
    } ${year}`;
    calendarBody.innerHTML = "";

    let weekHtml = "<tr>";
    const firstDayOfWeek = new Date(data.days[0].date + "T00:00:00").getDay();
    for (let i = 0; i < firstDayOfWeek; i++) {
      weekHtml += "<td></td>";
    }

    data.days.forEach((day, index) => {
      const isSelected = day.date === selectedDate ? "selected" : "";
      const hasEvent = data.events && data.events[day.date] ? "event-day" : "";
      weekHtml += `<td><div class="day-btn ${isSelected} ${hasEvent}" data-date="${day.date}">${day.day}</div></td>`;

      if ((index + firstDayOfWeek + 1) % 7 === 0) {
        weekHtml += "</tr><tr>";
      }
    });
    weekHtml += "</tr>";
    calendarBody.innerHTML = weekHtml;

    attachDayClickListeners(data.events);
    showEvents(
      selectedDate,
      data.events && data.events[selectedDate] ? data.events[selectedDate] : []
    );
  }

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

  function fetchCalendar() {
    const target = new Date();
    target.setDate(1);
    target.setMonth(target.getMonth() + currentMonthOffset);
    const formatted = target.toISOString().split("T")[0];

    fetch(`api/get_agenda_days.php?start=${formatted}&view=month&context=user`)
      .then((res) => res.json())
      .then((data) => renderCalendar(data));
  }

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

document.addEventListener("DOMContentLoaded", () => {
  // --- MÓDULO DE NOTIFICAÇÕES ---
  // Esta parte só executa se os elementos do sino existirem na página
  function initializeNotificationSystem() {
    const notificationBadge = document.getElementById("notification-badge");
    const notificationDropdownList = document.getElementById(
      "notification-dropdown-list"
    );
    const notificationBell = document.getElementById("notification-bell");

    if (!notificationBadge || !notificationDropdownList || !notificationBell) {
      return; // Se não encontrar os elementos, não faz nada
    }

    function fetchNotifications() {
      fetch("api/check_notifications.php")
        .then((response) => response.json())
        .then((data) => {
          if (!data || typeof data.notifications === "undefined") return;

          if (data.count > 0) {
            notificationBadge.textContent = data.count;
            notificationBadge.style.display = "block";
          } else {
            notificationBadge.style.display = "none";
          }

          notificationDropdownList.innerHTML = "";
          if (data.notifications.length > 0) {
            data.notifications.forEach((notif) => {
              let iconClass = "fa-info-circle";
              if (notif.type === "message") iconClass = "fa-comment";
              if (notif.type === "workshop_reminder")
                iconClass = "fa-calendar-check";
              let link =
                notif.type === "message" && notif.related_id
                  ? `mensagens.php?user=${notif.related_id}`
                  : "#";

              const li = document.createElement("li");
              li.innerHTML = `
                                <a class="dropdown-item d-flex align-items-start py-2" href="${link}">
                                    <i class="fas ${iconClass} mt-1 me-3 text-muted" style="width: 20px;"></i>
                                    <div class="notification-content">
                                        <span class="message">${
                                          notif.conteudo
                                        }</span>
                                        <div class="timestamp">${new Date(
                                          notif.criada_em
                                        ).toLocaleString("pt-PT", {
                                          dateStyle: "short",
                                          timeStyle: "short",
                                        })}</div>
                                    </div>
                                </a>`;
              notificationDropdownList.appendChild(li);
            });
            notificationDropdownList.innerHTML += `
                            <li><hr class="dropdown-divider m-0"></li>
                            <li class="notification-footer"><a class="dropdown-item text-center small" href="#" id="dismiss-all-notifs-btn">Marcar todas como lidas</a></li>
                        `;
          } else {
            notificationDropdownList.innerHTML =
              '<li><p class="text-center text-muted small my-3">Nenhuma notificação nova.</p></li>';
          }
        });
    }

    function markAsRead() {
      notificationBadge.style.display = "none";
      fetch("api/mark_notifications_read.php", { method: "POST" }).then(() => {
        setTimeout(fetchNotifications, 500);
      });
    }

    notificationDropdownList.addEventListener("click", (e) => {
      if (e.target.closest("#dismiss-all-notifs-btn")) {
        e.preventDefault();
        markAsRead();
      }
    });

    fetchNotifications();
    setInterval(fetchNotifications, 15000);
  }

  // --- MÓDULO DE PING DE ATIVIDADE ---
  // Esta parte só executa se o utilizador estiver logado (verificando se o menu dropdown do perfil existe)
  function initializeActivityPing() {
    const profileDropdown = document.getElementById("profileDropdown");
    if (!profileDropdown) {
      return;
    }

    // Envia um sinal de "estou online" para o servidor a cada 30 segundos
    setInterval(() => {
      fetch("api/update_activity.php", { method: "POST" });
    }, 30000);
  }

  // --- INICIALIZAÇÃO DOS MÓDULOS ---
  initializeNotificationSystem();
  initializeActivityPing();
});

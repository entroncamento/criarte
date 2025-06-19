<?php
session_start();
include_once 'connections/connection.php';
include 'components/cp_nav.php'; // Navbar no topo

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new_db_connection();
$user_id = $_SESSION['user_id'];

// Buscar dados do utilizador para o sidebar
$stmt_user = mysqli_prepare($conn, "SELECT username, pfp FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
mysqli_stmt_bind_result($stmt_user, $username, $pfp);
mysqli_stmt_fetch($stmt_user);
mysqli_stmt_close($stmt_user);

// Buscar aulas realizadas
$stmt_aulas = mysqli_prepare($conn, "SELECT w.id, w.title, w.event_date FROM workshops w JOIN user_workshops uw ON w.id = uw.workshop_id WHERE uw.user_id = ? AND w.event_date < CURDATE() ORDER BY w.event_date DESC");
mysqli_stmt_bind_param($stmt_aulas, "i", $user_id);
mysqli_stmt_execute($stmt_aulas);
$result_aulas = mysqli_stmt_get_result($stmt_aulas);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Perfil - <?= htmlspecialchars($username) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="page-wrapper">
        <?php include 'components/cp_sidebar.php'; ?>

        <main class="content-area">
            <h3 class="fw-bold mb-4">Aulas realizadas</h3>
            <?php if (mysqli_num_rows($result_aulas) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_aulas)):
                    $data = strtotime($row['event_date']);
                    setlocale(LC_TIME, 'pt_PT.utf8');
                ?>
                    <div class="aula-card">
                        <div class="data-box">
                            <div class="dia"><?= date('d', $data) ?></div>
                            <div class="mes"><?= strftime('%b', $data) ?></div>
                        </div>
                        <div class="info flex-grow-1">
                            <div class="titulo"><?= htmlspecialchars($row['title']) ?></div>
                            <a class="classificar" href="avaliar_workshop.php?id=<?= $row['id'] ?>">Classificar aula</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">Ainda não frequentou nenhuma aula.</p>
            <?php endif; ?>
        </main>

        <aside class="agenda">
            <h4 class="fw-bold">A tua Agenda</h4>
            <div class="nav-months">
                <button id="prev-month" class="nav-btn">&#8249;</button>
                <span class="month-label" id="month-label"></span>
                <button id="next-month" class="nav-btn">&#8250;</button>
            </div>
            <table class="table table-sm table-borderless">
                <thead>
                    <tr>
                        <th>Dom</th>
                        <th>Seg</th>
                        <th>Ter</th>
                        <th>Qua</th>
                        <th>Qui</th>
                        <th>Sex</th>
                        <th>Sáb</th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>
            <div id="event-list" class="mt-3"></div>
        </aside>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/perfil_calendar.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
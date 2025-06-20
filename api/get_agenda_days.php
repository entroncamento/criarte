<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
require_once '../connections/connection.php';

$conn = new_db_connection();

$view = $_GET['view'] ?? 'month';
$context = $_GET['context'] ?? 'user';

try {
    $baseDate = isset($_GET['start']) ? new DateTime($_GET['start']) : new DateTime();
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato de data inválido.']);
    exit;
}

$refDateForTitle = new DateTime($baseDate->format('Y-m-d'));
$days_to_generate = ($view === 'week') ? 7 : 35;

if ($view === 'week') {
    if ($baseDate->format('w') != 0) {
        $baseDate->modify('last sunday');
    }
} else {
    $baseDate->modify('first day of this month');
    if ($baseDate->format('w') != 0) {
        $baseDate->modify('last sunday');
    }
}

$startDateFormatted = $baseDate->format('Y-m-d');
$endDateFormatted = (clone $baseDate)->modify('+' . ($days_to_generate - 1) . ' days')->format('Y-m-d');

$weekdayMap = ['Sun' => 'Dom', 'Mon' => 'Seg', 'Tue' => 'Ter', 'Wed' => 'Qua', 'Thu' => 'Qui', 'Fri' => 'Sex', 'Sat' => 'Sáb'];
$monthMap = ['Jan' => 'Jan', 'Feb' => 'Fev', 'Mar' => 'Mar', 'Apr' => 'Abr', 'May' => 'Maio', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ago', 'Sep' => 'Set', 'Oct' => 'Out', 'Nov' => 'Nov', 'Dec' => 'Dez'];

$days = [];
for ($i = 0; $i < $days_to_generate; $i++) {
    $date = $baseDate->format('Y-m-d');
    $days[] = [
        'date' => $date,
        'day' => $baseDate->format('d'),
        'weekday' => $weekdayMap[$baseDate->format('D')],
        'month' => $monthMap[$baseDate->format('M')],
        'isToday' => ($date == date('Y-m-d'))
    ];
    $baseDate->modify('+1 day');
}

$dateRangeTitle = "";
$titleDate = ($view === 'week') ? $refDateForTitle : new DateTime($days[15]['date']);
$monthName = $monthMap[$titleDate->format('M')];
$year = $titleDate->format('Y');
$dateRangeTitle = ucfirst($monthName) . ' ' . $year;

$events = [];
$stmt = null;

if ($context === 'public') {
    $sql = "SELECT event_date, title FROM workshops WHERE estado = 'aceite' AND event_date BETWEEN ? AND ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $startDateFormatted, $endDateFormatted);
} elseif ($context === 'user' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT a.data AS event_date, a.titulo AS title 
            FROM aulas a
            JOIN user_workshops uw ON a.workshop_id = uw.workshop_id
            WHERE uw.user_id = ? AND a.data BETWEEN ? AND ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $startDateFormatted, $endDateFormatted);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Contexto inválido ou utilizador não autenticado.']);
    exit;
}

if ($stmt && mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $date_key = substr($row['event_date'], 0, 10);
            if (!isset($events[$date_key])) $events[$date_key] = [];
            $events[$date_key][] = $row['title'];
        }
    }
    mysqli_stmt_close($stmt);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na base de dados.']);
    exit;
}

echo json_encode(['days' => $days, 'events' => $events, 'currentMonth' => $dateRangeTitle]);
mysqli_close($conn);

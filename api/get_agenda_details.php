<?php
// Garante que a sessão é iniciada (se já não estiver)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// CORREÇÃO CRÍTICA: Inclui e inicializa a conexão com a base de dados
include_once '../connections/connection.php';
$conn = new_db_connection();

// Verifica se o utilizador está autenticado, pois esta query precisa do ID dele
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Devolve "Não autorizado"
    // Envia um array vazio para o JavaScript não quebrar
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
// Obtém a data da URL ou usa a data de hoje por defeito
$date = $_GET['date'] ?? date('Y-m-d');

// Validação simples da data
if (!DateTime::createFromFormat('Y-m-d', $date)) {
    http_response_code(400); // Devolve "Pedido Inválido"
    echo json_encode(['error' => 'Formato de data inválido']);
    exit;
}

// Query para buscar os workshops do utilizador para uma data específica
$sql = "SELECT a.titulo AS title
        FROM aulas a
        INNER JOIN user_workshops uw ON a.workshop_id = uw.workshop_id
        WHERE uw.user_id = ? AND DATE(a.data) = ?";



$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $user_id, $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$aulas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $aulas[] = $row; // já tem 'title'
}



mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode($aulas);

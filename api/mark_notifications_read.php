<?php
session_start();
require_once "../connections/connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = new_db_connection();

// Query para atualizar a coluna 'lida' na tabela 'notificacoes'
$query = "UPDATE notificacoes SET lida = 1 WHERE user_id = ? AND lida = 0";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);

// Usamos if/else para uma verificação de erros mais robusta
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Falha ao atualizar as notificações.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

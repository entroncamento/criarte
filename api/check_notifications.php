<?php
session_start();
require_once '../connections/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['notifications' => [], 'count' => 0]);
    exit;
}

$conn = new_db_connection();
$user_id = $_SESSION['user_id'];

// Query para buscar todas as notificações não lidas, usando os nomes de colunas corretos
$query = "SELECT id, type, conteudo, criada_em, related_id 
          FROM notificacoes 
          WHERE user_id = ? AND lida = 0 
          ORDER BY criada_em DESC";

$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    echo json_encode(['notifications' => $notifications, 'count' => count($notifications)]);
} else {
    // Em caso de erro, devolve uma resposta vazia e segura
    echo json_encode(['notifications' => [], 'count' => 0]);
}

mysqli_close($conn);

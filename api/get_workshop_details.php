<?php
session_start();
require_once "../connections/connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'Acesso negado.']));
}

$conn = new_db_connection();
$workshop_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Query que busca os detalhes do workshop, mas garante que pertence ao utilizador logado
$query = "SELECT title, description, publico, duracao, preco, materiais 
          FROM workshops 
          WHERE id = ? AND creator_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $workshop_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$workshop = mysqli_fetch_assoc($result);
mysqli_close($conn);

if ($workshop) {
    echo json_encode($workshop);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Workshop não encontrado ou não tem permissão para o editar.']);
}

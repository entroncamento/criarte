<?php
session_start();
require_once "../connections/connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['workshop_id'])) {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Acesso negado ou dados em falta.']));
}

$conn = new_db_connection();
$user_id = $_SESSION['user_id'];
$workshop_id = $_POST['workshop_id'];

// Recolhe e valida os dados do formulário
$publico = trim($_POST['publico'] ?? '');
$duracao = trim($_POST['duracao'] ?? '');
$preco = $_POST['preco'] ?? 0.0;
$materiais_texto = trim($_POST['materiais'] ?? '');
// Reutiliza a nossa função para converter texto em JSON
$materiais_json = parseMateriaisToJson($materiais_texto);

// A query só atualiza os workshops que pertencem ao utilizador logado
$query = "UPDATE workshops 
          SET publico = ?, duracao = ?, preco = ?, materiais = ? 
          WHERE id = ? AND creator_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssdssi", $publico, $duracao, $preco, $materiais_json, $workshop_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success', 'message' => 'Workshop atualizado com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao guardar as alterações.']);
}
mysqli_close($conn);

// A função que já tínhamos para converter os materiais
function parseMateriaisToJson($text)
{ /* ... */
}

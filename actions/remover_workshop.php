<?php
session_start();
require_once('../connections/connection.php');

// --- Verificação de Segurança ---
// 1. Garante que o pedido é um POST
// 2. Garante que o utilizador está logado
// 3. Garante que o utilizador é um Artesão
// 4. Garante que foi enviado um ID de workshop
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || ($_SESSION['tipo'] ?? '') !== 'Artesão' || !isset($_POST['id'])) {
    // Se alguma verificação falhar, redireciona sem fazer nada
    header("Location: ../gestao_oficinas.php?error=request_failed");
    exit;
}

$conn = new_db_connection();

$workshop_id_to_delete = $_POST['id'];
$creator_id = $_SESSION['user_id'];

// --- Lógica de Remoção Segura ---

// A query DELETE tem duas condições no WHERE:
// 1. O ID do workshop tem de ser o que veio do formulário.
// 2. O creator_id tem de ser o do utilizador logado.
// Isto impede que um artesão apague workshops de outro.
$query = "DELETE FROM workshops WHERE id = ? AND creator_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $workshop_id_to_delete, $creator_id);

if (mysqli_stmt_execute($stmt)) {
    // mysqli_stmt_affected_rows devolve o número de linhas que foram apagadas.
    // Se for 1, significa que o workshop foi apagado com sucesso.
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $status = "delete_success";
    } else {
        // Se for 0, significa que a query executou mas não apagou nada (provavelmente porque o workshop não pertencia a este artesão).
        $status = "delete_error_permission";
    }
} else {
    // Se a query falhar por um erro de SQL.
    $status = "delete_error_sql";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Redireciona de volta para a página de gestão com uma mensagem de estado
header("Location: ../perfil.php?status=$status");
exit;

<?php
session_start();
require_once("../connections/connection.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['status' => 'error', 'message' => 'Todos os campos são obrigatórios.']);
    exit;
}
if (strlen($new_password) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'A nova password deve ter pelo menos 8 caracteres.']);
    exit;
}
if ($new_password !== $confirm_password) {
    echo json_encode(['status' => 'error', 'message' => 'As novas palavras-passe não coincidem.']);
    exit;
}

$conn = new_db_connection();
$stmt = mysqli_prepare($conn, "SELECT password_hash FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $current_hash);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (password_verify($current_password, $current_hash)) {
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt_update = mysqli_prepare($conn, "UPDATE users SET password_hash = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt_update, "si", $new_hash, $user_id);
    mysqli_stmt_execute($stmt_update);

    echo json_encode(['status' => 'success', 'message' => 'Palavra-passe alterada com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'A sua palavra-passe atual está incorreta.']);
}
mysqli_close($conn);

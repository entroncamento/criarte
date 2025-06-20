<?php
session_start();
require_once("../connections/connection.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['status' => 'error', 'message' => 'Acesso negado.']));
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if (empty($current_password) || empty($new_password) || empty($confirm_password) || strlen($new_password) < 8 || $new_password !== $confirm_password) {
    exit(json_encode(['status' => 'error', 'message' => 'Por favor, verifique os dados inseridos.']));
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

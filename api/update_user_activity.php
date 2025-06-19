<?php
session_start();
require_once "../connections/connection.php";

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit();
}

$user_id = $_SESSION['user_id'];
$recipient_id = $_POST['recipient_id'] ?? 0;
$activity_type = $_POST['activity_type'] ?? 'clear'; // 'typing', 'recording', ou 'clear'

$conn = new_db_connection();

if ($activity_type === 'clear') {
    // Apaga o estado de atividade do utilizador para este recipiente
    $query = "DELETE FROM user_activity WHERE user_id = ? AND recipient_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $recipient_id);
} else {
    // Insere ou atualiza o estado de atividade
    $query = "INSERT INTO user_activity (user_id, recipient_id, activity_type, activity_timestamp) 
              VALUES (?, ?, ?, NOW())
              ON DUPLICATE KEY UPDATE activity_type = ?, activity_timestamp = NOW()";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiss", $user_id, $recipient_id, $activity_type, $activity_type);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode(['status' => 'success']);

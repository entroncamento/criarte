<?php
session_start();
require_once '../connections/connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['partner_id'])) {
    echo json_encode(['messages' => [], 'partner_status' => null]);
    exit;
}

$conn = new_db_connection();
$my_id = $_SESSION['user_id'];
$partner_id = $_GET['partner_id'];

// Marcar mensagens como lidas
$query_update = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
$stmt_update = mysqli_prepare($conn, $query_update);
mysqli_stmt_bind_param($stmt_update, "ii", $partner_id, $my_id);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);

// Buscar histórico de mensagens
$query_msg = "SELECT id, sender_id, message_content, timestamp, message_type, is_read 
              FROM messages 
              WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
              ORDER BY timestamp ASC";
$stmt_msg = mysqli_prepare($conn, $query_msg);
mysqli_stmt_bind_param($stmt_msg, "iiii", $my_id, $partner_id, $partner_id, $my_id);
mysqli_stmt_execute($stmt_msg);
$result_msg = mysqli_stmt_get_result($stmt_msg);
$messages = mysqli_fetch_all($result_msg, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_msg);

// Buscar estado de atividade do parceiro
$partner_status = null;
$query_status = "SELECT activity_type FROM user_activity 
                 WHERE user_id = ? AND recipient_id = ? AND activity_timestamp > NOW() - INTERVAL 3 SECOND";
$stmt_status = mysqli_prepare($conn, $query_status);
mysqli_stmt_bind_param($stmt_status, "ii", $partner_id, $my_id);
mysqli_stmt_execute($stmt_status);
mysqli_stmt_bind_result($stmt_status, $activity_type);
if (mysqli_stmt_fetch($stmt_status)) {
    $partner_status = $activity_type;
}
mysqli_stmt_close($stmt_status);

// Devolve o JSON apenas com as mensagens e o estado
echo json_encode(['messages' => $messages, 'partner_status' => $partner_status]);

mysqli_close($conn);

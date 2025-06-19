<?php
session_start();
require_once "../connections/connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['receiver_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit;
}

$conn = new_db_connection();

$sender_id = $_SESSION['user_id'];
$sender_username = $_SESSION['username'];
$receiver_id = $_POST['receiver_id'];
$message_content = trim($_POST['message_content'] ?? '');
$message_type = 'text';

// Lógica para upload de ficheiros
$file_uploaded = false;
if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
    $target_dir = "../uploads/images/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $file_info = pathinfo($_FILES["image_file"]["name"]);
    $unique_name = 'img_' . uniqid() . '.' . strtolower($file_info['extension']);
    if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_dir . $unique_name)) {
        $message_content = 'uploads/images/' . $unique_name;
        $message_type = 'image';
        $file_uploaded = true;
    }
} elseif (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
    $target_dir = "../uploads/audio/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $unique_name = 'audio_' . uniqid() . '.webm';
    if (move_uploaded_file($_FILES["audio_file"]["tmp_name"], $target_dir . $unique_name)) {
        $message_content = 'uploads/audio/' . $unique_name;
        $message_type = 'audio';
        $file_uploaded = true;
    }
}

if (empty($message_content)) {
    echo json_encode(['status' => 'error', 'message' => 'Mensagem vazia.']);
    exit;
}

// 1. Insere a mensagem na tabela 'messages'
$query_msg = "INSERT INTO messages (sender_id, receiver_id, message_content, message_type) VALUES (?, ?, ?, ?)";
$stmt_msg = mysqli_prepare($conn, $query_msg);
mysqli_stmt_bind_param($stmt_msg, "iiss", $sender_id, $receiver_id, $message_content, $message_type);

if (mysqli_stmt_execute($stmt_msg)) {
    mysqli_stmt_close($stmt_msg);

    // 2. Se a mensagem foi guardada, cria a notificação
    $notification_message = "Tem uma nova mensagem de " . htmlspecialchars($sender_username) . ".";
    $notification_type = 'message';
    $related_id = $sender_id;

    $query_notif = "INSERT INTO notificacoes (user_id, type, conteudo, related_id) VALUES (?, ?, ?, ?)";
    $stmt_notif = mysqli_prepare($conn, $query_notif);
    mysqli_stmt_bind_param($stmt_notif, "issi", $receiver_id, $notification_type, $notification_message, $related_id);

    if (mysqli_stmt_execute($stmt_notif)) {
        // Sucesso em ambas as operações
        echo json_encode(['status' => 'success']);
    } else {
        // Mensagem enviada, mas notificação falhou
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Mensagem enviada, mas falha ao notificar.']);
    }
    mysqli_stmt_close($stmt_notif);
} else {
    // Falha ao guardar a mensagem principal
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Falha ao guardar a mensagem.']);
    mysqli_stmt_close($stmt_msg);
}

mysqli_close($conn);

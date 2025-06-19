<?php
// Define o fuso horário para Portugal
date_default_timezone_set('Europe/Lisbon');

session_start();
require_once "../connections/connection.php";

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = new_db_connection();

// Atualiza o timestamp da última atividade para agora
$query = "UPDATE users SET last_activity = NOW() WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

require_once '../connections/connection.php';
$conn = new_db_connection();

$workshop_id = $_POST['workshop_id'];
$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$data = $_POST['data'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim = $_POST['hora_fim'];

$stmt = mysqli_prepare($conn, "INSERT INTO aulas (workshop_id, titulo, descricao, data, hora_inicio, hora_fim) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "isssss", $workshop_id, $titulo, $descricao, $data, $hora_inicio, $hora_fim);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../aulas.php?workshop_id=$workshop_id");
} else {
    echo "Erro ao adicionar aula: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

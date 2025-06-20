<?php
session_start();
require_once("../connections/connection.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['status' => 'error', 'message' => 'Acesso negado.']));
}

$user_id = $_SESSION['user_id'];
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$pfp_path_to_db = null;

if (isset($_FILES['pfp_file']) && $_FILES['pfp_file']['error'] == 0) {
    $target_dir = "../uploads/profiles/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_info = pathinfo($_FILES["pfp_file"]["name"]);
    $unique_name = 'pfp_' . $user_id . '_' . time() . '.' . strtolower($file_info['extension']);
    $target_file = $target_dir . $unique_name;

    if (move_uploaded_file($_FILES["pfp_file"]["tmp_name"], $target_file)) {
        $pfp_path_to_db = 'uploads/profiles/' . $unique_name;
    }
}

$conn = new_db_connection();
$query = "UPDATE users SET username = ?, email = ?";
$params_types = "ss";
$params_vars = [$username, $email];

if ($pfp_path_to_db !== null) {
    $query .= ", pfp = ?";
    $params_types .= "s";
    $params_vars[] = $pfp_path_to_db;
}

$query .= " WHERE id = ?";
$params_types .= "i";
$params_vars[] = $user_id;

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $params_types, ...$params_vars);

if (mysqli_stmt_execute($stmt)) {
    if ($pfp_path_to_db !== null) {
        $_SESSION['pfp'] = $pfp_path_to_db;
    }
    $_SESSION['username'] = $username;
    echo json_encode(['status' => 'success', 'message' => 'Perfil editado com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro: O email ou username pode já estar em uso.']);
}
mysqli_close($conn);

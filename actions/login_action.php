<?php
session_start();
require_once("../connections/connection.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$username = trim($_POST['login'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Por favor, preencha todos os campos.";
    header("Location: ../login.php");
    exit;
}

$conn = new_db_connection();
$stmt = mysqli_stmt_init($conn);

// Query ATUALIZADA: agora também seleciona a coluna `tipo`
$query = "SELECT id, username, password_hash, pfp, tipo FROM users WHERE username = ?";

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    // Adicionamos $db_tipo para receber o valor
    mysqli_stmt_bind_result($stmt, $id, $db_username, $db_password_hash, $db_pfp, $db_tipo);

    if (mysqli_stmt_fetch($stmt)) {
        if (password_verify($password, $db_password_hash)) {

            // Sucesso no login
            unset($_SESSION['login_error']);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['pfp'] = $db_pfp;
            // A LINHA MAIS IMPORTANTE: Guardamos o tipo na sessão
            $_SESSION['tipo'] = $db_tipo;

            header("Location: ../index.php");
            exit;
        }
    }
}

// Se chegou aqui, o login falhou por algum motivo
$_SESSION['login_error'] = "Nome de utilizador ou palavra-passe incorreta.";
header("Location: ../login.php");
exit;

<?php
session_start();
require_once("../connections/connection.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit;
}

// --- Recolher dados do formulário ---
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$tipo = $_POST['tipo'] ?? ''; // 'Aprendiz' ou 'Artesão'
$localizacao = trim($_POST['localizacao'] ?? null);
$hobbies = trim($_POST['hobbies'] ?? null);
$motivo = trim($_POST['motivo'] ?? null);
$mobilidade_reduzida = $_POST['mobilidade_reduzida'] ?? 0;
$tipo_mobilidade = trim($_POST['tipo_mobilidade'] ?? null);
$nif = trim($_POST['nif'] ?? null);
$nome_atelier = trim($_POST['nome_atelier'] ?? null);
$sub_tipo = $_POST['sub_tipo'] ?? null;

// --- Validações ---
if (empty($username) || empty($email) || empty($password) || empty($tipo)) {
    $_SESSION['register_error'] = "Nome, email, password e tipo de utilizador são obrigatórios.";
    header("Location: ../register.php");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = "O formato do email é inválido.";
    header("Location: ../register.php");
    exit;
}

// --- LÓGICA DO ROLE (A ALTERAÇÃO ESTÁ AQUI) ---
// Por defeito, o role é 2 (Aprendiz)
$role = 2;
// Se o formulário enviado for do tipo 'Artesão', muda o role para 3
if ($tipo === 'Artesão') {
    $role = 3;
}

// --- Gerar a hash da password ---
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$conn = new_db_connection();
mysqli_begin_transaction($conn);

try {
    // --- QUERY ATUALIZADA: agora inclui a coluna 'role' ---
    $query_user = "INSERT INTO users (username, email, password_hash, tipo, localizacao, hobbies, motivo, mobilidade_reduzida, tipo_mobilidade, nif, nome_atelier, sub_tipo, role) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_user = mysqli_prepare($conn, $query_user);
    // Adicionamos um 'i' no final para o tipo do novo parâmetro $role (integer)
    mysqli_stmt_bind_param($stmt_user, "ssssssssisssi", $username, $email, $password_hash, $tipo, $localizacao, $hobbies, $motivo, $mobilidade_reduzida, $tipo_mobilidade, $nif, $nome_atelier, $sub_tipo, $role);
    mysqli_stmt_execute($stmt_user);

    $user_id = mysqli_insert_id($conn);

    // Lógica para inserir os interesses (sem alterações)
    $interesses = $_POST['interesses'] ?? [];
    if (!empty($interesses) && $user_id) {
        $query_interesses = "INSERT INTO user_interesses (user_id, interesse_id) VALUES (?, ?)";
        $stmt_interesses = mysqli_prepare($conn, $query_interesses);
        foreach ($interesses as $interesse_id) {
            mysqli_stmt_bind_param($stmt_interesses, "ii", $user_id, $interesse_id);
            mysqli_stmt_execute($stmt_interesses);
        }
        mysqli_stmt_close($stmt_interesses);
    }

    mysqli_commit($conn);

    $_SESSION['register_success'] = "Conta criada com sucesso! Pode agora fazer login.";
    header("Location: ../login.php");
    exit;
} catch (mysqli_sql_exception $exception) {
    mysqli_rollback($conn);
    if (mysqli_errno($conn) == 1062) { // Erro de entrada duplicada (username ou email)
        $_SESSION['register_error'] = "O nome de utilizador ou o email que inseriu já existem.";
    } else {
        $_SESSION['register_error'] = "Erro ao criar conta: " . $exception->getMessage();
    }
    header("Location: ../register.php");
    exit;
}

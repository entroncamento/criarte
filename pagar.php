<?php

include_once 'connections/connection.php';
$conn = new_db_connection();

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$workshop_id = $_POST['workshop_id'] ?? 0;

if (!$workshop_id) {
    echo "<h1>Erro: Workshop não especificado.</h1>";
    exit;
}

$sql = "INSERT IGNORE INTO user_workshops (user_id, workshop_id) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $workshop_id);
mysqli_stmt_execute($stmt);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <link rel="stylesheet" href="styles/style.css">
    <meta charset="UTF-8">
    <title>Pagamento feito</title>
    <meta http-equiv="refresh" content="4;url=index.php">
    <link rel="stylesheet" href="styles/style.css">
    <style>
        body {
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
            padding: 60px 20px;
            color: #4806A4;
        }

        .check-icon {
            font-size: 5rem;
            display: inline-block;
            background-color: #9D6EFF;
            color: white;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            line-height: 100px;
            font-weight: bold;
            margin: 30px auto;
        }

        a {
            color: #4806A4;
            text-decoration: underline;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>

<body>


    <h2>Pagamento feito com sucesso!</h2>
    <div class="check-icon">✔</div>
    <p><a href="index.php">Sair</a></p>
</body>

</html>
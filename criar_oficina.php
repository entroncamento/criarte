<?php
session_start();
include_once 'connections/connection.php';
$conn = new_db_connection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Buscar dados do utilizador
$stmt = mysqli_prepare($conn, "SELECT username, pfp FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $username, $pfp);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Criar Oficina - Criarte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            background-color: #f9f9f9;
        }

        .main-wrapper {
            display: flex;
        }

        .sidebar {
            width: 240px;
            background-color: #e6d7f3;
            min-height: 100vh;
            padding: 30px 20px;
            color: #4d0f8b;
            text-align: center;
        }

        .sidebar h4 {
            margin-top: 15px;
            font-size: 18px;
        }

        .sidebar a {
            display: block;
            margin-top: 15px;
            color: #333;
            text-decoration: none;
        }

        .sidebar a.active {
            color: #800080;
            font-weight: bold;
        }

        .content {
            flex: 1;
            padding: 40px;
            background: #fff;
        }

        .content h2 {
            margin-bottom: 20px;
        }

        input,
        textarea,
        button {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #f7941d;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #d97b00;
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .image-upload {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .image-upload label {
            width: 90px;
            height: 90px;
            border-radius: 16px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #8c69cc;
            border: 2px dashed #cbb6e5;
            cursor: pointer;
        }

        .image-upload input[type="file"] {
            display: none;
        }

        .image-upload-preview {
            width: 90px;
            height: 90px;
            border-radius: 16px;
            background: #eee;
            background-size: cover;
            background-position: center;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="sidebar text-center">
            <img src="<?= $pfp ?? 'imgs/user-default.png' ?>" alt="Foto de perfil" class="img-fluid rounded-circle" width="100">
            <h4><?= htmlspecialchars($username) ?></h4>
            <a href="#">Mensagens</a>
            <a href="#">Editar Perfil</a>
            <a href="#" class="active">Oficina</a>
            <a href="#">Aulas</a>
        </div>

        <div class="content">
            <h2>Criar Oficina</h2>
            <form id="form-oficina" action="processar_oficina.php" method="post" enctype="multipart/form-data">

                <label>Adicionar fotos</label>
                <div class="image-upload">
                    <label for="imagem">+</label>
                    <input type="file" name="imagem" id="imagem" accept="image/*">
                    <div id="preview" class="image-upload-preview"></div>
                </div>

                <label for="titulo">Nome da Oficina</label>
                <input type="text" name="titulo" id="titulo" required placeholder="Ex: Carlos Teixeira">

                <label for="professor">Professor(es)</label>
                <input type="text" name="professor" id="professor" required>

                <label for="descricao">Descrição</label>
                <textarea name="descricao" id="descricao" rows="5"></textarea>

                <button type="button" onclick="verificarImagem()">Adicionar</button>
            </form>
        </div>
    </div>

    <div class="popup" id="popup-confirm">
        <div class="popup-content">
            <p>Não adicionaste uma imagem de capa.<br>Queres que o Criarte gere uma automaticamente?</p>
            <button onclick="confirmarGerarImagem(true)">Sim, gerar com AI</button>
            <button onclick="confirmarGerarImagem(false)">Não, quero adicionar uma imagem</button>
        </div>
    </div>

    <script>
        function verificarImagem() {
            const file = document.getElementById('imagem').files[0];
            if (!file) {
                document.getElementById('popup-confirm').style.display = 'flex';
            } else {
                document.getElementById('form-oficina').submit();
            }
        }

        function confirmarGerarImagem(confirmado) {
            document.getElementById('popup-confirm').style.display = 'none';
            if (confirmado) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'gerar_ai';
                input.value = '1';
                document.getElementById('form-oficina').appendChild(input);
                document.getElementById('form-oficina').submit();
            } else {
                alert('Por favor adiciona uma imagem manualmente.');
            }
        }

        document.getElementById('imagem').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>
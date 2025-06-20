<?php
session_start();

include_once 'connections/connection.php';
$conn = new_db_connection();

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo'] ?? '') !== 'Artesão') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar dados
$sql = "SELECT * FROM workshops WHERE id = ? AND creator_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$workshop = mysqli_fetch_assoc($result);

if (!$workshop) {
    echo "<div class='alert alert-danger text-center m-5'>Workshop não encontrado ou não autorizado.</div>";
    exit;
}

// Atualizar se submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $publico = $_POST['publico'] ?? '';
    $duracao = $_POST['duracao'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $materiais = $_POST['materiais'] ?? '{}';
    $image_path = $workshop['image_url'];

    // Atualizar imagem (opcional)
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $target_dir = "uploads/workshops/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $filename = 'edit-' . time() . '.' . strtolower($ext);
        $destination = $target_dir . $filename;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destination)) {
            $image_path = $destination;
        }
    }

    // Update
    $sql = "UPDATE workshops SET title = ?, description = ?, publico = ?, duracao = ?, preco = ?, materiais = ?, image_url = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssdssi", $titulo, $descricao, $publico, $duracao, $preco, $materiais, $image_path, $id);
    mysqli_stmt_execute($stmt);

    echo "<div class='alert alert-success text-center'>Oficina atualizada com sucesso!</div>";

    // Atualizar os dados locais
    $workshop['title'] = $titulo;
    $workshop['description'] = $descricao;
    $workshop['publico'] = $publico;
    $workshop['duracao'] = $duracao;
    $workshop['preco'] = $preco;
    $workshop['materiais'] = $materiais;
    $workshop['image_url'] = $image_path;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Editar Oficina - <?= htmlspecialchars($workshop['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Editar Oficina: <?= htmlspecialchars($workshop['title']) ?></h2>

        <form method="POST" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light">
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($workshop['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" rows="3" class="form-control"><?= htmlspecialchars($workshop['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagem de Capa (opcional)</label><br>
                <?php if (!empty($workshop['image_url']) && file_exists($workshop['image_url'])): ?>
                    <img src="<?= $workshop['image_url'] ?>" alt="Capa atual" style="max-height: 180px;" class="mb-2 d-block">
                <?php endif; ?>
                <input type="file" name="imagem" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Público-alvo</label>
                <input type="text" name="publico" class="form-control" value="<?= htmlspecialchars($workshop['publico']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Duração</label>
                <input type="text" name="duracao" class="form-control" value="<?= htmlspecialchars($workshop['duracao']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Preço (€)</label>
                <input type="number" step="0.01" name="preco" class="form-control" value="<?= htmlspecialchars($workshop['preco']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Materiais (JSON)</label>
                <textarea name="materiais" rows="8" class="form-control"><?= htmlspecialchars($workshop['materiais']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Guardar Alterações</button>
            <a href="perfil.php" class="btn btn-secondary ms-2">Cancelar</a>
        </form>
    </div>
</body>

</html>
<?php
include_once 'connections/connection.php';
$conn = new_db_connection();

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $publico = $_POST['publico'] ?? '';
    $duracao = $_POST['duracao'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $materiais = $_POST['materiais'] ?? '{}';

    $sql = "UPDATE workshops SET publico = ?, duracao = ?, preco = ?, materiais = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsi", $publico, $duracao, $preco, $materiais, $id);
    mysqli_stmt_execute($stmt);
    echo "<div class='alert alert-success text-center'>Atualizado com sucesso!</div>";
}

$sql = "SELECT * FROM workshops WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$workshop = mysqli_fetch_assoc($result);

if (!$workshop) {
    echo "<h1>Workshop não encontrado.</h1>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Workshop - <?= htmlspecialchars($workshop['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Editar Workshop: <?= htmlspecialchars($workshop['title']) ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Público-alvo</label>
                <input type="text" name="publico" class="form-control" value="<?= htmlspecialchars($workshop['publico'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Duração</label>
                <input type="text" name="duracao" class="form-control" value="<?= htmlspecialchars($workshop['duracao'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Preço (em euros)</label>
                <input type="number" step="0.01" name="preco" class="form-control" value="<?= htmlspecialchars($workshop['preco'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Materiais (JSON)</label>
                <textarea name="materiais" class="form-control" rows="10"><?= htmlspecialchars($workshop['materiais'] ?? '{}') ?></textarea>

            </div>
            <button type="submit" class="btn btn-primary">Guardar alterações</button>
        </form>
    </div>
</body>

</html>
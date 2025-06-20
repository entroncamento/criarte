<?php
session_start();
require_once 'connections/connection.php';
$conn = new_db_connection();

$workshop_id = $_GET['workshop_id'] ?? null;
if (!$workshop_id) {
    header("Location: perfil.php");
    exit;
}

// Buscar o título da oficina
$stmt = mysqli_prepare($conn, "SELECT title FROM workshops WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $workshop_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $workshop_title);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Aulas da Oficina - <?= htmlspecialchars($workshop_title) ?></title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <?php include 'components/cp_nav.php'; ?>
    <div class="container py-4">
        <h2 class="mb-4">Aulas da oficina: <?= htmlspecialchars($workshop_title) ?></h2>
        <button class="btn btn-success mb-4" data-bs-toggle="collapse" data-bs-target="#form-aula">+ Adicionar Nova Aula</button>

        <div id="form-aula" class="collapse mb-4">
            <form action="actions/adicionar_aula.php" method="POST" class="border p-4 rounded bg-light">
                <input type="hidden" name="workshop_id" value="<?= $workshop_id ?>">

                <div class="mb-3">
                    <label class="form-label">Título da Aula</label>
                    <input type="text" class="form-control" name="titulo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea class="form-control" name="descricao" rows="3" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" class="form-control" name="data" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hora de Início</label>
                        <input type="time" class="form-control" name="hora_inicio" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hora de Fim</label>
                        <input type="time" class="form-control" name="hora_fim" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Aula</button>
            </form>
        </div>

        <div id="aulas-container" class="row g-3">
            <!-- Aulas aparecem aqui via AJAX -->
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            fetch("api/get_aulas.php?workshop_id=<?= $workshop_id ?>")
                .then(res => res.text())
                .then(html => {
                    document.getElementById("aulas-container").innerHTML = html;
                })
                .catch(err => {
                    document.getElementById("aulas-container").innerHTML = "<p class='text-danger'>Erro ao carregar aulas.</p>";
                });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
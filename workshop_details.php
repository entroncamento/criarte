<?php

include_once 'connections/connection.php';
$conn = new_db_connection();

$id = $_GET['id'] ?? 0;

// Obter dados do workshop
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

// Contar número de inscritos
$inscritos = 0;
$stmt2 = mysqli_prepare($conn, "SELECT COUNT(*) FROM user_workshops WHERE workshop_id = ?");
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_bind_result($stmt2, $inscritos);
mysqli_stmt_fetch($stmt2);
mysqli_stmt_close($stmt2);

// Verificar se o utilizador já está inscrito
$ja_inscrito = false;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $stmt3 = mysqli_prepare($conn, "SELECT 1 FROM user_workshops WHERE user_id = ? AND workshop_id = ?");
    mysqli_stmt_bind_param($stmt3, "ii", $uid, $id);
    mysqli_stmt_execute($stmt3);
    mysqli_stmt_store_result($stmt3);
    $ja_inscrito = mysqli_stmt_num_rows($stmt3) > 0;
    mysqli_stmt_close($stmt3);
}

$lotacao = $workshop['lotacao_maxima'] ?? 0;
$percentagem = ($lotacao > 0) ? min(100, ($inscritos / $lotacao) * 100) : 0;
$cheio = $lotacao > 0 && $inscritos >= $lotacao;

$materiais = json_decode($workshop['materiais'], true);
$publico = $workshop['publico'] ?? "Indicado para todos os públicos.";
$duration = $workshop['duracao'] ?? "Desconhecida";
$price = $workshop['preco'] ?? "Sob consulta";
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($workshop['title']) ?> - Criarte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style/style.css" />
</head>

<body>
    <?php include 'components/cp_nav.php'; ?>

    <div class="container py-5">
        <h2 class="text-center mb-4 text-orange">Workshop <?= htmlspecialchars($workshop['title']) ?></h2>

        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <img src="<?= $workshop['image_url'] ?>" alt="<?= htmlspecialchars($workshop['title']) ?>" class="img-fluid rounded">
            </div>
            <div class="col-md-6">
                <p class="lead">
                    <?= nl2br(htmlspecialchars($workshop['description'])) ?>
                </p>

                <?php if ($cheio): ?>
                    <div class="alert alert-danger">Este workshop está cheio.</div>
                <?php elseif ($ja_inscrito): ?>
                    <div class="alert alert-success">Já estás inscrito neste workshop.</div>
                <?php else: ?>
                    <a href="pagamento.php?workshop_id=<?= $workshop['id'] ?>" class="btn btn-warning px-4">Inscrever-me</a>
                <?php endif; ?>

                <div class="mt-4">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-purple" role="progressbar" style="width: <?= $percentagem ?>%"></div>
                    </div>
                    <small class="text-muted"><?= $inscritos ?>/<?= $lotacao ?> inscritos</small>
                </div>
            </div>
        </div>

        <h4 class="text-center text-purple mt-5 mb-4">Materiais Inclusos no Workshop</h4>

        <div class="row g-4">
            <?php if (is_array($materiais)) : ?>
                <?php foreach ($materiais as $secao => $itens): ?>
                    <div class="col-md-4">
                        <div class="p-3 bg-light border rounded">
                            <h6 class="text-purple fw-bold"><?= htmlspecialchars($secao) ?></h6>
                            <ul class="mb-0">
                                <?php foreach ($itens as $item): ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Nenhuma informação de materiais disponível.</p>
            <?php endif; ?>

            <div class="col-md-4">
                <div class="p-3 bg-light border rounded">
                    <h6 class="text-purple fw-bold">Público-alvo</h6>
                    <p class="mb-0"><?= htmlspecialchars($publico) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light border rounded">
                    <h6 class="text-purple fw-bold">Informações adicionais</h6>
                    <ul class="mb-0">
                        <li><strong>Duração:</strong> <?= htmlspecialchars($duration) ?></li>
                        <li><strong>Preço:</strong> <?= htmlspecialchars($price) ?>€</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="#topo" class="btn btn-link">Voltar para o topo da página</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
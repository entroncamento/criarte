<?php
include_once 'connections/connection.php';
$conn = new_db_connection();


$workshop_id = $_GET['workshop_id'] ?? 0;

// Buscar dados do workshop (título e preço)
$sql = "SELECT title, preco FROM workshops WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $workshop_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$workshop = mysqli_fetch_assoc($result);

if (!$workshop) {
    echo "<h1>Workshop não encontrado.</h1>";
    exit;
}
$preco = number_format((float)$workshop['preco'], 2, ',', '') . '€';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Método pagamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>

    <?php include 'components/cp_nav.php'; ?>

    <div class="pagamento-page">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 main-container">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center mb-4 mb-md-0">
                            <img src="imgs/pagamento.png" alt="Pagamento" class="img-fluid">
                        </div>
                        <div class="col-md-6">
                            <h4 class="text-orange mb-4">Detalhes de pagamento</h4>
                            <form action="pagar.php" method="POST">
                                <input type="hidden" name="workshop_id" value="<?= $workshop_id ?>">

                                <div class="mb-3">
                                    <label>Nome do proprietário</label>
                                    <input type="text" name="nome" class="form-control" required
                                        value="Carlos Daniel Almeida Teixeira">
                                </div>

                                <div class="mb-3">
                                    <label>Número do cartão</label>
                                    <input type="text" name="cartao" class="form-control" required
                                        value="4173 4199 300 1111">
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Mês expira</label>
                                        <input type="text" name="mes" class="form-control" required value="07">
                                    </div>
                                    <div class="col">
                                        <label>Ano expira</label>
                                        <input type="text" name="ano" class="form-control" required value="2026">
                                    </div>
                                    <div class="col">
                                        <label>CVV</label>
                                        <input type="text" name="cvv" class="form-control" required value="973">
                                    </div>
                                </div>

                                <p class="valor-pagamento">Montante de pagamento: <strong><?= $preco ?></strong></p>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-purple">Pagar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
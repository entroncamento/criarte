<?php
session_start();


include 'components/cp_nav.php';
require_once 'connections/connection.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo'] ?? '') !== 'Artesão' || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$workshop_id = intval($_GET['id']);

$conn = new_db_connection();

// Buscar dados do workshop
$stmt = mysqli_prepare($conn, "SELECT title, image_url FROM workshops WHERE id = ? AND creator_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $workshop_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $titulo, $imagem);
if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: perfil.php?erro=workshop_nao_encontrado");
    exit;
}
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Caminho QR code
$qr_code_path = "uploads/qrcodes/ws_" . $workshop_id . ".png";
$workshop_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/workshop_details.php?id=" . $workshop_id;
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Oficina Criada com Sucesso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
    <style>
        .qr-code-img {
            max-width: 300px;
        }

        .success-icon {
            font-size: 3rem;
            color: green;
        }

        .cover-img {
            max-height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>

<body class="content-area-suave">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-lg text-center mt-5">
                    <div class="card-body p-5">
                        <div class="success-icon mb-3">✓</div>
                        <h2 class="fw-bold">Oficina Criada com Sucesso!</h2>
                        <p class="text-muted">Partilhe o QR code ou o link abaixo com os seus aprendizes.</p>

                        <h4 class="mt-4 fw-semibold"><?= htmlspecialchars($titulo) ?></h4>

                        <?php if (!empty($imagem) && file_exists($imagem)): ?>
                            <div class="mt-3 mb-4">
                                <img src="<?= $imagem ?>" alt="Imagem de Capa" class="img-fluid cover-img">
                            </div>
                        <?php endif; ?>

                        <div class="my-3">
                            <?php if (file_exists($qr_code_path)): ?>
                                <img src="<?= $qr_code_path ?>" alt="QR Code para o Workshop" class="qr-code-img img-fluid">
                            <?php else: ?>
                                <p class="text-danger">QR code não encontrado.</p>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 small text-muted">Link direto para a oficina:</p>
                            <input type="text" class="form-control text-center" value="<?= htmlspecialchars($workshop_url) ?>" readonly onclick="this.select()">
                        </div>

                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                            <a href="<?= $qr_code_path ?>" download="qrcode_workshop_<?= $workshop_id ?>.png" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Descarregar QR Code
                            </a>
                            <a href="backoffice_workshop.php?id=<?= $workshop_id ?>" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Editar Oficina
                            </a>
                            <a href="perfil.php" class="btn btn-outline-secondary">Ir para o Meu Perfil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>

</html>
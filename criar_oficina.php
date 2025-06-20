<?php
session_start();
include 'components/cp_nav.php';
include_once 'connections/connection.php';

// Verificação de segurança: só permite acesso a Artesãos
if (!isset($_SESSION['user_id']) || ($_SESSION['tipo'] ?? 'Aprendiz') !== 'Artesão') {
    header("Location: index.php");
    exit;
}

$conn = new_db_connection();

// Buscar dados para o sidebar
$user_id = $_SESSION['user_id'];
$stmt_user = mysqli_prepare($conn, "SELECT username, pfp, tipo FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
mysqli_stmt_bind_result($stmt_user, $username, $pfp, $user_type);
mysqli_stmt_fetch($stmt_user);
mysqli_stmt_close($stmt_user);

// Buscar categorias para o dropdown
$query_categorias = "SELECT id, nome FROM categorias";
$result_categorias = mysqli_query($conn, $query_categorias);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Criar Oficina - Criarte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="page-wrapper">
        <?php include 'components/cp_sidebar.php'; ?>

        <main class="content-area">
            <div class="container">
                <h2 class="fw-bold">Criar Oficina</h2>
                <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                    <div class="alert alert-danger">Erro ao carregar a imagem. Tente novamente.</div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] == 'json'): ?>
                    <div class="alert alert-danger">Erro: os materiais não estão num formato JSON válido.</div>
                <?php endif; ?>

                <hr class="mb-4">

                <?php if (isset($_GET['status']) && $_GET['status'] == 'workshop_criado'): ?>
                    <div class="alert alert-success">Oficina criada com sucesso! Pode criar outra abaixo.</div>
                <?php endif; ?>

                <form id="form-oficina" action="actions/processar_oficina.php" method="post" enctype="multipart/form-data">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Foto de Capa</label>
                        <div class="image-upload">
                            <label for="imagem" class="image-upload-label" title="Adicionar imagem de capa" role="button" tabindex="0">
                                <i class="fas fa-plus"></i>
                            </label>

                            <div id="preview-container" class="d-flex gap-3"></div>
                            <input type="file" name="imagem" id="imagem" accept="image/*" class="d-none">


                        </div>
                    </div>

                    <h5 class="fw-bold mt-4">Informações Gerais</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Nome da Oficina</label>
                                <input type="text" name="titulo" id="titulo" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" name="categoria_id" id="categoria" required>
                                <option selected disabled value="">Selecione...</option>
                                <?php while ($cat = mysqli_fetch_assoc($result_categorias)): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="4" placeholder="Descreva o workshop, o que os participantes irão aprender, etc."></textarea>
                    </div>

                    <h5 class="fw-bold mt-4">Detalhes do Evento</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="preco" class="form-label">Preço (€)</label>
                            <input type="number" step="0.01" name="preco" id="preco" class="form-control" placeholder="Ex: 35.00">
                        </div>
                        <div class="col-md-4">
                            <label for="lotacao" class="form-label">Lotação Máxima</label>
                            <input type="number" name="lotacao_maxima" id="lotacao" class="form-control" placeholder="Ex: 10">
                        </div>
                        <div class="col-md-4">
                            <label for="duracao" class="form-label">Duração</label>
                            <input type="text" name="duracao" id="duracao" class="form-control" placeholder="Ex: 3 horas">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="publico" class="form-label">Público-alvo</label>
                        <input type="text" name="publico" id="publico" class="form-control" placeholder="Ex: Adultos e jovens iniciantes">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Materiais</label>
                        <div id="materiais-builder-container" class="materiais-builder">
                        </div>
                        <button type="button" id="btn-add-section" class="btn btn-sm btn-success mt-2">
                            <i class="fas fa-plus me-1"></i> Adicionar Secção de Materiais
                        </button>
                        <input type="hidden" name="materiais" id="materiais-json-output">
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="submit_button" value="adicionar" class="btn btn-orange">Adicionar Oficina</button>

                    </div>
                </form>
            </div>
        </main>
    </div>
    <div id="popup-confirm" class="popup">
        <div class="popup-content">
            <h5>Sem imagem?</h5>
            <p>Deseja gerar automaticamente uma imagem com base no título da oficina?</p>
            <button onclick="confirmarGerarImagem(true)" class="btn btn-success me-2">Sim</button>
            <button onclick="confirmarGerarImagem(false)" class="btn btn-secondary">Não</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/criar_oficina.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
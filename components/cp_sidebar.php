<?php
// Garante que a sessão está disponível


// Valores padrão para o caso de não haver sessão (evita erros)
$username = 'Visitante';
$pfp_path = 'imgs/user-default.png';
$user_type = '';

// Se o utilizador estiver logado, vai buscar os seus próprios dados
if (isset($_SESSION['user_id'])) {
    // Inclui e abre a sua própria ligação à BD
    require_once __DIR__ . '/../connections/connection.php';
    $conn_sidebar = new_db_connection();

    $user_id = $_SESSION['user_id'];

    // Busca os dados necessários
    $stmt = mysqli_prepare($conn_sidebar, "SELECT username, pfp, tipo FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $pfp, $user_type);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn_sidebar); // Fecha a sua própria ligação

    $pfp_path = (!empty($pfp) && file_exists($pfp)) ? htmlspecialchars($pfp) : 'imgs/user-default.png';
}

// Descobre o nome da página atual para o link 'active'
$page_name = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar text-center">
    <img src="<?= $pfp_path ?>" alt="Foto de perfil" class="pfp">
    <h5><?= htmlspecialchars($username) ?></h5>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p class="text-muted small"><?= htmlspecialchars($user_type) ?></p>
        <nav class="sidebar-nav mt-3">
            <?php if ($user_type === 'Artesão'): ?>
                <a href="perfil.php" class="<?= ($page_name == 'perfil.php' ? 'active' : '') ?>"><i class="fas fa-tachometer-alt fa-fw me-2"></i> Painel</a>
                <a href="criar_oficina.php" class="<?= ($page_name == 'criar_oficina.php' ? 'active' : '') ?>"><i class="fas fa-plus-circle fa-fw me-2"></i> Criar Oficina</a>
                <a href="mensagens.php" class="<?= ($page_name == 'mensagens.php' ? 'active' : '') ?>"><i class="fas fa-comment fa-fw me-2"></i> Mensagens</a>
                <a href="editar_perfil.php" class="<?= ($page_name == 'editar_perfil.php' ? 'active' : '') ?>"><i class="fas fa-edit fa-fw me-2"></i> Editar Perfil</a>
            <?php else: ?>
                <a href="perfil.php" class="<?= ($page_name == 'perfil.php' ? 'active' : '') ?>"><i class="fas fa-user fa-fw me-2"></i> Meu Perfil</a>
                <a href="mensagens.php" class="<?= ($page_name == 'mensagens.php' ? 'active' : '') ?>"><i class="fas fa-comment fa-fw me-2"></i> Mensagens</a>
                <a href="editar_perfil.php" class="<?= ($page_name == 'editar_perfil.php' ? 'active' : '') ?>"><i class="fas fa-edit fa-fw me-2"></i> Editar Perfil</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
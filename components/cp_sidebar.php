<?php
// As variáveis $pfp e $username são definidas na página principal que inclui este sidebar
$pfp_path = (isset($pfp) && !empty($pfp) && file_exists($pfp)) ? htmlspecialchars($pfp) : 'imgs/user-default.png';
// Descobre o nome da página atual para adicionar a classe 'active' ao link correto
$page_name = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar text-center">
    <img src="<?= $pfp_path ?>" alt="Foto de perfil" class="pfp">
    <h5><?= htmlspecialchars($username) ?></h5>
    <nav class="sidebar-nav">
        <a href="perfil.php" class="<?= ($page_name == 'perfil.php' ? 'active' : '') ?>"><i class="fas fa-user"></i> Meu Perfil</a>
        <a href="mensagens.php" class="<?= ($page_name == 'mensagens.php' ? 'active' : '') ?>"><i class="fas fa-comment"></i> Mensagens</a>
        <a href="editar_perfil.php" class="<?= ($page_name == 'editar_perfil.php' ? 'active' : '') ?>"><i class="fas fa-edit"></i> Editar Perfil</a>
        <a href="agenda.php" class="<?= ($page_name == 'agenda.php' ? 'active' : '') ?>"><i class="fas fa-calendar-alt"></i> Agenda Completa</a>
    </nav>
</div>
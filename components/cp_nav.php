<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="custom-navbar d-flex align-items-center px-3 justify-content-between">
    <a href="index.php"><img src="imgs/logo.png" alt="Logo" class="navbar-logo me-2" /></a>
    <div class="search-bar w-100 mx-3"><?php include 'cp_search.php'; ?></div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="d-flex align-items-center">
            <div class="dropdown me-3">
                <a href="#" id="notification-bell" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none position-relative">
                    <i class="fas fa-bell" style="font-size: 1.5rem; color: #555;"></i>
                    <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.65rem;"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow mt-2" aria-labelledby="notification-bell" id="notification-dropdown-list" style="width: 350px;">
                    <li>
                        <p class="text-center text-muted small my-2">A carregar...</p>
                    </li>
                </ul>
            </div>
            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                    $pfp_path = 'imgs/user-default.png';
                    if (!empty($_SESSION['pfp']) && file_exists($_SESSION['pfp'])) {
                        $pfp_path = $_SESSION['pfp'];
                    }
                    ?>
                    <img src="<?= htmlspecialchars($pfp_path) ?>" alt="Avatar" class="nav-profile-pfp rounded-circle me-2">
                    <span class="fw-semibold text-dark"><?= htmlspecialchars($_SESSION['username']) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                    <li>
                        <h6 class="dropdown-header">Conta de <?= htmlspecialchars($_SESSION['username']) ?></h6>
                    </li>
                    <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user me-2"></i>Perfil</a></li>
                    <li><a class="dropdown-item" href="mensagens.php"><i class="fas fa-comment me-2"></i>Mensagens</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="actions/logout_action.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <a href="login.php" class="ms-2"><i class="fas fa-user-circle profile-icon"></i></a>
    <?php endif; ?>
</nav>
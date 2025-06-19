<?php
session_start();
include_once 'connections/connection.php';
include 'components/cp_nav.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$conn = new_db_connection();

// Buscar dados do utilizador para o sidebar e para o cabeçalho inicial
$stmt = mysqli_prepare($conn, "SELECT username, pfp, tipo FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $username, $pfp, $user_type);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Criarte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="page-wrapper">
        <?php include 'components/cp_sidebar.php'; ?>

        <div class="content-area content-area-suave">
            <div id="dynamic-content" class="container">
                <div id="profile-menu">
                    <div class="profile-header">
                        <div class="pfp-container me-3">
                            <img class="pfp" src="<?= (!empty($pfp) && file_exists($pfp)) ? htmlspecialchars($pfp) : 'imgs/user-default.png'; ?>" alt="pfp">
                            <div class="edit-icon" id="pfp-edit-icon-trigger"><i class="fas fa-pencil-alt"></i></div>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold"><?= htmlspecialchars($username) ?></h4>
                            <p class="text-muted mb-0"><?= htmlspecialchars($user_type) ?></p>
                        </div>
                    </div>
                    <button class="profile-menu-btn" data-form="components\form_edit_data.php">Editar Dados</button>
                    <button class=" profile-menu-btn" data-form="components\form_edit_password.php">Alterar Palavra-passe</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/editar_perfil.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
<?php
session_start();
require_once "../connections/connection.php";
$conn = new_db_connection();
$user_id = $_SESSION['user_id'];

// Buscar dados atuais para preencher os campos
$stmt = mysqli_prepare($conn, "SELECT username, email, pfp FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $username, $email, $pfp);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<form action="actions/update_profile.php" method="POST" enctype="multipart/form-data">
    <h4 class="mb-4 fw-bold">Editar Dados do Perfil</h4>

    <div class="mb-4 text-center">
        <label for="pfp-input" style="cursor:pointer;">
            <?php
            // CORREÇÃO: Usamos ../ no file_exists para o PHP encontrar o ficheiro
            // E garantimos que o nome da imagem padrão é consistente
            $pfp_path = (!empty($pfp) && file_exists("../" . $pfp)) ? $pfp : 'imgs/avatar_default.png';
            ?>
            <img id="pfp-preview" src="<?= htmlspecialchars($pfp_path) ?>" alt="Pré-visualização" class="pfp rounded-circle">
        </label>
        <p class="mt-2 text-muted small">Clique na imagem para alterar</p>
        <input type="file" id="pfp-input" name="pfp_file" accept="image/*" class="d-none">
    </div>

    <div class="mb-3">
        <label class="form-label">Nome de Utilizador</label>
        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($username) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
    </div>

    <div class="error-message alert alert-danger p-2 small mt-3" style="display:none;"></div>
    <button type="submit" class="btn btn-orange fw-bold">Guardar Alterações</button>
</form>
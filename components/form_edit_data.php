<?php
session_start();
require_once "../connections/connection.php";
$conn = new_db_connection();
$user_id = $_SESSION['user_id'];

// Buscar os dados atuais do utilizador
$stmt = mysqli_prepare($conn, "SELECT username, email, pfp FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $username, $email, $pfp);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

$image_to_display = (!empty($pfp) && file_exists("../" . $pfp)) ? "../" . $pfp : '../imgs/user-default.png';
?>

<form action="scripts/sc_update_profile.php" method="POST" enctype="multipart/form-data">
    <h4 class="mb-4 fw-bold">Editar Dados do Perfil</h4>

    <div class="mb-4 text-center">
        <label for="pfp-input">
            <img id="pfp-preview" src="<?= htmlspecialchars($image_to_display) ?>" alt="Pré-visualização" class="rounded-circle" style="width: 130px; height: 130px; object-fit: cover; cursor: pointer; border: 3px solid #ddd;">
        </label>
        <p class="mt-2 text-muted small">Clique na imagem para alterar</p>
        <input type="file" id="pfp-input" name="pfp_file" accept="image/*" class="d-none">
    </div>

    <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($username) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
    </div>

    <div class="error-message alert alert-danger p-2 small mt-3" style="display:none;"></div>
    <button type="submit" class="btn btn-warning fw-bold">Guardar Alterações</button>
</form>
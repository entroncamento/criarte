<?php
session_start();
include 'components/cp_nav.php';
include_once 'connections/connection.php';
$conn = new_db_connection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// --- BUSCAR DADOS PARA O SIDEBAR ---
$stmt_sidebar = mysqli_prepare($conn, "SELECT username, pfp FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_sidebar, "i", $user_id);
mysqli_stmt_execute($stmt_sidebar);
mysqli_stmt_bind_result($stmt_sidebar, $username, $pfp);
mysqli_stmt_fetch($stmt_sidebar);
mysqli_stmt_close($stmt_sidebar);

// --- BUSCAR LISTA DE UTILIZADORES (INCLUINDO ESTADO ONLINE) ---
$query_users = "SELECT id, username, pfp, 
                CASE WHEN last_activity > NOW() - INTERVAL 2 MINUTE THEN 1 ELSE 0 END AS is_online
                FROM users WHERE id != ?";
$stmt_users = mysqli_prepare($conn, $query_users);
mysqli_stmt_bind_param($stmt_users, "i", $user_id);
mysqli_stmt_execute($stmt_users);
$result_users = mysqli_stmt_get_result($stmt_users);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Mensagens - Criarte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="page-wrapper">
        <?php include 'components/cp_sidebar.php'; ?>

        <div class="chat-container" data-my-id="<?= htmlspecialchars($_SESSION['user_id']) ?>">
            <div class="conversations-list">
                <div class="header">Conversas</div>
                <div class="users-list">
                    <?php while ($user = mysqli_fetch_assoc($result_users)):
                        $pfp_path = (!empty($user['pfp']) && file_exists($user['pfp'])) ? $user['pfp'] : 'imgs/user-default.png';
                    ?>
                        <div class="user-item" data-userid="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>" data-pfp="<?= htmlspecialchars($pfp_path) ?>" data-online="<?= $user['is_online'] ?>">
                            <div style="position: relative;">
                                <img src="<?= htmlspecialchars($pfp_path) ?>" alt="pfp" class="me-3 rounded-circle" style="width:50px; height:50px; object-fit:cover;">
                                <span class="status-indicator <?= $user['is_online'] ? 'online' : '' ?>"></span>
                            </div>
                            <div class="user-info">
                                <div class="username"><?= htmlspecialchars($user['username']) ?></div>
                                <div class="last-message">Clique para conversar...</div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="chat-window">
                <div class="chat-header" style="display: none;"></div>



                <div class="chat-messages">
                    <div class="text-center text-muted mt-5">Selecione uma conversa para começar.</div>
                </div>
                <div class="chat-input" style="display: none;">
                    <form id="message-form" class="input-group align-items-center" enctype="multipart/form-data">
                        <input type="file" id="image-input" name="image_file" accept="image/*" style="display: none;">
                        <button class="btn" type="button" id="image-button-trigger" title="Enviar Imagem"><i class="fas fa-image"></i></button>
                        <button class="btn" type="button" id="record-audio-button" title="Gravar Áudio"><i class="fas fa-microphone"></i></button>
                        <input type="text" id="message-input" name="message_content" class="form-control" placeholder="Escreva uma mensagem..." autocomplete="off">
                        <button class="btn btn-primary" type="submit" title="Enviar"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/wavesurfer.js"></script>
    <script src="js/chat.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
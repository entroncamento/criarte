<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    return;
}

require_once __DIR__ . '/../connections/connection.php';
$conn_list = new_db_connection();
$user_id = $_SESSION['user_id'];

$query = "SELECT id, title, estado, event_date FROM workshops WHERE creator_id = ? ORDER BY id DESC";
$stmt = mysqli_prepare($conn_list, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result_workshops = mysqli_stmt_get_result($stmt);
?>

<div class="list-group">
    <?php if ($result_workshops && mysqli_num_rows($result_workshops) > 0): ?>
        <?php while ($workshop = mysqli_fetch_assoc($result_workshops)):
            // ... (lógica para a cor do emblema) ...
        ?>
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center oficina-list-item">
                <div class="workshop-actions">
                    <a href="editar_oficina.php?id=<?= $workshop['id'] ?>" class="btn-action" title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="actions/remover_workshop.php" method="POST" onsubmit="return confirm('Tem a certeza que deseja remover esta oficina? É uma ação irreversível.');">
                        <input type="hidden" name="id" value="<?= $workshop['id'] ?>">
                        <button type="submit" class="btn-action btn-delete" title="Remover">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
    <?php endif; ?>
</div>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn_list);
?>
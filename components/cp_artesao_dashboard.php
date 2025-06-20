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

$query = "SELECT id, title, estado, event_date FROM workshops WHERE creator_id = ? ORDER BY event_date DESC";
$stmt = mysqli_prepare($conn_list, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result_workshops = mysqli_stmt_get_result($stmt);
?>

<div class="list-group">
    <?php if ($result_workshops && mysqli_num_rows($result_workshops) > 0): ?>
        <?php while ($workshop = mysqli_fetch_assoc($result_workshops)): ?>
            <?php
            // Lógica para a cor do emblema de estado
            $estado_class = 'bg-secondary'; // Cinzento por defeito
            if ($workshop['estado'] == 'aceite') {
                $estado_class = 'bg-success';
            } elseif ($workshop['estado'] == 'pendente') {
                $estado_class = 'bg-warning text-dark';
            }
            ?>
            <div class="list-group-item d-flex align-items-center oficina-list-item">

                <div class="flex-grow-1 d-flex align-items-center">
                    <i class="fas fa-palette fa-2x text-purple me-3"></i>
                    <div>
                        <h5 class="mb-1"><?= htmlspecialchars($workshop['title']) ?></h5>
                        <small>Data: <?= date('d/m/Y', strtotime($workshop['event_date'])) ?> | Estado: <span class="badge rounded-pill <?= $estado_class ?>"><?= ucfirst($workshop['estado']) ?></span></small>
                    </div>
                </div>

                <div class="workshop-actions">
                    <a href="backoffice_workshop.php?id=<?= $workshop['id'] ?>" class="btn-action" title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <!-- Botão para adicionar aula (NOVIDADE) -->
                    <a href="aulas.php?workshop_id=<?= $workshop['id'] ?>" class="btn-action" title="Adicionar Aula">
                        <i class="fas fa-book"></i>
                    </a>

                    <form action="actions/remover_workshop.php" method="POST" onsubmit="return confirm('Tem a certeza que deseja remover esta oficina?');">
                        <input type="hidden" name="id" value="<?= $workshop['id'] ?>">
                        <button type="submit" class="btn-action btn-delete" title="Remover">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>

            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="list-group-item text-center p-4">
            <p class="mb-1 text-muted">Ainda não criou nenhuma oficina.</p>
            <a href="criar_oficina.php">Comece agora!</a>
        </div>
    <?php endif; ?>
</div>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn_list);
?>
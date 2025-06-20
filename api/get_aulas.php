<?php
require_once '../connections/connection.php';
$conn = new_db_connection();

$workshop_id = $_GET['workshop_id'] ?? null;
if (!$workshop_id) exit;

$stmt = mysqli_prepare($conn, "SELECT titulo, descricao, data, hora_inicio, hora_fim FROM aulas WHERE workshop_id = ? ORDER BY data ASC, hora_inicio ASC");
mysqli_stmt_bind_param($stmt, "i", $workshop_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0):
    while ($row = mysqli_fetch_assoc($result)):
?>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5 class="fw-bold"><?= htmlspecialchars($row['titulo']) ?></h5>
                <p><?= nl2br(htmlspecialchars($row['descricao'])) ?></p>
                <p class="mb-0 text-muted">
                    <?= date('d/m/Y', strtotime($row['data'])) ?>,
                    <?= htmlspecialchars($row['hora_inicio']) ?> - <?= htmlspecialchars($row['hora_fim']) ?>
                </p>
            </div>
        </div>
    <?php
    endwhile;
else:
    ?>
    <p class="text-muted">Ainda não foram adicionadas aulas a esta oficina.</p>
<?php
endif;
mysqli_stmt_close($stmt);
mysqli_close($conn);

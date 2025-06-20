<?php
// Este ficheiro assume que as variáveis $conn e $user_id já existem na página que o chama (perfil.php)

// Busca as aulas em que o utilizador esteve inscrito e que já passaram
$stmt_aulas = mysqli_prepare(
    $conn,
    "SELECT w.id, w.title, w.event_date 
     FROM workshops w 
     JOIN user_workshops uw ON w.id = uw.workshop_id 
     WHERE uw.user_id = ? AND w.event_date < CURDATE() 
     ORDER BY w.event_date DESC"
);

mysqli_stmt_bind_param($stmt_aulas, "i", $user_id);
mysqli_stmt_execute($stmt_aulas);
$result_aulas = mysqli_stmt_get_result($stmt_aulas);
?>

<h3 class="fw-bold mb-4">Aulas realizadas</h3>

<?php if ($result_aulas && mysqli_num_rows($result_aulas) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result_aulas)):
        $data = strtotime($row['event_date']);
        setlocale(LC_TIME, 'pt_PT.utf8');
    ?>
        <div class="aula-card">
            <div class="data-box">
                <div class="dia"><?= date('d', $data) ?></div>
                <div class="mes"><?= strftime('%b', $data) ?></div>
            </div>
            <div class="info flex-grow-1">
                <div class="titulo"><?= htmlspecialchars($row['title']) ?></div>
                <a class="classificar" href="avaliar_workshop.php?id=<?= $row['id'] ?>">Classificar aula</a>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p class="text-muted">Ainda não frequentou nenhuma aula.</p>
<?php endif;

// É importante fechar o statement aqui dentro do componente
if (isset($stmt_aulas)) {
    mysqli_stmt_close($stmt_aulas);
}
?>
<?php
require_once "../connections/connection.php";
$conn = new_db_connection();
$query_interesses = "SELECT id, nome FROM interesses";
$result_interesses = mysqli_query($conn, $query_interesses);
?>

<form action="actions/register_action.php" method="POST">
    <input type="hidden" name="tipo" value="Aprendiz">

    <div class="form-row mb-2">
        <div class="form-group">
            <label for="username" class="form-label">Nome</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
    </div>

    <div class="form-row mb-2">
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="localizacao" class="form-label">Localização</label>
            <input type="text" name="localizacao" class="form-control">
        </div>
    </div>

    <div class="form-row mb-2">
        <div class="form-group full-width">
            <label for="hobbies" class="form-label">Hobbies</label>
            <textarea name="hobbies" class="form-control" rows="1"></textarea>
        </div>
    </div>
    <div class="form-row mb-2">
        <div class="form-group full-width">
            <label for="motivo" class="form-label">Motivo para frequentar a aula</label>
            <textarea name="motivo" class="form-control" rows="1"></textarea>
        </div>
    </div>

    <div class="form-row mb-2">
        <div class="form-group full-width">
            <label class="form-label">Quais são os teus interesses?</label>
            <div class="choice-group">
                <?php while ($interesse = mysqli_fetch_assoc($result_interesses)): ?>
                    <input type="checkbox" class="btn-check" id="interesse_<?= $interesse['id'] ?>" name="interesses[]" value="<?= $interesse['id'] ?>">
                    <label class="btn" for="interesse_<?= $interesse['id'] ?>"><?= htmlspecialchars($interesse['nome']) ?></label>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group full-width">
            <label class="form-label">Tens algum tipo de mobilidade reduzida?</label>
            <div class="choice-group">
                <input type="radio" class="btn-check" name="mobilidade_reduzida" id="mob_sim" value="1">
                <label class="btn" for="mob_sim">Sim</label>
                <input type="radio" class="btn-check" name="mobilidade_reduzida" id="mob_nao" value="0" checked>
                <label class="btn" for="mob_nao">Não</label>
            </div>
        </div>
    </div>
    <div class="form-row mb-2">
        <div class="form-group full-width">
            <label for="tipo_mobilidade" class="form-label">Se sim, qual?</label>
            <input type="text" name="tipo_mobilidade" class="form-control">
        </div>
    </div>

    <button type="submit" class="btn btn-submit">Registar</button>
</form>
<?php mysqli_close($conn); ?>
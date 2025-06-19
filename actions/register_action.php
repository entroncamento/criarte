<?php
require_once "../connections/connection.php";
$conn = new_db_connection();
$result_interesses = mysqli_query($conn, "SELECT id, nome FROM interesses");
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
    <button type="submit" class="btn btn-submit">Registar</button>
</form>
<?php mysqli_close($conn); ?>
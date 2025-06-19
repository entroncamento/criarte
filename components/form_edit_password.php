<form action="scripts/sc_update_password.php" method="POST">
    <h4 class="mb-4 fw-bold">Alterar Palavra-passe</h4>
    <div class="mb-3">
        <label class="form-label">Palavra-passe Atual</label>
        <input type="password" class="form-control" name="current_password" placeholder="Introduza a sua password atual" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nova Palavra-passe</label>
        <input type="password" class="form-control" name="new_password" placeholder="Pelo menos 8 caracteres" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirmar Nova Palavra-passe</label>
        <input type="password" class="form-control" name="confirm_password" placeholder="Repita a nova password" required>
    </div>
    <div class="error-message alert alert-danger p-2 small mt-3" style="display:none;"></div>
    <button type="submit" class="btn btn-orange mt-3">Guardar</button>
</form>
<form action="actions/register_action.php" method="POST">
    <input type="hidden" name="tipo" value="Artesão">

    <div class="mb-3">
        <div class="choice-group">
            <input type="radio" class="btn-check" name="sub_tipo" id="tipo_prof" value="Professor individual" checked>
            <label class="btn" for="tipo_prof">Professor individual</label>

            <input type="radio" class="btn-check" name="sub_tipo" id="tipo_art" value="Artesão">
            <label class="btn" for="tipo_art">Artesão</label>

            <input type="radio" class="btn-check" name="sub_tipo" id="tipo_atel" value="Ateliê">
            <label class="btn" for="tipo_atel">Ateliê</label>
        </div>
    </div>

    <div class="mb-2">
        <input type="text" name="username" class="form-control" placeholder="Nome" required>
    </div>
    <div class="mb-2">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-2">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="mb-2">
        <input type="text" name="nif" class="form-control" placeholder="NIF">
    </div>
    <div class="mb-2">
        <input type="text" name="nome_atelier" class="form-control" placeholder="Nome do ateliê (opcional)">
    </div>

    <button type="submit" class="btn btn-submit">Registar</button>
</form>
<?php
// Este ficheiro é incluído, por isso o caminho para a conexão é direto
include_once 'connections/connection.php';
$conn = new_db_connection();

// Busca os workshops com estado 'aceite'
$sql = "SELECT id, title, image_url FROM workshops WHERE estado = 'aceite' LIMIT 6";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
?>
        <div class="card-custom">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <div class="card-image" style="background-image: url('<?= htmlspecialchars($row['image_url']) ?>');"></div>
            <a href="workshop_details.php?id=<?= $row['id'] ?>" class="btn-saber-mais">Saber mais</a>
        </div>
<?php
    }
}
mysqli_close($conn);
?>
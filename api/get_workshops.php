<?php
require_once '../connections/connection.php';
$conn = new_db_connection();

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$category = $_GET['category'] ?? '';

// Começa a query SQL
$sql = "SELECT id, title, image_url FROM workshops WHERE estado = 'aceite'";
$params = [];
$types = '';

// Adiciona o filtro de categoria se um for selecionado
if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

$sql .= " ORDER BY title ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Ajuste do caminho correto para imagens (relativo à pasta api/)
            $default_image = '../imgs/user-default.png';
            $image_path = (!empty($row['image_url']) && file_exists("../" . $row['image_url']))
                ? "../" . $row['image_url']
                : $default_image;

            // Para que o background-image funcione corretamente no browser, removemos o '../'
            $image_url = str_replace('../', '', $image_path);
?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <a href="workshop_details.php?id=<?= $row['id'] ?>" class="text-decoration-none">
                    <div class="card-custom">
                        <div class="card-image" style="background-image: url('<?= htmlspecialchars($image_url) ?>');"></div>
                        <div class="card-title-bar"><?= htmlspecialchars($row['title']) ?></div>
                    </div>
                </a>
            </div>
<?php
        }
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
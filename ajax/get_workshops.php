<?php
include_once '../connections/connection.php';
$conn = new_db_connection();

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM workshops WHERE 1";
$params = [];

if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY title ASC LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);

if ($category !== '') {
    mysqli_stmt_bind_param($stmt, "sii", $params[0], $limit, $offset);
} else {
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo ""; // Nada a mostrar
} else {
    while ($row = mysqli_fetch_assoc($result)) {
?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-3">
            <a href="../criarte/workshop_details.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark">
                <div class="workshop-card">
                    <div class="workshop-thumb" style="background-image: url('<?= $row['image_url'] ?>');"></div>
                    <div class="workshop-title"><?= htmlspecialchars($row['title']) ?></div>
                </div>
            </a>
        </div>
<?php
    }
}

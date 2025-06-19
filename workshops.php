<?php
include_once 'connections/connection.php';
$conn = new_db_connection();

// Obter categorias e contagem de workshops
$categories = [];
$cat_result = mysqli_query($conn, "SELECT category, COUNT(*) as total FROM workshops WHERE category IS NOT NULL GROUP BY category");
while ($cat_row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = [
        'name' => $cat_row['category'],
        'total' => $cat_row['total']
    ];
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Workshops - Criarte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style/style.css" />
</head>

<body>
    <?php include 'components/cp_nav.php'; ?>

    <div class="container py-5">
        <h2 class="text-center mb-4" style="color: #f29d40;">Todos os Workshops</h2>

        <div class="text-center mb-4">
            <button class="btn btn-outline-primary me-2 category-btn active" data-category="">Todos</button>
            <?php foreach ($categories as $cat): ?>
                <button class="btn btn-outline-primary me-2 category-btn" data-category="<?= $cat['name'] ?>">
                    <?= $cat['name'] ?> (<?= $cat['total'] ?>)
                </button>
            <?php endforeach; ?>
        </div>

        <div class="row g-4 justify-content-center" id="workshop-list">
            <!-- workshops aparecem aqui -->
        </div>

        <div class="text-center mt-4">
            <button id="load-more" class="btn btn-primary">Carregar mais</button>
        </div>





        <script src="scripts/load_workshops.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
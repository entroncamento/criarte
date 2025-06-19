    <?php
    $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

    ?>
    <!DOCTYPE html>
    <html lang="pt">

    <head>
        <meta charset="UTF-8" />
        <title>Resultados da Pesquisa</title>
    </head>

    <body>
        <h1>Resultados para: <?= htmlspecialchars($searchTerm) ?></h1>
        <!-- Aqui podes colocar lógica para pesquisar e mostrar resultados -->
    </body>

    </html>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Criarte - Início</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style/style.css" />
</head>

<body>
    <?php include 'components/cp_nav.php'; ?>

    <div class="container">
        <div class="section-header mt-4">
            <div>Workshops em Destaque</div>
            <a href="workshops.php">Ver mais</a>
        </div>
        <div class="carousel-scroll-wrapper">
            <button class="carousel-arrow" id="carousel-left">‹</button>
            <div class="carousel-scroll-container" id="carousel-container">
                <?php include 'components/cp_card.php'; ?>
            </div>
            <button class="carousel-arrow" id="carousel-right">›</button>
        </div>


        <div class="section-header">
            <div>A nossa Agenda</div>
        </div>

        <div id="homepage-agenda">
            <?php include 'components/cp_agenda.php'; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/sc_carrosel.js"></script>
        <script src="js/agenda_main.js"></script>
        <script src="js/main.js"></script>
</body>

</html>
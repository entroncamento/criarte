<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Registo - Criarte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="auth-page-body">
    <div class="main-wrapper">
        <div class="register-container">
            <div class="form-panel">
                <h2>Registo</h2>
                <div class="tab-group">
                    <button class="tab-btn active" data-target="form_aprendiz.php">Aprendiz</button>
                    <button class="tab-btn" data-target="form_artesao.php">Artesão</button>
                </div>
                <div id="message-container">
                    <?php
                    if (isset($_SESSION['register_error'])) {
                        echo '<div class="alert alert-danger p-2 small">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
                        unset($_SESSION['register_error']);
                    }
                    ?>
                </div>
                <div id="form-container"></div>
            </div>
            <div class="illustration-panel">
                <img id="register-illustration" src="imgs/registo.png" alt="Ilustração">
            </div>
        </div>
    </div>
    <script src="js/register.js"></script>
</body>

</html>
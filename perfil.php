<?php
session_start();
include 'components/cp_nav.php';
include_once 'connections/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$conn = new_db_connection();
$user_id = $_SESSION['user_id'];
// A variável $user_type vem da sessão e é usada para decidir o que mostrar
$user_type = $_SESSION['tipo'] ?? 'Aprendiz';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="page-wrapper">
        <?php include 'components/cp_sidebar.php'; ?>

        <main class="content-area">
            <?php
            // Lógica para mostrar o conteúdo principal correto
            if ($user_type === 'Artesão') {
                include 'components/cp_artesao_dashboard.php';
            } else {
                include 'components/cp_aulas_realizadas.php';
            }
            ?>
        </main>

        <?php
        // A agenda lateral só aparece para o Aprendiz
        if ($user_type === 'Aprendiz') {
            include 'components/cp_perfil_agenda.php';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/perfil_calendar.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
<?php
mysqli_close($conn);
?>
<?php
session_start();
require_once('../connections/connection.php');
require_once('../lib/phpqrcode/qrlib.php'); // Inclui a biblioteca de QR code

function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower(empty($text) ? 'n-a' : $text);
}

// Verificação de segurança
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['tipo'] ?? '') !== 'Artesão') {
    header("Location: ../login.php");
    exit;
}

$conn = new_db_connection();
$creator_id = $_SESSION['user_id'];

// --- Recolha e validação de dados ---
$titulo = trim($_POST['titulo'] ?? 'Workshop Sem Título');
$descricao = trim($_POST['descricao'] ?? '');
$categoria_id = $_POST['categoria_id'] ?? null;
if (!is_numeric($categoria_id)) {
    header("Location: ../criar_oficina.php?error=categoria");
    exit;
}
$preco = !empty($_POST['preco']) ? $_POST['preco'] : null;
$lotacao_maxima = !empty($_POST['lotacao_maxima']) ? $_POST['lotacao_maxima'] : null;
$duracao = trim($_POST['duracao'] ?? null);
$publico = trim($_POST['publico'] ?? null);
$materiais_texto = trim($_POST['materiais'] ?? '');
$event_date = $_POST['event_date'] ?? date('Y-m-d');
$usar_gerador_ai = isset($_POST['gerar_ai']) && $_POST['gerar_ai'] == '1';

// Validar JSON de materiais
$materiais_array = json_decode($materiais_texto, true);
if ($materiais_array === null) {
    header("Location: ../criar_oficina.php?error=json");
    exit;
}
$materiais_json = json_encode($materiais_array, JSON_UNESCAPED_UNICODE);

// --- Upload ou geração por IA ---
$image_path = null;
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $target_dir = "../uploads/workshops/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $slug = slugify($titulo);
    $file_info = pathinfo($_FILES["imagem"]["name"]);
    $unique_name = $slug . '-' . time() . '.' . strtolower($file_info['extension']);
    $target_file = $target_dir . $unique_name;
    if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
        $image_path = 'uploads/workshops/' . $unique_name;
    }
} elseif ($usar_gerador_ai) {
    // Geração por IA (se necessário)
    $image_path = 'uploads/workshops/gerada_por_ai.jpg';
}

if ($image_path === null) {
    header("Location: ../criar_oficina.php?error=no_image");
    exit;
}

// --- Inserção na BD ---
$query = "INSERT INTO workshops 
          (title, description, image_url, creator_id, estado, categoria_id, preco, lotacao_maxima, duracao, publico, materiais, event_date) 
          VALUES (?, ?, ?, ?, 'pendente', ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Erro na preparação da query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param(
    $stmt,
    "sssiidissss",
    $titulo,
    $descricao,
    $image_path,
    $creator_id,
    $categoria_id,
    $preco,
    $lotacao_maxima,
    $duracao,
    $publico,
    $materiais_json,
    $event_date
);
if (!mysqli_stmt_execute($stmt)) {
    die("Erro ao executar a query: " . mysqli_stmt_error($stmt));
}

// Obter o ID do workshop acabado de criar
$workshop_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);
mysqli_close($conn);

// --- Geração do QR Code ---
$base_url = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF']));
$workshop_url = $base_url . "/workshop_details.php?id=" . $workshop_id;

$qr_code_dir = '../uploads/qrcodes/';
if (!file_exists($qr_code_dir)) {
    mkdir($qr_code_dir, 0777, true);
}
$qr_code_path = $qr_code_dir . 'ws_' . $workshop_id . '.png';

QRcode::png($workshop_url, $qr_code_path, QR_ECLEVEL_L, 10, 2);

// --- Redirecionar para página com o QR gerado ---
header("Location: ../qr_success.php?id=" . $workshop_id);
exit;

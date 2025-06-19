<?php
session_start();
include_once 'connections/connection.php';
$conn = new_db_connection();

$titulo = $_POST['titulo'] ?? "Workshop Criativo";
$descricao = $_POST['descricao'] ?? "";
$professor = $_POST['professor'] ?? "";
$categoria_id = $_POST['categoria_id'] ?? null;
$preco = $_POST['preco'] ?? null;
date_default_timezone_set('Europe/Lisbon');
$data_evento = $_POST['data'] ?? date('Y-m-d');
$lotacao = $_POST['lotacao'] ?? null;

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

$filename = null;

if (!empty($_FILES['imagem']['tmp_name'])) {
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $filepath = __DIR__ . 'imgs/' . slugify($titulo) . '.' . $ext;
    $filename = 'imgs/' . slugify($titulo) . '.' . $ext;
    move_uploaded_file($_FILES['imagem']['tmp_name'], $filepath);
} else if (isset($_POST['gerar_ai']) && $_POST['gerar_ai'] === '1') {
    $prompt = "Highly detailed illustration of: $titulo, photorealistic, Lousã, Portuguese traditional style, studio lighting";
    $apiKey = 'sk-ogl4E3LOZBVhG99zP0hP90amH3HilqwtviUVH5NlSPLrbNyp';
    $url = 'https://api.stability.ai/v2beta/stable-image/generate/core';

    $headers = [
        "Authorization: Bearer $apiKey",
        "Accept: image/*"
    ];

    $postFields = [
        'prompt' => $prompt,
        'aspect_ratio' => '1:1',
        'output_format' => 'png',
        'negative_prompt' => 'blurry, distorted, low quality'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if (strpos($contentType, 'image/png') !== false) {
        $slug = slugify($titulo);
        $filepath = __DIR__ . '/../imgs/' . $slug . '.png';
        $filename = 'imgs/' . $slug . '.png';
        file_put_contents($filepath, $response);
    } else {
        echo "<h2>❌ Erro ao gerar imagem.</h2>";
        echo "<pre>";
        var_dump($response);
        echo "</pre>";
        exit;
    }
} else {
    echo "<script>alert('É necessário escolher uma imagem ou aceitar a geração automática.'); history.back();</script>";
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO workshops (title, description, image_url, event_date, preco, lotacao_maxima, estado, categoria_id, publico, duracao) VALUES (?, ?, ?, ?, ?, ?, 'pendente', ?, ?, ?)");
$publico = "Todos os públicos";
$duracao = "Por definir";
mysqli_stmt_bind_param($stmt, "sssdisiss", $titulo, $descricao, $filename, $data_evento, $preco, $lotacao, $categoria_id, $publico, $duracao);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: perfil.php");
exit;

function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text ?: 'evento');
}

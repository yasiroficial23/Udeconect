<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Debes iniciar sesión.';
    echo json_encode($response);
    exit;
}

$usuario_id = $_SESSION['user_id'];
$publicacion_id = $_POST['publicacion_id'] ?? null;
$comentario = trim($_POST['comentario'] ?? '');

if (!$publicacion_id || !$comentario) {
    $response['message'] = 'Datos incompletos.';
    echo json_encode($response);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO publicacion_comentarios (publicacion_id, usuario_id, comentario, fecha_comentario) VALUES (?, ?, ?, NOW())");
$stmt->execute([$publicacion_id, $usuario_id, $comentario]);

$response['success'] = true;
$response['message'] = 'Comentario guardado.';
echo json_encode($response);

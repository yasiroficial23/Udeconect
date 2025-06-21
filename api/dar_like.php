<?php
session_start();
require_once '../api/db_connect.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'total_likes' => 0];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Debes iniciar sesión.';
    echo json_encode($response);
    exit;
}

$usuario_id = $_SESSION['user_id'];
$publicacion_id = $_POST['publicacion_id'] ?? null;

if (!$publicacion_id) {
    $response['message'] = 'Publicación inválida.';
    echo json_encode($response);
    exit;
}

// Verificar si ya dio like
$stmt = $pdo->prepare("SELECT id FROM publicacion_likes WHERE publicacion_id = ? AND usuario_id = ?");
$stmt->execute([$publicacion_id, $usuario_id]);

if ($stmt->fetch()) {
    // Si ya dio like, lo quitamos (toggle)
    $pdo->prepare("DELETE FROM publicacion_likes WHERE publicacion_id = ? AND usuario_id = ?")
        ->execute([$publicacion_id, $usuario_id]);
} else {
    $pdo->prepare("INSERT INTO publicacion_likes (publicacion_id, usuario_id, fecha_like) VALUES (?, ?, NOW())")
        ->execute([$publicacion_id, $usuario_id]);
}

// Contar likes
$stmt = $pdo->prepare("SELECT COUNT(*) FROM publicacion_likes WHERE publicacion_id = ?");
$stmt->execute([$publicacion_id]);
$response['total_likes'] = $stmt->fetchColumn();
$response['success'] = true;

echo json_encode($response);

<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$publicacion_id = $_GET['publicacion_id'] ?? null;
$response = ['success' => false, 'comentarios' => []];

if (!$publicacion_id) {
    echo json_encode($response);
    exit;
}

$stmt = $pdo->prepare("
    SELECT pc.comentario, pc.fecha_comentario as fecha, u.nombre as usuario 
    FROM publicacion_comentarios pc 
    JOIN usuarios u ON pc.usuario_id = u.id 
    WHERE pc.publicacion_id = ? 
    ORDER BY pc.fecha_comentario DESC
");
$stmt->execute([$publicacion_id]);
$response['comentarios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response['success'] = true;

echo json_encode($response);

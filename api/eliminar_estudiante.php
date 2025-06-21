<?php
require_once '../api/db_connect.php';
header('Content-Type: application/json');

$codigo = trim($_POST['codigo_estudiantil'] ?? null);

if (!$codigo) {
    echo json_encode(['success' => false, 'message' => 'Código no recibido.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM estudiantes_certificados WHERE codigo_estudiantil = ?");
$stmt->execute([$codigo]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$id = $row['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado.']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM estudiantes_certificados WHERE id = ?");
if ($stmt->execute([$id])) {
    echo json_encode(['success' => true, 'message' => 'Estudiante eliminado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar.']);
}

<?php
require_once '../api/db_connect.php';

$term = $_GET['term'] ?? '';

if (!$term) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, nombre, apellidos, email FROM usuarios WHERE rol = 'estudiante' AND email LIKE ? LIMIT 10");
$stmt->execute(["%$term%"]);

$resultados = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $resultados[] = [
        'label' => "{$row['nombre']} {$row['apellidos']} ({$row['email']})",
        'value' => $row['email']
    ];
}

header('Content-Type: application/json');
echo json_encode($resultados);

<?php
header('Content-Type: application/json');
require_once '../api/db_connect.php';

$codigo = $_GET['codigo'] ?? '';

$response = ['success' => false, 'data' => [], 'message' => ''];

if ($codigo) {
    try {
        $stmt = $pdo->prepare("SELECT id, usuario_id, nombres, apellidos, email, codigo_estudiantil, certificado_nombre FROM estudiantes_certificados WHERE codigo_estudiantil = ?");
        $stmt->execute([$codigo]);
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($estudiante) {
            $response['success'] = true;
            $response['data'] = $estudiante;
        } else {
            $response['message'] = 'Estudiante no encontrado.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Código no proporcionado.';
}

echo json_encode($response);


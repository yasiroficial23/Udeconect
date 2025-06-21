<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$response = ['success' => false, 'message' => 'Error al registrar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $codigo = $_POST['codigo_estudiantil'] ?? '';
    $email = $_POST['email'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $centro_id = $_POST['centro_tutorial_id'] ?? null;

    if ($nombres && $apellidos && $codigo && $email && $centro_id) {
        try {
            $stmt = $pdo->prepare("INSERT INTO estudiantes_certificados 
                (nombres, apellidos, codigo_estudiantil, email, fecha_nacimiento, centro_tutorial_id, activo, creado_por) 
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)");

            $stmt->execute([
                $nombres, 
                $apellidos, 
                $codigo, 
                $email, 
                $fecha_nacimiento, 
                $centro_id, 
                $_SESSION['user_id'] ?? 1 // Usa el admin actual o un ID por defecto
            ]);

            $response['success'] = true;
            $response['message'] = 'Estudiante registrado exitosamente.';
        } catch (PDOException $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Faltan campos obligatorios.';
    }
}

echo json_encode($response);

<?php
require_once '../api/db_connect.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$nombres = $_POST['nombres'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$email = $_POST['email'] ?? '';
$centro = $_POST['centro_tutorial_id'] ?? null;

if (!$id || !$nombres || !$apellidos || !$email || !$centro) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE estudiantes_certificados SET nombres = ?, apellidos = ?, email = ?, centro_tutorial_id = ? WHERE id = ?");
    $stmt->execute([$nombres, $apellidos, $email, $centro, $id]);
    echo json_encode(['success' => true, 'message' => 'Estudiante actualizado correctamente.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

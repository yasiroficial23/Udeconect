<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    header('Location: ../index.html');
    exit;
}

$tutor_id = $_SESSION['user_id'];
$grupo_id = $_POST['grupo_id'] ?? null;
$viable = isset($_POST['viable']) ? (int)$_POST['viable'] : null;
$observaciones = trim($_POST['observaciones'] ?? '');

if (!$grupo_id || $viable === null) {
    exit('Datos incompletos.');
}

try {
    // Insertar evaluación
    $stmt = $pdo->prepare("INSERT INTO tcc_evaluaciones (grupo_id, evaluador_id, viable, observaciones) VALUES (?, ?, ?, ?)");
    $stmt->execute([$grupo_id, $tutor_id, $viable, $observaciones]);

    // Actualizar estado en grupos_tcc
    $nuevo_estado = $viable ? 'viable' : 'en progreso';
    $stmt2 = $pdo->prepare("UPDATE grupos_tcc SET estado_tcc = ? WHERE id = ?");
    $stmt2->execute([$nuevo_estado, $grupo_id]);

    header('Location: ../html/tcc_tutor.php');
    exit;

} catch (PDOException $e) {
    exit('Error al guardar evaluación: ' . $e->getMessage());
}

<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    echo "<script>alert('Acceso no autorizado'); window.location.href = '../index.html';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proyecto_id = $_POST['proyecto_id'] ?? null;
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $descripcion_corta = $_POST['descripcion_corta'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $presupuesto = $_POST['presupuesto'] ?? 0;
    $repositorio_url = $_POST['repositorio_url'] ?? '';

    if (!$proyecto_id || empty($titulo) || empty($descripcion)) {
        echo "<script>alert('Faltan datos obligatorios.'); window.history.back();</script>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE proyectos SET 
            titulo = ?, 
            descripcion = ?, 
            descripcion_corta = ?, 
            tipo = ?, 
            fecha_inicio = ?, 
            fecha_fin = ?, 
            presupuesto = ?, 
            repositorio_url = ?, 
            fecha_actualizacion = NOW()
            WHERE id = ?");

        $stmt->execute([
            $titulo,
            $descripcion,
            $descripcion_corta,
            $tipo,
            $fecha_inicio,
            $fecha_fin,
            $presupuesto,
            $repositorio_url,
            $proyecto_id
        ]);

        echo "<script>alert('Proyecto actualizado correctamente.'); window.location.href = '../api/proyectos_tutor.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error al actualizar: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Método no permitido'); window.location.href = '../api/proyectos_tutor.php';</script>";
}
?>

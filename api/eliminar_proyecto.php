<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    echo "<script>alert('Acceso no autorizado.'); window.location.href = '../index.html';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proyecto_id = $_POST['proyecto_id'] ?? null;

    if (!$proyecto_id) {
        echo "<script>alert('ID de proyecto no proporcionado.'); window.history.back();</script>";
        exit;
    }

    try {
        // Eliminar participantes del proyecto primero si hay restricción de integridad
        $stmtParticipantes = $pdo->prepare("DELETE FROM proyecto_participantes WHERE proyecto_id = ?");
        $stmtParticipantes->execute([$proyecto_id]);

        // Luego eliminar el proyecto
        $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id = ?");
        $stmt->execute([$proyecto_id]);

        echo "<script>alert('Proyecto eliminado correctamente.'); window.location.href = '../api/proyectos_tutor.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar proyecto: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Método no permitido.'); window.location.href = '../api/proyectos_tutor.php';</script>";
}
?>

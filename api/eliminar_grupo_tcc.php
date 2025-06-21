<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    header("Location: ../html/index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grupo_id = $_POST['grupo_id'] ?? null;

    if ($grupo_id) {
        try {
            $pdo->beginTransaction();

            // Eliminar relaciones (miembros y documentos)
            $pdo->prepare("DELETE FROM grupos_tcc_miembros WHERE grupo_id = ?")->execute([$grupo_id]);
            $pdo->prepare("DELETE FROM tcc_documentos WHERE grupo_id = ?")->execute([$grupo_id]);

            // Eliminar grupo
            $pdo->prepare("DELETE FROM grupos_tcc WHERE id = ?")->execute([$grupo_id]);

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            die("Error al eliminar grupo: " . $e->getMessage());
        }
    }
}

header("Location: ../html/tcc_tutor.php");
exit;

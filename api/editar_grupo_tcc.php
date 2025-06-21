<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    header("Location: ../html/index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grupo_id = $_POST['grupo_id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $titulo = $_POST['titulo_tcc'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if ($grupo_id && $nombre && $titulo && $descripcion) {
        $stmt = $pdo->prepare("UPDATE grupos_tcc SET nombre = ?, titulo_tcc = ?, descripcion = ? WHERE id = ?");
        $stmt->execute([$nombre, $titulo, $descripcion, $grupo_id]);
    }
}

header("Location: ../html/tcc_tutor.php");
exit;

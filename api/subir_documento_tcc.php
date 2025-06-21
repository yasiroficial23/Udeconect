<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'estudiante') {
    echo "<script>alert('Acceso no autorizado.'); window.history.back();</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grupo_id = $_POST['grupo_id'] ?? '';
    $titulo = trim($_POST['titulo_documento'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $archivo = $_FILES['archivo'] ?? null;

    // Validaciones básicas
    if (empty($grupo_id) || empty($titulo) || empty($tipo) || !$archivo) {
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
        exit;
    }

    // Validar archivo
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Error al subir el archivo.'); window.history.back();</script>";
        exit;
    }

    if ($archivo['type'] !== 'application/pdf') {
        echo "<script>alert('Solo se permiten archivos PDF.'); window.history.back();</script>";
        exit;
    }

    // Procesar archivo
    $uploadsDir = '../uploads/tcc/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0775, true); // Crear carpeta si no existe
    }

    $nombre_archivo = basename($archivo['name']);
    $ruta_relativa = uniqid('tcc_') . '_' . $nombre_archivo;
    $ruta_completa = $uploadsDir . $ruta_relativa;

    if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
        echo "<script>alert('Error al guardar el archivo.'); window.history.back();</script>";
        exit;
    }

    // Guardar en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO tcc_documentos (grupo_id, titulo_documento, tipo, archivo_ruta, nombre_archivo, fecha_subida) 
                                VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $grupo_id,
            $titulo,
            $tipo,
            $ruta_relativa,
            $nombre_archivo
        ]);

        echo "<script>alert('Documento subido exitosamente.'); window.history.back();</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Error en la base de datos: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
}

// Si no es POST
echo "<script>alert('Método no permitido.'); window.history.back();</script>";
exit;


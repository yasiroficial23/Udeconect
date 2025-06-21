<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'estudiante') {
    echo "<script>alert('Acceso no autorizado.'); window.location.href = '../html/feed.php';</script>";
    exit;
}

$usuario_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proyecto_id = $_POST['proyecto_id'] ?? null;
    $presupuesto = $_POST['presupuesto'] ?? 0;
    $estado = $_POST['estado'] ?? '';
    $repositorio_url = $_POST['repositorio_url'] ?? '';
    $demo_url = $_POST['demo_url'] ?? '';
    $publico = isset($_POST['publico']) ? 1 : 0;
    $destacado = isset($_POST['destacado']) ? 1 : 0;

    if (!$proyecto_id || !$estado) {
        echo "<script>alert('Faltan campos obligatorios.'); window.location.href = '../html/proyectos_estudiante.php';</script>";
        exit;
    }

    try {
        // Verificar que el estudiante sea líder del proyecto
        $stmtVerif = $pdo->prepare("SELECT COUNT(*) FROM proyecto_participantes WHERE proyecto_id = ? AND usuario_id = ? AND rol = 'lider' AND activo = 1");
        $stmtVerif->execute([$proyecto_id, $usuario_id]);

        if ($stmtVerif->fetchColumn() == 0) {
            echo "<script>alert('No tienes permisos para editar este proyecto.'); window.location.href = '../html/proyectos_estudiante.php';</script>";
            exit;
        }

        // Subir imagen si se proporciona
        $imagen_id = null;
        if (!empty($_FILES['imagen']['name'])) {
            $uploadDir = '../uploads/proyectos/';
            $filename = uniqid('proy_') . '_' . basename($_FILES['imagen']['name']);
            $rutaCompleta = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
                // Guardar info de la imagen
                $stmtImg = $pdo->prepare("INSERT INTO imagenes (ruta, nombre_archivo, fecha_subida) VALUES (?, ?, NOW())");
                $stmtImg->execute([$filename, $_FILES['imagen']['name']]);
                $imagen_id = $pdo->lastInsertId();
            }
        }

        // Actualizar proyecto
        $sql = "UPDATE proyectos SET presupuesto = ?, estado = ?, repositorio_url = ?, demo_url = ?, publico = ?, destacado = ?, fecha_actualizacion = NOW()";
        $params = [$presupuesto, $estado, $repositorio_url, $demo_url, $publico, $destacado];

        if ($imagen_id) {
            $sql .= ", imagenPrincipal_id = ?";
            $params[] = $imagen_id;
        }

        $sql .= " WHERE id = ?";
        $params[] = $proyecto_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo "<script>alert('Proyecto actualizado exitosamente.'); window.location.href = '../html/proyectos_estudiante.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = '../html/proyectos_estudiante.php';</script>";
    }
} else {
    echo "<script>alert('Solicitud inválida.'); window.location.href = '../html/proyectos_estudiante.php';</script>";
}

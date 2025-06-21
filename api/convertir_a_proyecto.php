<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    echo "<script>alert('Acceso no autorizado.'); window.location.href='../index.html';</script>";
    exit;
}

$tutor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grupo_id = $_POST['grupo_id'] ?? null;
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $descripcion_corta = trim($_POST['descripcion_corta'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $presupuesto = $_POST['presupuesto'] ?? 0;
    $repositorio_url = trim($_POST['repositorio_url'] ?? '');

    if (!$grupo_id || !$titulo || !$descripcion || !$tipo) {
        echo "<script>alert('Faltan campos obligatorios.'); window.history.back();</script>";
        exit;
    }

    try {
        // Obtener centro tutorial del tutor
        $stmtCentro = $pdo->prepare("SELECT centro_tutorial_id FROM usuarios WHERE id = ?");
        $stmtCentro->execute([$tutor_id]);
        $centro = $stmtCentro->fetchColumn();

        if (!$centro) {
            throw new Exception("No se encontró centro tutorial para este tutor.");
        }

        // Iniciar transacción
        $pdo->beginTransaction();

        // Insertar el proyecto con grupo_tcc_id
        $stmtProyecto = $pdo->prepare("
            INSERT INTO proyectos 
            (grupo_tcc_id, titulo, descripcion, descripcion_corta, tipo, estado, fecha_inicio, fecha_fin, presupuesto, repositorio_url, centro_tutorial_id, tutor_id, fecha_creacion, fecha_actualizacion)
            VALUES (?, ?, ?, ?, ?, 'activo', ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmtProyecto->execute([
            $grupo_id, $titulo, $descripcion, $descripcion_corta, $tipo,
            $fecha_inicio, $fecha_fin, $presupuesto, $repositorio_url,
            $centro, $tutor_id
        ]);

        $proyecto_id = $pdo->lastInsertId();

        // Obtener integrantes del grupo
        $stmtIntegrantes = $pdo->prepare("SELECT usuario_id, rol FROM grupos_tcc_miembros WHERE grupo_id = ?");
        $stmtIntegrantes->execute([$grupo_id]);
        $integrantes = $stmtIntegrantes->fetchAll();

        // Insertar participantes
        $stmtParticipante = $pdo->prepare("
            INSERT INTO proyecto_participantes (proyecto_id, usuario_id, rol, fecha_ingreso, activo)
            VALUES (?, ?, ?, NOW(), 1)
        ");

        foreach ($integrantes as $i) {
            $stmtParticipante->execute([
                $proyecto_id,
                $i['usuario_id'],
                $i['rol']
            ]);
        }

        // Determinar autor de la publicación (primer estudiante del grupo)
        $autor_publicacion = $tutor_id;
        foreach ($integrantes as $i) {
            if ($i['rol'] === 'estudiante') {
                $autor_publicacion = $i['usuario_id'];
                break;
            }
        }

        // Insertar publicación relacionada
        $stmtPublicacion = $pdo->prepare("
            INSERT INTO publicaciones 
            (usuario_id, proyecto_id, tipo, titulo, contenido, publico, fecha_creacion) 
            VALUES (?, ?, 'proyecto', ?, ?, 1, NOW())
        ");
        $stmtPublicacion->execute([
            $autor_publicacion,
            $proyecto_id,
            $titulo,
            $descripcion_corta ?: $descripcion
        ]);

        // Confirmar transacción
        $pdo->commit();
        echo "<script>alert('Proyecto creado y publicación generada exitosamente.'); window.location.href='../html/tcc_tutor.php';</script>";
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<script>alert('Error al crear el proyecto: " . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('Método no permitido.'); window.location.href='../html/tcc_tutor.php';</script>";
    exit;
}

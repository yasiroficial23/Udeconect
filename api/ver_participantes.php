<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    echo "<script>alert('Acceso no autorizado.'); window.location.href = '../index.html';</script>";
    exit;
}

$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$proyecto_id) {
    echo "<script>alert('ID de proyecto no proporcionado.'); window.history.back();</script>";
    exit;
}

// Obtener información del proyecto
$stmtProyecto = $pdo->prepare("SELECT titulo FROM proyectos WHERE id = ?");
$stmtProyecto->execute([$proyecto_id]);
$proyecto = $stmtProyecto->fetch();

if (!$proyecto) {
    echo "<script>alert('Proyecto no encontrado.'); window.history.back();</script>";
    exit;
}

// Obtener los participantes
$stmt = $pdo->prepare("
    SELECT u.nombre, u.apellidos, u.email, pp.rol, pp.fecha_ingreso 
    FROM proyecto_participantes pp
    JOIN usuarios u ON u.id = pp.usuario_id
    WHERE pp.proyecto_id = ?
");
$stmt->execute([$proyecto_id]);
$participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Participantes del Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h3 class="mb-4">Participantes del Proyecto: <span class="text-primary"><?= htmlspecialchars($proyecto['titulo']) ?></span></h3>

        <?php if (count($participantes) === 0): ?>
            <div class="alert alert-info">Este proyecto aún no tiene participantes registrados.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha de Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participantes as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?></td>
                            <td><?= htmlspecialchars($p['email']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($p['rol'])) ?></td>
                            <td><?= htmlspecialchars($p['fecha_ingreso']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="../api/proyectos_tutor.php" class="btn btn-secondary mt-3">← Volver a proyectos</a>
    </div>
</body>
</html>

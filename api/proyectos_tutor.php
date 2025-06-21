<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    header('Location: ../index.html');
    exit;
}

$tutor_id = $_SESSION['user_id'];

// Obtener los proyectos creados por este tutor
$stmt = $pdo->prepare("SELECT * FROM proyectos WHERE tutor_id = ? ORDER BY fecha_creacion DESC");
$stmt->execute([$tutor_id]);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Proyectos de Investigación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
$usuarioNombre = $_SESSION['nombre'] ?? 'Invitado';
$usuarioRol = $_SESSION['rol'] ?? 'Desconocido';
?>
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-primary">Mis Proyectos</span>

        <div class="d-flex ms-auto align-items-center">
            <span class="me-3 fw-semibold">
                Bienvenido, <?= htmlspecialchars($usuarioNombre) ?> | <?= htmlspecialchars(ucfirst($usuarioRol)) ?>
            </span>
            <a href="../html/feed.php" class="btn btn-outline-primary btn-sm me-2">Volver al inicio</a>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-4">
    <h2 class="mb-4">Proyectos de Investigación</h2>

    <?php if (count($proyectos) === 0): ?>
        <div class="alert alert-info">Aún no tienes proyectos registrados.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($proyectos as $proyecto): ?>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong><?= htmlspecialchars($proyecto['titulo']) ?></strong>
                            <span class="badge bg-light text-dark ms-2"><?= $proyecto['tipo'] ?></span>
                        </div>
                        <div class="card-body">
                            <p><?= htmlspecialchars($proyecto['descripcion_corta']) ?></p>
                            <p><strong>Estado:</strong> <?= $proyecto['estado'] ?></p>
                            <p><strong>Presupuesto:</strong> $<?= number_format($proyecto['presupuesto']) ?></p>
                            <?php if ($proyecto['repositorio_url']): ?>
                                <p><a href="<?= htmlspecialchars($proyecto['repositorio_url']) ?>" target="_blank">Repositorio</a></p>
                            <?php endif; ?>
                            <?php if ($proyecto['demo_url']): ?>
                                <p><a href="<?= htmlspecialchars($proyecto['demo_url']) ?>" target="_blank">Demo</a></p>
                            <?php endif; ?>
                            <p class="text-muted small">Creado: <?= $proyecto['fecha_creacion'] ?></p>

                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $proyecto['id'] ?>">Editar</button>
                                <form action="../api/eliminar_proyecto.php" method="POST" onsubmit="return confirm('Seguro que deseas eliminar este proyecto?')">
                                    <input type="hidden" name="proyecto_id" value="<?= $proyecto['id'] ?>">
                                    <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                                </form>
                                <a href="ver_participantes.php?proyecto_id=<?= $proyecto['id'] ?>" class="btn btn-info btn-sm">
    Ver Participantes
</a>

                            </div>
                        </div>
                    </div>

                    <!-- Modal Editar Proyecto -->
                    <div class="modal fade" id="modalEditar<?= $proyecto['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="../api/editar_proyecto.php" method="POST" class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Editar Proyecto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="proyecto_id" value="<?= $proyecto['id'] ?>">
                                    <div class="mb-3">
                                        <label>Título</label>
                                        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($proyecto['titulo']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Descripción corta</label>
                                        <input type="text" name="descripcion_corta" class="form-control" value="<?= htmlspecialchars($proyecto['descripcion_corta']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Descripción extendida</label>
                                        <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($proyecto['descripcion']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Estado</label>
                                        <select name="estado" class="form-select">
                                            <option value="activo" <?= $proyecto['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                            <option value="finalizado" <?= $proyecto['estado'] === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                            <option value="suspendido" <?= $proyecto['estado'] === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'estudiante') {
    header('Location: ../html/index.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];
$usuario_nombre = $_SESSION['nombre'] ?? 'Estudiante';

// Buscar proyectos en los que participa el estudiante
$stmtProyectos = $pdo->prepare("SELECT p.*, pp.rol AS rol_participante
                                FROM proyectos p
                                JOIN proyecto_participantes pp ON p.id = pp.proyecto_id
                                WHERE pp.usuario_id = ? AND pp.activo = 1");
$stmtProyectos->execute([$usuario_id]);
$proyectos = $stmtProyectos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proyectos de Investigación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-primary">Proyectos de investigación - Estudiante</span>
        <div class="d-flex ms-auto align-items-center">
            <span class="me-3 fw-semibold">
                Bienvenido, <?= htmlspecialchars($usuario_nombre) ?> | Estudiante
            </span>
            <a href="../html/feed.php" class="btn btn-outline-primary btn-sm me-2">Volver al inicio</a>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-4">
    <h2 class="mb-4">Mis Proyectos de Investigación</h2>

    <?php if (empty($proyectos)): ?>
        <div class="alert alert-info">Aún no participas en ningún proyecto.</div>
    <?php endif; ?>

    <?php foreach ($proyectos as $proyecto): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong><?= htmlspecialchars($proyecto['titulo']) ?></strong>
                <span class="badge bg-light text-dark ms-3">Estado: <?= $proyecto['estado'] ?></span>
            </div>
            <div class="card-body">
                <p><strong>Descripción corta:</strong> <?= htmlspecialchars($proyecto['descripcion_corta']) ?></p>
                <p><strong>Descripción extendida:</strong> <?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                <p><strong>Tipo:</strong> <?= ucfirst($proyecto['tipo']) ?></p>
                <p><strong>Fechas:</strong> <?= $proyecto['fecha_inicio'] ?> → <?= $proyecto['fecha_fin'] ?></p>
                <p><strong>Presupuesto:</strong> $<?= number_format($proyecto['presupuesto'], 2) ?></p>
                <p><strong>Repositorio:</strong> <a href="<?= htmlspecialchars($proyecto['repositorio_url']) ?>" target="_blank"><?= $proyecto['repositorio_url'] ?></a></p>
                <p><strong>Demo:</strong> <a href="<?= htmlspecialchars($proyecto['demo_url']) ?>" target="_blank"><?= $proyecto['demo_url'] ?></a></p>
                <p><strong>Visible al público:</strong> <?= $proyecto['publico'] ? 'Sí' : 'No' ?></p>
                <p><strong>Destacado:</strong> <?= $proyecto['destacado'] ? 'Sí' : 'No' ?></p>

                <?php if (!empty($proyecto['imagen_principal_id'])): ?>
                    <p><strong>Imagen:</strong> 
                        <a href="../uploads/proyectos/<?= htmlspecialchars($proyecto['imagen_principal_id']) ?>" target="_blank" class="btn btn-sm btn-outline-info">Ver imagen</a>
                    </p>
                <?php else: ?>
                    <p><strong>Imagen:</strong> No existe imagen</p>
                <?php endif; ?>

                <?php if ($proyecto['rol_participante'] === 'lider'): ?>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarProyecto<?= $proyecto['id'] ?>">
                        ✏️ Editar Proyecto
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="modalEditarProyecto<?= $proyecto['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="../api/editar_proyecto_estudiante.php" method="POST" class="modal-content" enctype="multipart/form-data">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Editar Proyecto: <?= htmlspecialchars($proyecto['titulo']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="proyecto_id" value="<?= $proyecto['id'] ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Presupuesto</label>
                                        <input type="number" name="presupuesto" class="form-control" step="0.01"
                                               value="<?= htmlspecialchars($proyecto['presupuesto']) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <select name="estado" class="form-select" required>
                                            <option value="planificacion" <?= $proyecto['estado'] === 'planificacion' ? 'selected' : '' ?>>Planificación</option>
                                            <option value="desarrollo" <?= $proyecto['estado'] === 'desarrollo' ? 'selected' : '' ?>>Desarrollo</option>
                                            <option value="finalizado" <?= $proyecto['estado'] === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">URL Repositorio</label>
                                        <input type="url" name="repositorio_url" class="form-control"
                                               value="<?= htmlspecialchars($proyecto['repositorio_url']) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">URL Demo</label>
                                        <input type="url" name="demo_url" class="form-control"
                                               value="<?= htmlspecialchars($proyecto['demo_url']) ?>">
                                    </div>

                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" name="publico" id="publico<?= $proyecto['id'] ?>"
                                               <?= $proyecto['publico'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="publico<?= $proyecto['id'] ?>">Visible al público</label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" name="destacado" id="destacado<?= $proyecto['id'] ?>"
                                               <?= $proyecto['destacado'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="destacado<?= $proyecto['id'] ?>">Proyecto destacado</label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Imagen Principal (opcional)</label>
                                        <input type="file" name="imagen" class="form-control" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

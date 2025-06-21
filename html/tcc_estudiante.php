<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'estudiante') {
    header('Location: ../index.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];

// Buscar el grupo al que pertenece el estudiante
$stmtGrupo = $pdo->prepare("SELECT gt.* FROM grupos_tcc_miembros gm 
                            JOIN grupos_tcc gt ON gt.id = gm.grupo_id 
                            WHERE gm.usuario_id = ? AND gm.activo = 1 LIMIT 1");
$stmtGrupo->execute([$usuario_id]);
$grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

$isLider = false;
$integrantes = [];
$documentos = [];

if ($grupo) {
    // Verificar si el usuario es líder
    $stmtRol = $pdo->prepare("SELECT rol FROM grupos_tcc_miembros WHERE grupo_id = ? AND usuario_id = ?");
    $stmtRol->execute([$grupo['id'], $usuario_id]);
    $rol = $stmtRol->fetchColumn();
    $isLider = ($rol === 'lider');

    // Obtener integrantes
    $stmtIntegrantes = $pdo->prepare("SELECT u.nombre, u.apellidos, gm.rol FROM grupos_tcc_miembros gm 
                                        JOIN usuarios u ON u.id = gm.usuario_id 
                                        WHERE gm.grupo_id = ?");
    $stmtIntegrantes->execute([$grupo['id']]);
    $integrantes = $stmtIntegrantes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener documentos
    $stmtDocs = $pdo->prepare("SELECT * FROM tcc_documentos WHERE grupo_id = ?");
    $stmtDocs->execute([$grupo['id']]);
    $documentos = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Grupo TCC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php
$usuarioNombre = $_SESSION['nombre'] ?? 'Invitado';
$usuarioRol = $_SESSION['rol'] ?? 'Desconocido';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid">
        <!-- Botón menú si usas sidebar (puedes eliminar si no lo necesitas) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Bienvenida y rol -->
        <div class="d-flex align-items-center flex-grow-1 ms-3">
            <span class="fw-semibold text-dark">Bienvenido, <?= htmlspecialchars($usuarioNombre) ?> | <?= ucfirst(htmlspecialchars($usuarioRol)) ?></span>
        </div>

        <!-- Botones -->
        <div class="d-flex align-items-center gap-2 me-2">
            <a href="../html/feed.php" class="btn btn-outline-primary btn-sm">Volver al inicio</a>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>

    <div class="container mt-5 pt-4">

        <h2 class="mb-4">Mi Grupo TCC</h2>

        <?php if (!$grupo): ?>
            <div class="alert alert-warning">No estás registrado en ningún grupo TCC.</div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <strong><?= htmlspecialchars($grupo['nombre']) ?></strong> - Estado: <em><?= $grupo['estado_tcc'] ?></em>
                </div>
                <div class="card-body">
                    <p><strong>Título del TCC:</strong> <?= htmlspecialchars($grupo['titulo_tcc']) ?></p>
                    <p><strong>Descripción:</strong> <?= htmlspecialchars($grupo['descripcion']) ?></p>
                </div>
            </div>

            <h5>Integrantes del grupo</h5>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($integrantes as $int): ?>
                        <tr>
                            <td><?= htmlspecialchars($int['nombre'] . ' ' . $int['apellidos']) ?></td>
                            <td><?= ucfirst($int['rol']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h5>Documentos del TCC</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Archivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentos as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['titulo_documento']) ?></td>
                            <td><?= $doc['tipo'] ?></td>
                            <td><?= $doc['fecha_subida'] ?></td>
                            <td><a href="../uploads/tcc/<?= htmlspecialchars($doc['archivo_ruta']) ?>" target="_blank">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($isLider): ?>
                <hr>
                <h5>Subir nuevo documento</h5>
                <form action="../api/subir_documento_tcc.php" method="POST" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                    <div class="col-md-6">
                        <label class="form-label">Título del documento</label>
                        <input type="text" name="titulo_documento" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            <option value="planteamiento">Planteamiento</option>
                            <option value="avance">Avance</option>
                            <option value="diseño">Diseño</option>
                            <option value="implementación">Implementación</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Archivo (PDF)</label>
                        <input type="file" name="archivo" class="form-control" accept="application/pdf" required>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Subir</button>
                        <br><br><br>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>

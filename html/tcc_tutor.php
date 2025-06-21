<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    header('Location: ../index.html');
    exit;
}

$tutor_id = $_SESSION['user_id'];

// Obtener grupos asignados al tutor
$stmtGrupos = $pdo->prepare("SELECT * FROM grupos_tcc WHERE tutor_id = ?");
$stmtGrupos->execute([$tutor_id]);
$grupos = $stmtGrupos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- jQuery y jQuery UI para autocomplete -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Grupos TCC - Tutor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php
$usuarioNombre = $_SESSION['nombre'] ?? 'Invitado';
$usuarioRol = $_SESSION['rol'] ?? 'Desconocido';
?>
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-primary">Panel TCC - Tutor</span>
        
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


        <h2 class="mb-4">Crear Nuevo Grupo TCC</h2>
        <form action="../api/crear_grupo_tcc.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre del Grupo</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Título del TCC</label>
                <input type="text" name="titulo_tcc" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>

            <h5>Integrantes del Grupo</h5>
            <div id="integrantes-container">
                <div class="row g-3 align-items-end integrante-item mb-2">
                    <div class="col-md-6">
                        <label>Email del Estudiante</label>
                        <input type="email" name="integrantes[0][email]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Rol</label>
                        <select name="integrantes[0][rol]" class="form-select" required>
                            <option value="lider">Líder</option>
                            <option value="colaborador">Colaborador</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-remove">✕</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-integrante">+ Añadir Integrante</button>

            <button type="submit" class="btn btn-primary">Crear Grupo</button>
        </form>

        <hr class="my-5">

        <h2 class="mb-4">Grupos TCC Asignados</h2>

        <?php if (count($grupos) === 0): ?>
            <div class="alert alert-info">No tienes grupos asignados aún.</div>
        <?php endif; ?>

        <?php foreach ($grupos as $grupo): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <strong><?= htmlspecialchars($grupo['nombre']) ?></strong>
                    <span class="badge bg-light text-dark ms-3">Estado: <?= $grupo['estado_tcc'] ?></span>
                </div>
                <div class="card-body">
                    <p><strong>Título TCC:</strong> <?= htmlspecialchars($grupo['titulo_tcc']) ?></p>
                    <p><strong>Descripción:</strong> <?= htmlspecialchars($grupo['descripcion']) ?></p>

                    <h6>Integrantes:</h6>
                    <ul>
                        <?php
                        $stmtIntegrantes = $pdo->prepare("SELECT u.nombre, u.apellidos, gm.rol FROM grupos_tcc_miembros gm 
                                                            JOIN usuarios u ON gm.usuario_id = u.id 
                                                            WHERE gm.grupo_id = ?");
                        $stmtIntegrantes->execute([$grupo['id']]);
                        foreach ($stmtIntegrantes as $int): ?>
                            <li><?= htmlspecialchars($int['nombre'] . ' ' . $int['apellidos']) ?> (<?= $int['rol'] ?>)</li>
                        <?php endforeach; ?>
                    </ul>

                    <h6>Documentos Subidos:</h6>
                    <ul>
                        <?php
                        $stmtDocs = $pdo->prepare("SELECT * FROM tcc_documentos WHERE grupo_id = ?");
                        $stmtDocs->execute([$grupo['id']]);
                        foreach ($stmtDocs as $doc): ?>
                            <li><a href="../uploads/tcc/<?= urlencode($doc['archivo_ruta']) ?>" target="_blank">📄 <?= $doc['titulo_documento'] ?> (<?= $doc['tipo'] ?>)</a></li>
                        <?php endforeach; ?>
                    </ul>

                    <form method="POST" action="../api/evaluar_tcc.php" class="mt-3">
                        <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="viable" value="1" id="viable<?= $grupo['id'] ?>" required>
                            <label class="form-check-label" for="viable<?= $grupo['id'] ?>">Marcar como Viable</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="viable" value="0" id="noViable<?= $grupo['id'] ?>">
                            <label class="form-check-label" for="noViable<?= $grupo['id'] ?>">No Viable</label>
                        </div>
                        <div class="mt-2">
                            <label>Observaciones:</label>
                            <textarea name="observaciones" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm mt-2">Guardar Evaluación</button>
                    </form>

                    <!-- Botón editar fuera del form -->
                    <button type="button" class="btn btn-warning btn-sm mt-2 me-2" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $grupo['id'] ?>">Editar Grupo</button>

                    <!-- Form eliminar separado -->
                    <form action="../api/eliminar_grupo_tcc.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este grupo?')" style="display:inline;">
                        <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm mt-2">Eliminar Grupo</button>
                    </form>
                    <!-- ...Tu código previo permanece intacto... -->

            <!-- Botón Convertir a Proyecto -->
            <?php if ($grupo['estado_tcc'] === 'viable'): ?>
                <button class="btn btn-info btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalProyecto<?= $grupo['id'] ?>">
                    Convertir a Proyecto de investigación 
                </button>
            <?php endif; ?>

            <!-- Modal Convertir a Proyecto -->
            <div class="modal fade" id="modalProyecto<?= $grupo['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form action="../api/convertir_a_proyecto.php" method="POST" class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">Convertir TCC en Proyecto de investigación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">
                            <div class="mb-3">
                                <label>Título del Proyecto</label>
                                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($grupo['titulo_tcc']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Descripción Corta</label>
                                <input type="text" name="descripcion_corta" class="form-control" maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label>Descripción Extendida</label>
                                <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($grupo['descripcion']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Tipo de Proyecto</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="investigacion">Investigación</option>
                                    <option value="desarrollo">Desarrollo</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Fecha de Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Fecha de Finalización</label>
                                    <input type="date" name="fecha_fin" class="form-control">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label>Presupuesto Estimado</label>
                                <input type="number" name="presupuesto" class="form-control" step="0.01">
                            </div>
                            <div class="mt-3">
                                <label>Repositorio (URL)</label>
                                <input type="url" name="repositorio_url" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Crear Proyecto</button>
                        </div>
                    </form>
                </div>
            </div>

                    <!-- Modal Editar Grupo -->
                    <div class="modal fade" id="modalEditar<?= $grupo['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="../api/editar_grupo_tcc.php" method="POST" class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Editar Grupo: <?= htmlspecialchars($grupo['nombre']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="grupo_id" value="<?= $grupo['id'] ?>">

                                    <div class="mb-3">
                                        <label>Nombre del Grupo</label>
                                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($grupo['nombre']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Título del TCC</label>
                                        <input type="text" name="titulo_tcc" class="form-control" value="<?= htmlspecialchars($grupo['titulo_tcc']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($grupo['descripcion']) ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        let contador = 1;
        document.getElementById('add-integrante').addEventListener('click', function () {
            const container = document.getElementById('integrantes-container');
            const html = `
            <div class="row g-3 align-items-end integrante-item mb-2">
                <div class="col-md-6">
                    <input type="email" name="integrantes[${contador}][email]" class="form-control" placeholder="Email del Estudiante" required>
                </div>
                <div class="col-md-4">
                    <select name="integrantes[${contador}][rol]" class="form-select" required>
                        <option value="lider">Líder</option>
                        <option value="colaborador">Colaborador</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-remove">✕</button>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
            contador++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-remove')) {
                e.target.closest('.integrante-item').remove();
            }
        });

        function activarAutocomplete() {
            document.querySelectorAll('input[type="email"]').forEach(input => {
                $(input).autocomplete({
                    source: '../api/buscar_estudiantes.php',
                    minLength: 2,
                });
            });
        }

        activarAutocomplete();

        document.getElementById('add-integrante').addEventListener('click', () => {
            setTimeout(() => {
                activarAutocomplete();
            }, 100);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
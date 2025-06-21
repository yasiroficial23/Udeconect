<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.html');
    exit;
}
require_once '../api/db_connect.php';

// Obtener el nombre del usuario que inició sesión
$stmtUser = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmtUser->execute([$_SESSION['user_id']]);
$nombreUsuario = $stmtUser->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Certificados - Vitrina de Talento</title>
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('../img/unicartagena.jpg');
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .dashboard {
            background: #fff;
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .dashboard h2 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .dashboard p {
            color: #555;
            margin-bottom: 30px;
        }
        .dashboard .btn-primary {
            background: linear-gradient(to right, #8e2de2, #4a00e0);
            border: none;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }
        .stat-box {
            background: #f4f4f4;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            margin: 0 10px;
        }
        .stat-box h4 {
            font-size: 28px;
            color: #4a00e0;
        }
        .top-bar {
            background: rgba(59, 57, 57, 0.5);
            position: absolute;
            top: 20px;
            left: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
        }
        .top-right-buttons {
            background: rgba(59, 57, 57, 0.5);
            position: absolute;
            top: 20px;
            right: 30px;
            display: flex;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
        }
        .btn.btn-outline-light{
            background-color: white;
            color: black;
        }
        .btn.btn-outline-light:hover {
            background-color: rgba(59, 57, 57, 0.5);
            color: white;
            border-color: rgba(59, 57, 57, 0.5);
        }
         /* Fondo del modal (si quieres cambiar el oscuro de Bootstrap) */
        #modalRegistrar .modal-backdrop.fade.show {
            background-color: rgba(0, 0, 0, 0.6); /* Un poco más oscuro que el predeterminado si lo deseas */
        }

        /* Estilo para el contenido del modal (el recuadro blanco) */
        #modalRegistrar .modal-content {
            border-radius: 10px; /* Bordes redondeados para el modal */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); /* Sombra sutil */
            border: none; /* Quitar el borde predeterminado */
        }

        /* Encabezado del Modal */
        #modalRegistrar .modal-header {
            background-color: #6f42c1; /* Color de fondo similar al botón "Registrar Estudiante" */
            color: #ffffff; /* Texto blanco para el título */
            border-bottom: 1px solid #5a369e; /* Borde inferior ligeramente más oscuro */
            border-top-left-radius: 10px; /* Redondear esquinas superiores para coincidir con modal-content */
            border-top-right-radius: 10px;
            padding: 1rem 1.5rem; /* Ajustar padding */
        }

        #modalRegistrar .modal-header .modal-title {
            font-size: 1.5rem; /* Tamaño de fuente del título */
            font-weight: 600; /* Negrita */
        }

        /* Botón de cerrar (la 'x') en el encabezado del modal */
        #modalRegistrar .modal-header .btn-close {
            filter: invert(1) grayscale(1) brightness(2); /* Hace la 'x' blanca para que contraste con el fondo oscuro */
            opacity: 0.8; /* Un poco de transparencia */
            transition: opacity 0.2s ease;
        }

        #modalRegistrar .modal-header .btn-close:hover {
            opacity: 1; /* Al pasar el ratón, la 'x' es completamente opaca */
        }

        /* Cuerpo del Modal (Formulario) */
        #modalRegistrar .modal-body {
            padding: 1.5rem; /* Padding interno para el cuerpo del formulario */
        }

        #modalRegistrar .modal-body label {
            font-weight: 500; /* Un poco más de negrita para las etiquetas */
            color: #333; /* Color de texto más oscuro para las etiquetas */
            margin-bottom: .5rem; /* Espacio debajo de la etiqueta */
            display: block; /* Asegura que la etiqueta esté en su propia línea */
        }

        /* Estilo para los campos de entrada de texto (inputs y selects) */
        #modalRegistrar .modal-body .form-control,
        #modalRegistrar .modal-body .form-select {
            border: 1px solid #ced4da; /* Borde estándar */
            border-radius: 5px; /* Bordes ligeramente redondeados */
            padding: 0.75rem 1rem; /* Padding interno */
            font-size: 1rem; /* Tamaño de fuente */
            transition: border-color 0.2s ease, box-shadow 0.2s ease; /* Transición suave para el focus */
        }

        #modalRegistrar .modal-body .form-control:focus,
        #modalRegistrar .modal-body .form-select:focus {
            border-color: #6f42c1; /* Borde del color principal al enfocar */
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25); /* Sombra al enfocar (similar a Bootstrap) */
        }

        /* Pie del Modal */
        #modalRegistrar .modal-footer {
            background-color: #f8f9fa; /* Un gris muy claro para el pie */
            border-top: 1px solid #dee2e6; /* Borde superior sutil */
            border-bottom-left-radius: 10px; /* Redondear esquinas inferiores */
            border-bottom-right-radius: 10px;
            padding: 1rem 1.5rem; /* Ajustar padding */
        }

        /* Estilo para el botón "Guardar" en el pie del modal */
        #modalRegistrar .modal-footer .btn-primary {
            background-color: #6f42c1; /* Color principal del botón */
            border-color: #6f42c1; /* Color del borde del botón */
            color: #ffffff; /* Texto blanco */
            padding: 0.75rem 1.25rem; /* Padding más generoso */
            font-size: 1rem;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        #modalRegistrar .modal-footer .btn-primary:hover {
            background-color: #5a369e; /* Un tono más oscuro al pasar el ratón */
            border-color: #5a369e;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.35); /* Sombra ligeramente más intensa */
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <i class="fas fa-user-circle fa-lg"></i> Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?>
    </div>
<br>
    <div class="top-right-buttons">
        <a href="feed.php" class="btn btn-outline-light">Volver al inicio</a>
        <a href="../logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>

    <div class="dashboard">
        <img src="../img/udec_log.png" alt="Logo"  width="180px" class="mb-3"> 
        <br><br>
        <h2>Bienvenido a Click Document</h2>
        <p>Gestiona de manera eficiente el registro de estudiantes y la emisión de certificados de bienestar universitario de la Universidad de Cartagena.</p>
        <button class="btn btn-primary w-75 p-2 mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">Registrar Estudiante</button>
        <button class="btn btn-outline-primary w-75 p-2" data-bs-toggle="modal" data-bs-target="#modalConsultar">Buscar Estudiante</button>

        <div class="stats">
            <div class="stat-box">
                <h4 id="countEstudiantes">-</h4>
                <p>Estudiantes Registrados</p>
            </div>
            <div class="stat-box">
                <h4 id="countCertificados">-</h4>
                <p>Certificados Emitidos</p>
            </div>
        </div>
    </div>


    <!-- Modal: Registrar Estudiante -->
    <div class="modal fade" id="modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-lg"> <form class="modal-content" id="formRegistrar">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Estudiante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3"> <label for="nombres">Nombre</label>
                        <input type="text" name="nombres" id="nombres" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="codigo_estudiantil">Código Estudiantil</label>
                        <input type="text" name="codigo_estudiantil" id="codigo_estudiantil" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="centro_tutorial_id">Centro Tutorial</label>
                        <select name="centro_tutorial_id" id="centro_tutorial_id" class="form-select" required>
                            <option value="">Selecciona un centro</option>
                        <?php $stmt = $pdo->query("SELECT id, nombre FROM centros_tutoriales");
                        while ($row = $stmt->fetch()) { echo "<option value='{$row['id']}'>{$row['nombre']}</option>"; } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

    <!-- Modal: Consultar Estudiante -->
    <div class="modal fade" id="modalConsultar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Consultar por Código Estudiantil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <input type="text" id="codigoBusqueda" class="form-control" placeholder="Ingrese el código estudiantil">
                        <button class="btn btn-primary" id="btnBuscar">Buscar</button>
                    </div>
                    <div id="resultadoConsulta">
                        <!-- Tabla con resultados dinámicos -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Estudiante -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="formEditar">
      <div class="modal-header">
        <h5 class="modal-title">Editar Estudiante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="editar_id">
        <div class="mb-3">
          <label for="editar_nombres">Nombres</label>
          <input type="text" class="form-control" name="nombres" id="editar_nombres" required>
        </div>
        <div class="mb-3">
          <label for="editar_apellidos">Apellidos</label>
          <input type="text" class="form-control" name="apellidos" id="editar_apellidos" required>
        </div>
        <div class="mb-3">
          <label for="editar_email">Email</label>
          <input type="email" class="form-control" name="email" id="editar_email" required>
        </div>
        <div class="mb-3">
          <label for="editar_centro">Centro Tutorial</label>
          <select class="form-select" name="centro_tutorial_id" id="editar_centro" required>
            <option value="">Seleccione...</option>
            <?php
            $stmt = $pdo->query("SELECT id, nombre FROM centros_tutoriales");
            while ($row = $stmt->fetch()) {
              echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Registro real del estudiante
        document.getElementById('formRegistrar').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            const response = await fetch('../api/registrar_estudiante.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message);
                form.reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalRegistrar'));
                modal.hide();
            } else {
                alert('Error: ' + data.message);
            }
        });

        // Consulta real del estudiante
        document.getElementById('btnBuscar').addEventListener('click', async () => {
            const codigo = document.getElementById('codigoBusqueda').value.trim();
            const resultadoDiv = document.getElementById('resultadoConsulta');
            resultadoDiv.innerHTML = '';

            if (!codigo) {
                alert("Por favor ingresa un código estudiantil.");
                return;
            }

            const response = await fetch(`../api/consultar_estudiante.php?codigo=${encodeURIComponent(codigo)}`);
            const result = await response.json();

            if (result.success) {
                const est = result.data;
                resultadoDiv.innerHTML = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Certificado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${est.nombres}</td>
                                <td>${est.apellidos}</td>
                                <td>${est.email}</td>
                                <td>
                                    <input type="file" class="form-control" accept=".pdf" onchange="subirCertificado(event, ${est.id})">
                                </td>
                    
                                <td>
                                    ${est.certificado_nombre 
                                    ? `<a href="../api/descargar_certificado.php?id=${est.usuario_id}" class="btn btn-sm btn-success me-1" target="_blank">Descargar</a>` 
                                    + `<button class="btn btn-sm btn-info" onclick="verCertificado(${est.usuario_id})">Ver</button>`
                                    : 'Sin certificado'}
                                
                                    <button class="btn btn-sm btn-warning" onclick='abrirModalEditar(${JSON.stringify(est)})'>Editar</button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarEstudiante('${est.codigo_estudiantil}')">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                `;
            } else {
                resultadoDiv.innerHTML = `<div class="alert alert-warning">${result.message}</div>`;
            }
        });

        // Cargar conteo de estudiantes y certificados
        (async () => {
            const estudiantesRes = await fetch('../api/contar_estudiantes.php');
            const certificadosRes = await fetch('../api/contar_certificados.php');
            const estData = await estudiantesRes.json();
            const certData = await certificadosRes.json();
            document.getElementById('countEstudiantes').textContent = estData.total || 0;
            document.getElementById('countCertificados').textContent = certData.total || 0;
        })();

        // Función para eliminar estudiante (placeholder, implementar lógica real)
        async function eliminarEstudiante(codigo) {
    if (!confirm("¿Estás seguro de eliminar este estudiante?")) return;

    const response = await fetch('../api/eliminar_estudiante.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `codigo_estudiantil=${encodeURIComponent(codigo)}`
    });

    const result = await response.json();
    alert(result.message);

    if (result.success) {
        document.getElementById('btnBuscar').click(); // Vuelve a buscar para actualizar tabla
    }
}
function abrirModalEditar(est) {
    document.getElementById('editar_id').value = est.id;
    document.getElementById('editar_nombres').value = est.nombres;
    document.getElementById('editar_apellidos').value = est.apellidos;
    document.getElementById('editar_email').value = est.email;

  // Si tienes el centro en el JSON, así:
    if (est.centro_tutorial_id) {
    document.getElementById('editar_centro').value = est.centro_tutorial_id;}

const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
modal.show();
}

document.getElementById('formEditar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    const response = await fetch('../api/editar_estudiante.php', {
    method: 'POST',
    body: formData
    });

    const result = await response.json();
    alert(result.message);

    if (result.success) {
    bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
    document.getElementById('btnBuscar').click(); // recargar tabla
    }
});

// funcion subir certificado
function subirCertificado(event, estudianteId) {
    const archivo = event.target.files[0];

    if (!archivo || archivo.type !== "application/pdf") {
        alert("Por favor selecciona un archivo PDF válido.");
        return;
    }

    const formData = new FormData();
    formData.append("certificado", archivo);
    formData.append("estudiante_id", estudianteId);

    fetch('../api/subir_certificado.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        document.getElementById('btnBuscar').click(); // refresca la tabla
    })
    .catch(err => {
        alert("Error al subir el certificado.");
        console.error(err);
    });
}

//ver certificado
function verCertificado(id) {
    const url = `../api/ver_certificado.php?id=${id}`;
    const win = window.open(url, '_blank');
    win.focus();
}


    </script>
</body>
</html>
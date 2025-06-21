<?php 
require_once '../api/db_connect.php';
session_start();

$usuarioRol = $_SESSION['rol'] ?? 'invitado';
$redirectUrl = 'index.html';

if (isset($_SESSION['rol'])) {
    $redirectUrl = ($_SESSION['rol'] === 'admin') 
        ? 'admin_certificados.php' 
        : (($_SESSION['rol'] === 'estudiante') 
            ? 'student_certificados.php' 
            : 'index.html');
}
$usuarioNombre = $_SESSION['nombre'] ?? 'Invitado';
$usuarioRol = $_SESSION['rol'] ?? 'Desconocido';

// Obtener proyectos públicos
$stmt = $pdo->query("SELECT * FROM proyectos WHERE publico = 1 ORDER BY fecha_creacion DESC");
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed de Proyectos - Vitrina de Talento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003087;
            --accent-color: #FFD700;
            --bg-color: #F4F6F9;
            --card-bg: #FFFFFF;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #004080;
            color: white;
            padding-top: 20px;
            transition: 0.3s;
            z-index: 1050;
        }
        .sidebar.active {
            left: 0;
        }
        .sidebar .nav-link {
            color: white;
            padding: 10px 20px;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .close-btn {
            text-align: right;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 20px;
        }
        .content {
            margin-left: 0;
            transition: margin-left 0.3s;
            padding: 20px;
        }
        .content.shifted {
            margin-left: 250px;
        }
        .navbar {
            background-color: var(--card-bg);
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .navbar .search-bar {
            max-width: 300px;
            margin-left: 2rem;
        }
        .navbar-custom {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-center {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
        }
        .navbar-center .search-bar {
            max-width: 300px;
            flex-shrink: 1;
        }
        @media (max-width: 768px) {
            .navbar-center {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 540px;
            margin-left: auto;
            margin-right: auto;
        }
        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            height: 180px;
            object-fit: cover;
        }
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-light navbar-custom px-3">
    <div class="navbar-left">
        <button class="btn btn-outline-primary" id="menuToggleBtn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div class="navbar-center">
        <span class="fw-semibold">
            <?php if (isset($_SESSION['user_id'])): ?>
                Bienvenido, <?= htmlspecialchars($usuarioNombre) ?> | <?= htmlspecialchars(ucfirst($usuarioRol)) ?>
            <?php else: ?>
                Bienvenido, Invitado
            <?php endif; ?>
        </span>
        <form class="d-flex search-bar" role="search">
            <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Buscar">
            <button class="btn btn-outline-success" type="submit">Buscar</button>
        </form>
    </div>
    <div class="navbar-end">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm ms-3">Cerrar sesión</a>
        <?php else: ?>
            <a href="../html/index.html" class="btn btn-outline-primary btn-sm ms-3">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</nav>
<div class="sidebar" id="sidebar">
    <div class="close-btn" id="closeSidebar"><i class="fas fa-times"></i></div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link" href="#">Vitrina de Talento</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="certificadosLink">Certificados</a>
        </li>
        <?php if (in_array($_SESSION['rol'] ?? '', ['tutor', 'estudiante'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="#" id="tccLink">TCC</a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <?php if ($_SESSION['rol'] === 'tutor'): ?>
                <a class="nav-link" href="../api/proyectos_tutor.php">Proyectos de Investigación</a>
            <?php elseif ($_SESSION['rol'] === 'estudiante'): ?>
                <a class="nav-link" href="../api/proyectos_estudiante.php">Proyectos de Investigación</a>
            <?php else: ?>
                <a class="nav-link disabled" href="#">Proyectos de Investigación</a>
            <?php endif; ?>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="btnPerfil">Mi Perfil</a>
        </li>
        <li class="nav-item">
    <a class="nav-link" href="../html/registrar_habilidades.php">Registrar Habilidades</a>
</li>

    </ul>
    
</div>
<div class="content" id="mainContent">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php foreach ($proyectos as $proyecto): ?>
                    <div class="card">
                        <img src="<?= $proyecto['imagen_principal_id'] ? '../uploads/proyectos/' . $proyecto['imagen_principal_id'] : 'https://via.placeholder.com/600x200' ?>"
                                class="card-img-top" alt="Imagen Proyecto">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($proyecto['titulo']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($proyecto['descripcion_corta']) ?></p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalProyecto<?= $proyecto['id'] ?>">
                                Ver más
                            </button>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa fa-thumbs-up"></i> Me gusta</button>
                            <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-comment"></i> Comentar</button>
                        </div>
                    </div>
                    <div class="modal fade" id="modalProyecto<?= $proyecto['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title"><?= htmlspecialchars($proyecto['titulo']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                                    <p><strong>Tipo:</strong> <?= ucfirst($proyecto['tipo']) ?></p>
                                    <p><strong>Estado:</strong> <?= ucfirst($proyecto['estado']) ?></p>
                                    <p><strong>Presupuesto:</strong> $<?= number_format($proyecto['presupuesto'], 2) ?></p>
                                    <p><strong>Fechas:</strong> <?= $proyecto['fecha_inicio'] ?> a <?= $proyecto['fecha_fin'] ?></p>
                                    <?php if (!empty($proyecto['repositorio_url'])): ?>
                                        <p><strong>Repositorio:</strong> <a href="<?= $proyecto['repositorio_url'] ?>" target="_blank">Ver en GitHub</a></p>
                                    <?php endif; ?>
                                    <?php if (!empty($proyecto['demo_url'])): ?>
                                        <p><strong>Demo:</strong> <a href="<?= $proyecto['demo_url'] ?>" target="_blank">Ver demo</a></p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="mailto:contacto@proyecto.com" class="btn btn-success">Contactar Integrantes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- Modal Mi Perfil -->
<div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="perfilLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="perfilLabel">Mi Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
          <dt class="col-sm-4">Nombre completo</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($_SESSION['nombre'] ?? '---') ?></dd>

          <dt class="col-sm-4">Rol</dt>
          <dd class="col-sm-8"><?= ucfirst(htmlspecialchars($_SESSION['rol'] ?? '---')) ?></dd>

          <dt class="col-sm-4">Correo electrónico</dt>
          <dd class="col-sm-8">
            <?php
              // Traer el correo desde la base de datos
              $stmtEmail = $pdo->prepare("SELECT email FROM usuarios WHERE id = ?");
              $stmtEmail->execute([$_SESSION['user_id']]);
              $correo = $stmtEmail->fetchColumn();
              echo htmlspecialchars($correo ?: '---');
            ?>
          </dd>

          <dt class="col-sm-4">Código Estudiantil</dt>
          <dd class="col-sm-8">
            <?php
              // Si es estudiante, mostrar código
              if ($_SESSION['rol'] === 'estudiante') {
                $stmtCodigo = $pdo->prepare("SELECT codigo_estudiantil FROM estudiantes_certificados WHERE usuario_id = ?");
                $stmtCodigo->execute([$_SESSION['user_id']]);
                echo htmlspecialchars($stmtCodigo->fetchColumn() ?: '---');
              } else {
                echo '---';
              }
            ?>
          </dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('mainContent');

    document.getElementById('menuToggleBtn').addEventListener('click', () => {
        sidebar.classList.add('active');
        content.classList.add('shifted');
    });

    document.getElementById('closeSidebar').addEventListener('click', () => {
        sidebar.classList.remove('active');
        content.classList.remove('shifted');
    });

    document.getElementById('certificadosLink').addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = "<?= htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8') ?>";
    });

    const tccLink = document.getElementById('tccLink');
    if (tccLink) {
        tccLink.addEventListener('click', (e) => {
            e.preventDefault();
            const rol = "<?= $_SESSION['rol'] ?? '' ?>";
            if (rol === "tutor") {
                window.location.href = "tcc_tutor.php";
            } else if (rol === "estudiante") {
                window.location.href = "tcc_estudiante.php";
            } else {
                alert("No tienes acceso a esta sección.");
            }
        });
    }
</script>
<script>
document.getElementById('btnPerfil').addEventListener('click', function (e) {
    e.preventDefault();
    const modalPerfil = new bootstrap.Modal(document.getElementById('modalPerfil'));
    modalPerfil.show();
});
</script>


</body>
</html>

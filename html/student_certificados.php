<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'estudiante') {
    header('Location: index.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Buscar datos del estudiante con su certificado
$stmt = $pdo->prepare("SELECT nombres, apellidos, email, codigo_estudiantil, certificado_nombre, fecha_generacion_certificado 
                        FROM estudiantes_certificados 
                        WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$estudiante = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Certificados - Vitrina de Talento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-image: url('../img/unicartagena.jpg');
        font-family: 'Arial', sans-serif;
    }
    .container {
        margin-top: 50px;
        max-width: 700px;
    }
    .card {
    border-radius: 12px;
    /* offset-x | offset-y | blur-radius | spread-radius | color */
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.8); /* Sombra más difuminada y prominente */
    background-color: #f8f9fa; /* Fondo claro para contraste */
    }

    .btn {
        min-width: 130px;
    }
    .btn.btn-outline-secondary {
        color: rgb(239, 195, 20); /* Color del texto por defecto (tu púrpura) */
        border-color:rgb(239, 195, 20); /* Color del borde por defecto (tu púrpura) */
        background-color: transparent; /* Fondo transparente por defecto */
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; /* Transición suave */
    }

    /* Estilos para el hover del botón btn-outline-secondary */
    .btn.btn-outline-secondary:hover {
        background-color: rgb(239, 195, 20); /* Fondo púrpura al pasar el ratón */
        color: #ffffff; /* Texto blanco al pasar el ratón */
        border-color: rgb(239, 195, 20); /* Borde púrpura al pasar el ratón (se funde con el fondo) */
        cursor: pointer; /* Cambiar el cursor a una mano */
    }
    </style>
</head>
<body>
    <br><br><br>
    <div class="container">
    <div class="card p-4">
        <h3 class="text-center mb-4">Mi Certificado</h3>

        <p><strong>Nombre:</strong> <?= htmlspecialchars($estudiante['nombres'] ?? '') . ' ' . htmlspecialchars($estudiante['apellidos'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($estudiante['email'] ?? '') ?></p>
        <p><strong>Código Estudiantil:</strong> <?= htmlspecialchars($estudiante['codigo_estudiantil'] ?? '') ?></p>

        <?php if (!empty($estudiante['certificado_nombre'])): ?>
        <div class="text-center mt-4">
            <p><strong>Certificado:</strong> <?= htmlspecialchars($estudiante['certificado_nombre']) ?></p>
            <?php if (!empty($estudiante['fecha_generacion_certificado'])): ?>
            <p><strong>Fecha de emisión:</strong> <?= date('d/m/Y H:i', strtotime($estudiante['fecha_generacion_certificado'])) ?></p>
            <?php endif; ?>
            <a href="../api/ver_certificado.php?id=<?= $user_id ?>" class="btn btn-info me-2" target="_blank">Ver</a>
            <a href="../api/descargar_certificado.php?id=<?= $user_id ?>" class="btn btn-success">Descargar</a>
        </div>
        <?php else: ?>
        <div class="alert alert-warning text-center mt-4">
            Aún no tienes un certificado disponible.
        </div>
        <?php endif; ?>

        <div class="text-center mt-4">
        <a href="feed.php" class="btn btn-outline-secondary">Volver al inicio</a>
        </div>
    </div>
    </div>
</body>
</html>

<?php
require_once '../api/db_connect.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../img/unicartagena.jpg');
            font-family: 'Arial', sans-serif;
        }
        /* ... Tus estilos existentes para .form-container ... */
    .form-container {
        max-width: 900px; /* Aumentado de 700px a 900px para permitir 3 columnas cómodamente */
        /* Si tu diseño de página lo permite, podrías incluso ir a 1000px o más,
           o usar un porcentaje como max-width: 90%; si está dentro de un contenedor más grande */
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    /* ==================================== */
    /* Estilos para los elementos dentro del formulario de registro */
    /* ==================================== */

    .form-container h3, .form-container h5 {
        color: #333;
        margin-bottom: 1.5rem;
    }

    .form-container label {
        font-weight: 500;
        color: #333;
        margin-bottom: .25rem; /* Compacto */
        display: block;
        font-size: 0.88rem; /* Ligeramente más pequeña para ganar espacio horizontal */
    }

    .form-container .form-control,
    .form-container .form-select {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 0.5rem 0.75rem; /* Padding más compacto */
        font-size: 0.9rem; /* Fuente más compacta */
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-container .form-control:focus,
    .form-container .form-select:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
    }

    /* Ajustar el margen inferior de las filas y columnas para compactar aún más */
    .form-container .row.mb-2 {
        margin-bottom: 0.75rem !important; /* Más compacto entre filas */
    }

    .form-container .col-md-4.mb-2,
    .form-container .col-md-6.mb-2, /* En caso de que uses col-md-6 en alguna parte */
    .form-container .col-12.mb-2 {
        margin-bottom: 0.75rem !important; /* Más compacto entre campos dentro de la misma fila */
    }
    
    /* Para el mb-4 del final antes del botón */
    .form-container .mb-4 {
        margin-bottom: 1.5rem !important; /* Mantiene un buen espacio antes del botón */
    }


    .form-container hr {
        border-top: 1px solid #e0e0e0;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
    }

    /* Estilos para el botón de Registrarse (mantener w-100 para que ocupe todo el ancho del contenedor) */
    .form-container .btn-primary {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: #ffffff;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 5px;
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        margin-top: 1.5rem;
    }

    .form-container .btn-primary:hover {
        background-color: #5a369e;
        border-color: #5a369e;
        box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.35);
    }
    </style>
</head>
<body>
    <br>
<div class="form-container">
    <h3 class="mb-4 text-center">Registro de Usuario</h3>
    <form action="../api/procesar_registro.php" method="POST" enctype="multipart/form-data" id="registroForm">

        <div class="row mb-2">
            <div class="col-md-4 mb-2">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="col-md-4 mb-2">
                <label for="apellidos">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control" required>
            </div>
            <div class="col-md-4 mb-2">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-4 mb-2">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="col-md-4 mb-2">
                <label for="confirmar_password">Confirmar Contraseña</label>
                <input type="password" name="confirmar_password" id="confirmar_password" class="form-control" required>
            </div>
            <div class="col-md-4 mb-2">
                <label for="tipo_documento">Tipo de documento</label>
                <select name="tipo_documento" id="tipo_documento" class="form-select">
                    <option value="CC">CC</option>
                    <option value="TI">TI</option>
                    <option value="CE">CE</option>
                    <option value="PP">PP</option>
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-4 mb-2">
                <label for="documento">Número de documento</label>
                <input type="text" name="documento" id="documento" class="form-control" required>
            </div>
            <div class="col-md-4 mb-2">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
            </div>
        </div>

        <div class="row mb-2 justify-content-center"> <div class="col-md-4 mb-2"> <label for="genero">Género</label>
                <select name="genero" id="genero" class="form-select">
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="O">Otro</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="rol">Rol</label>
                <select name="rol" id="rol" class="form-select" required>
                    <option value="">Seleccione un rol</option>
                    <option value="estudiante">Estudiante</option>
                    <option value="tutor">Tutor</option>
                    <option value="visitor">Visitante / Empresa</option>
                </select>
            </div>
        </div>


        <div id="datosAcademicos" style="display: none;">
            <hr class="my-3">
            <h5 class="text-center mb-3">Datos Académicos</h5>
            <div class="row mb-2">
                <div class="col-md-4 mb-2">
                    <label for="centro_tutorial_id">Centro tutorial</label>
                    <select name="centro_tutorial_id" id="centro_tutorial_id" class="form-select">
                        <option value="">Selecciona un centro</option>
                        <?php
                        $stmt = $pdo->query("SELECT id, nombre FROM centros_tutoriales");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 mb-2" id="programaField">
                    <label for="programa_academico">Programa académico</label>
                    <select name="programa_id" class="form-select">
    <option value="">Seleccione un programa</option>
    <?php
    $stmt = $pdo->query("SELECT id, nombre FROM programas_academicos");
    while ($row = $stmt->fetch()) {
        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
    }
    ?>
</select>

                </div>
                <div class="col-md-4 mb-2" id="semestreField">
                    <label for="semestre">Semestre</label>
                    <select name="semestre" class="form-select">
    <option value="">Seleccione semestre</option>
    <?php for ($i = 1; $i <= 10; $i++) echo "<option value='$i'>$i</option>"; ?>

</select>

                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12"> <label for="foto_perfil">Foto de perfil (opcional)</label>
                <input type="file" name="foto_perfil" id="foto_perfil" class="form-control" accept="image/*">
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
    </form>
</div>
<script>
    const rolSelect = document.getElementById('rol');
    const datosAcademicos = document.getElementById('datosAcademicos');
    const programaField = document.getElementById('programaField');
    const semestreField = document.getElementById('semestreField');

    rolSelect.addEventListener('change', () => {
        const rol = rolSelect.value;
        if (rol === 'estudiante') {
            datosAcademicos.style.display = 'block';
            programaField.style.display = 'block';
            semestreField.style.display = 'block';
        } else if (rol === 'tutor') {
            datosAcademicos.style.display = 'block';
            programaField.style.display = 'none';
            semestreField.style.display = 'none';
        } else {
            datosAcademicos.style.display = 'none';
        }
    });
</script>
</body>
</html>

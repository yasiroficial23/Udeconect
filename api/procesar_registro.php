<?php
require_once '../api/db_connect.php';

function limpiarTexto($texto) {
    return htmlspecialchars(trim($texto));
}

$nombre = limpiarTexto($_POST['nombre'] ?? '');
$apellidos = limpiarTexto($_POST['apellidos'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$confirmar = $_POST['confirmar_password'] ?? '';
$tipo_documento = limpiarTexto($_POST['tipo_documento'] ?? '');
$documento = limpiarTexto($_POST['documento'] ?? '');
$telefono = limpiarTexto($_POST['telefono'] ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
$genero = limpiarTexto($_POST['genero'] ?? '');
$rol = limpiarTexto($_POST['rol'] ?? '');
// Corregido: solo guardar si aplica según el rol
$centro_tutorial_id = !empty($_POST['centro_tutorial_id']) ? $_POST['centro_tutorial_id'] : null;
$programa_id = ($rol === 'estudiante' && !empty($_POST['programa_academico_id'])) ? $_POST['programa_academico_id'] : null;
$semestre = ($rol === 'estudiante' && !empty($_POST['semestre'])) ? $_POST['semestre'] : null;


// Validaciones básicas
if (!$nombre || !$apellidos || !$email || !$password || $password !== $confirmar) {
    die("Error: Datos incompletos o contraseñas no coinciden.");
}

// Verificar si el usuario ya existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("Error: El correo ya está registrado.");
}

// Manejo de imagen de perfil
$foto_perfil_nombre = null;
if (!empty($_FILES['foto_perfil']['name'])) {
    $archivo = $_FILES['foto_perfil'];
    $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $permitidas = ['jpg', 'jpeg', 'png'];

    if (in_array(strtolower($ext), $permitidas)) {
        $foto_perfil_nombre = 'perfil_' . time() . '.' . $ext;
        $ruta_destino = '../img/perfiles/' . $foto_perfil_nombre;
        move_uploaded_file($archivo['tmp_name'], $ruta_destino);
    } else {
        die("Error: Formato de imagen no permitido.");
    }
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$sql = "INSERT INTO usuarios (nombre, apellidos, email, password_hash, tipo_documento, documento, telefono, fecha_nacimiento, genero, rol, centro_tutorial_id, programa_academico_id, semestre, foto_perfil_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
$stmt = $pdo->prepare($sql);
$exito = $stmt->execute([
    $nombre,
    $apellidos,
    $email,
    $password_hash,
    $tipo_documento,
    $documento,
    $telefono,
    $fecha_nacimiento,
    $genero,
    $rol,
    $centro_tutorial_id,
    $rol === 'estudiante' ? $programa_academico_id : null,
    $rol === 'estudiante' ? $semestre : null,
    $foto_perfil_nombre
]);

if ($exito) {
    echo "<script>
        alert('Registro exitoso. Serás redirigido al inicio de sesión.');
        window.location.href = '../html/index.html';
    </script>";
} else {
    echo "<script>
        alert('Error al registrar usuario.');
        window.history.back();
    </script>";
}

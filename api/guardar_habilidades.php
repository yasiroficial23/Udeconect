<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('No has iniciado sesión.'); window.location.href = '../html/index.html';</script>";
    exit;
}

$usuario_id = $_SESSION['user_id'];

// Verifica si llegaron habilidades desde el formulario
if (!isset($_POST['habilidades']) || !is_array($_POST['habilidades'])) {
    echo "<script>alert('No se enviaron habilidades.'); window.location.href = '../html/registrar_habilidades.php';</script>";
    exit;
}

$habilidades = $_POST['habilidades'];
$niveles = $_POST['niveles'];

try {
    $pdo->beginTransaction();

    // Recorre todas las habilidades enviadas
    foreach ($habilidades as $i => $habilidad_id) {
        $nivel = $niveles[$i] ?? 'basico';

        // Validación básica
        $habilidad_id = intval($habilidad_id);
        $nivel = in_array($nivel, ['basico', 'intermedio', 'avanzado', 'experto']) ? $nivel : 'basico';

        // Verificar si ya existe la habilidad para el usuario
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuario_habilidades WHERE usuario_id = ? AND habilidad_id = ?");
        $stmtCheck->execute([$usuario_id, $habilidad_id]);
        $existe = $stmtCheck->fetchColumn();

        if ($existe == 0) {
            // Insertar habilidad
            $stmtInsert = $pdo->prepare("INSERT INTO usuario_habilidades (usuario_id, habilidad_id, nivel) VALUES (?, ?, ?)");
            $stmtInsert->execute([$usuario_id, $habilidad_id, $nivel]);
        }
    }

    $pdo->commit();
    echo "<script>alert('Habilidades guardadas correctamente.'); window.location.href = '../html/feed.php';</script>";
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<script>alert('Error al guardar habilidades: " . $e->getMessage() . "'); window.history.back();</script>";
    exit;
}

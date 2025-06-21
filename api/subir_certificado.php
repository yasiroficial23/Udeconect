<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_FILES['certificado']) || !isset($_POST['estudiante_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

$archivo = $_FILES['certificado'];
$estudiante_id = $_POST['estudiante_id'];

if ($archivo['type'] !== 'application/pdf') {
    echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PDF.']);
    exit;
}

// Leer contenido del archivo
$contenido_binario = file_get_contents($archivo['tmp_name']);
$nombre_archivo = $archivo['name'];
$tipo_archivo = $archivo['type'];
$tamano_archivo = $archivo['size'];
$fecha_actual = date("Y-m-d H:i:s");

try {
    $stmt = $pdo->prepare("UPDATE estudiantes_certificados 
        SET certificado_generado = ?, certificado_nombre = ?, certificado_tipo = ?, certificado_tamaño = ?, fecha_generacion_certificado = ? 
        WHERE id = ?");
    
    $stmt->execute([
        $contenido_binario,
        $nombre_archivo,
        $tipo_archivo,
        $tamano_archivo,
        $fecha_actual,
        $estudiante_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Certificado guardado exitosamente en la base de datos.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en BD: ' . $e->getMessage()]);
}


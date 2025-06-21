<?php
require_once '../api/db_connect.php';

// Validar el ID recibido
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    exit('ID no proporcionado o inválido.');
}

// Consultar el certificado por usuario_id
$stmt = $pdo->prepare("SELECT certificado_generado, certificado_nombre, certificado_tipo 
                        FROM estudiantes_certificados 
                        WHERE usuario_id = ?");
$stmt->execute([$id]);
$cert = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar si se encontró y tiene contenido
if (!$cert || empty($cert['certificado_generado'])) {
    exit('Certificado no encontrado o vacío.');
}

// Mostrar el PDF embebido en el navegador
header("Content-Type: " . $cert['certificado_tipo']);
header("Content-Disposition: inline; filename=\"" . basename($cert['certificado_nombre']) . "\"");
header("Content-Length: " . strlen($cert['certificado_generado']));

// Mostrar contenido
echo $cert['certificado_generado'];
exit;
?>


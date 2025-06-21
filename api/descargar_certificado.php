<?php
require_once '../api/db_connect.php';

// Validar si se proporcionó el ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    exit('ID no proporcionado o inválido.');
}

// Buscar el certificado según usuario_id
$stmt = $pdo->prepare("SELECT certificado_generado, certificado_nombre, certificado_tipo 
                        FROM estudiantes_certificados 
                        WHERE usuario_id = ?");
$stmt->execute([$id]);
$cert = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar existencia y contenido
if (!$cert || empty($cert['certificado_generado'])) {
    exit('Certificado no encontrado o vacío.');
}

// Establecer headers para forzar descarga del archivo
header("Content-Type: " . $cert['certificado_tipo']);
header("Content-Disposition: attachment; filename=\"" . basename($cert['certificado_nombre']) . "\"");
header("Content-Length: " . strlen($cert['certificado_generado']));

// Entregar el archivo al navegador
echo $cert['certificado_generado'];
exit;
?>


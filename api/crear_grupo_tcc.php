<?php
require_once '../api/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'tutor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

$tutor_id = $_SESSION['user_id'];

$response = ['success' => false, 'message' => 'Error desconocido.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $titulo = $_POST['titulo_tcc'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $estado = 'activo';
    $integrantes = $_POST['integrantes'] ?? [];

    if (empty($nombre) || empty($titulo) || empty($descripcion) || empty($integrantes)) {
        $response['message'] = 'Todos los campos son obligatorios.';
    } else {
        try {
            $pdo->beginTransaction();

            $integrantesProcesados = [];
            $lider_id = null;

            foreach ($integrantes as $miembro) {
                $email = trim($miembro['email']);
                $rol = $miembro['rol'];

                // Buscar usuario por email
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND rol = 'estudiante'");
                $stmt->execute([$email]);
                $usuario_id = $stmt->fetchColumn();

                if (!$usuario_id) {
                    throw new Exception("El estudiante con correo '$email' no fue encontrado o no es un estudiante.");
                }

                if ($rol === 'lider') {
                    if ($lider_id) {
                        throw new Exception("Solo puede haber un líder por grupo.");
                    }
                    $lider_id = $usuario_id;
                }

                $integrantesProcesados[] = [
                    'usuario_id' => $usuario_id,
                    'rol' => $rol
                ];
            }

            if (!$lider_id) {
                throw new Exception("Debes asignar un estudiante como líder.");
            }

            // Insertar grupo con lider_id
            $stmtGrupo = $pdo->prepare("INSERT INTO grupos_tcc (nombre, titulo_tcc, descripcion, estado_tcc, tutor_id, lider_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtGrupo->execute([$nombre, $titulo, $descripcion, $estado, $tutor_id, $lider_id]);
            $grupo_id = $pdo->lastInsertId();

            // Insertar miembros
            $stmtMiembro = $pdo->prepare("INSERT INTO grupos_tcc_miembros (grupo_id, usuario_id, rol) VALUES (?, ?, ?)");
            foreach ($integrantesProcesados as $m) {
                $stmtMiembro->execute([$grupo_id, $m['usuario_id'], $m['rol']]);
            }

            $pdo->commit();
            $response['success'] = true;
            $response['message'] = 'Grupo creado exitosamente.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $response['message'] = 'Error al crear grupo: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Solicitud inválida.';
}

header('Content-Type: application/json');
echo json_encode($response);

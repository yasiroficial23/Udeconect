<?php
header('Content-Type: application/json');
require_once '../api/db_connect.php';

$response = ['success' => false, 'message' => 'Credenciales inválidas'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if ($email && $password) {
        // ✅ También traemos el nombre desde la base de datos
        $stmt = $pdo->prepare("SELECT id, nombre, password_hash, rol FROM usuarios WHERE email = ? AND estado = 'activo'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['nombre'] = $user['nombre']; // ✅ Guardamos el nombre en sesión

            $response['success'] = true;
            $response['message'] = 'Inicio de sesión exitoso';

            // ✅ Sincronizar con estudiantes_certificados si es estudiante
            if ($user['rol'] === 'estudiante') {
                $sync = $pdo->prepare("UPDATE estudiantes_certificados 
                    SET usuario_id = ? 
                    WHERE email = ? AND (usuario_id IS NULL OR usuario_id = 0)");
                $sync->execute([$user['id'], $email]);
            }
        }
    }
}

echo json_encode($response);
?>

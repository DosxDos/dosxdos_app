<?php
require __DIR__ . '/clases/conexion_clase.php'; // Ajusta según tu conexión

// Leer los datos enviados desde JS (JSON)
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->token) && !isset($data->user_id)) {
    // Si no se recibió el token, retornamos error
    echo json_encode([
        'status'  => 'error',
        'message' => 'No se ha recibido el token del montador o el id del usuario'
    ]);
    exit;
}

$token = $data->token;

try {
    // Crear instancia de tu clase de conexión
    $db = new Conexion();
    
    // Revisar si hubo algún error al conectar
    if ($db->conexion->connect_errno) {
        throw new Exception('Error de conexión: ' . $db->conexion->connect_error);
    }

    // Preparamos la consulta con placeholders. 
    // ON DUPLICATE KEY UPDATE, pero usando la sintaxis para MySQL con placeholders.
    // (Si la columna 'token' es UNIQUE, esto actualizará si ya existe.)
    $sql = "INSERT INTO tokens VALUES (?,?) 
            ON DUPLICATE KEY UPDATE token = VALUES(token)";

    $stmt = $db->conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la sentencia: " . $db->conexion->error);
    }

    // Enlazamos el parámetro. 's' -> string
    $stmt->bind_param('si', $token, $id);

    // Ejecutamos
    $stmt->execute();

    // Verificamos si se insertó o actualizó algo
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'token'  => $token
        ]);
    } else {
        // Si affected_rows == 0, significa que no se cambió nada
        // (podría pasar si el token ya existía y no hubo cambios)
        echo json_encode([
            'status'  => 'warning',
            'message' => 'No row inserted or updated',
            'token'   => $token
        ]);
    }

    // Cerramos
    $stmt->close();
} catch (Exception $e) {
    // Cualquier error se captura aquí
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
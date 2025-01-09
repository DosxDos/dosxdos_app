<?php
require __DIR__ . '/clases/conexion_clase.php'; // Ajusta según tu conexión
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

// 1. Leer el JSON recibido en el cuerpo de la petición
$inputData = json_decode(file_get_contents('php://input'), true);

if (!isset($inputData['titulo'], $inputData['cuerpo'], $inputData['user_id'])) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Faltan campos (titulo, cuerpo, user_id).'
    ]);
    exit;
}

// Asignar variables desde el JSON
$titulo  = $inputData['titulo'];
$cuerpo  = $inputData['cuerpo'];
$user_id = $inputData['user_id'];

// 2. Conectar a la BD usando tu clase Conexion
try {
    $db = new Conexion();

    // Revisar si hubo error al conectar
    if ($db->conexion->connect_errno) {
        throw new Exception('Error de conexión: ' . $db->conexion->connect_error);
    }

    // Preparar la consulta con placeholders (?)
    $sql = "SELECT token FROM tokens WHERE user_id = ? LIMIT 1";
    $stmt = $db->conexion->prepare($sql);
    
    if (!$stmt) {
        // Error al preparar la sentencia
        throw new Exception("Error al preparar el statement: " . $db->conexion->error);
    }

    // Vincular el parámetro (en este caso, user_id como string o int, según tu esquema)
    // Si user_id es int en la BD, usa "i"
    // Si es string, usa "s"
    $stmt->bind_param("i", $user_id);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Error al obtener el resultado: " . $db->conexion->error);
    }

    // Verificar si se encontró algún token
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => 'No se encontró token para ese usuario.'
        ]);
        exit;
    }

    // Extraer el token
    $row = $result->fetch_assoc();
    $tokenDestino = $row['token'];

    // Cerrar statement
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}


// 4. Configurar Firebase con la cuenta de servicio
$serviceAccountPath = __DIR__ . '/credenciales.json'; // Ajusta la ruta a tu JSON de credenciales
$factory  = (new Factory)->withServiceAccount($serviceAccountPath);
$messaging = $factory->createMessaging();

// 5. Construir la notificación
$message = CloudMessage::new()
    ->withNotification(Notification::create($titulo, $cuerpo))
    ->withData([
        // Campos de datos adicionales si quieres
        'info_extra' => 'valor'
    ])
    ->withChangedTarget('token', $tokenDestino);

// 6. Enviar la notificación
try {
    $messaging->send($message);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Notificación enviada exitosamente'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error al enviar la notificación: '.$e->getMessage()
    ]);
}
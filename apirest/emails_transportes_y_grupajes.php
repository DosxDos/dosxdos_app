<?php

ignore_user_abort(true); // Continuar aunque el cliente se desconecte

require_once 'config.php';
require_once 'middlewares/jwtMiddleware.php';
require_once 'clases/respuestas_clase.php';

$logFile = 'logs/emails_transportes_y_grupajes.txt';
$logMessage = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Guardar el token de la solicitud
    if (isset($_REQUEST['tokenJwt'])) {
        $token = $_REQUEST['tokenJwt'];
    } else {
        $token = null;
    }
    // Respuesta inmediata debido a las limitantes de Notion de tiempos de respuesta en las automatizaciones (las pausa si supera tiempo límite establecido por ellos);
    $response = new Respuestas;
    $respuestaFinal = $response->ok("Solicitud al servidor iniciada");
    http_response_code(200);
    echo json_encode($respuestaFinal);
    // IMPORTANTE: Cerrar la conexión HTTP para liberar a Notion
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request(); // Para PHP-FPM
    } else {
        // Alternativa para otros SAPIs
        header('Connection: close');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
        flush();
    }
    // Inicio de procesos en el servidor
    try {
        // Guardar Log de inicio de la solicitud
        $fecha = date('Y-m-d H:i:s');
        $logMessage['timestamp'] = $fecha;
        $logMessage['mensaje'] = "Solicitud iniciada en el servidor";
        $logMessageFinal = json_encode($logMessage);
        file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
        // Guardar cuerpo en archivo de logs
        file_put_contents($logFile, file_get_contents('php://input') . PHP_EOL, FILE_APPEND | LOCK_EX);
        // Guardar Log de inicio de la autenticación de la solicitud
        $fecha = date('Y-m-d H:i:s');
        $logMessage['timestamp'] = $fecha;
        $logMessage['mensaje'] = "Se inicia la autenticación de la solicitud";
        $logMessageFinal = json_encode($logMessage);
        file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
        // Autenticación JWT
        require_once 'clases/JwtHandler.php';
        $jwt = new JwtHandler;
        $middleware = $jwt->validarToken($token);
        if (!$middleware) {
            // Guardar log de error de autenticación
            $logMessage['error'] = $jwt->error;
            $logMessageFinal = json_encode($logMessage);
            file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
            exit;
        }
        // Guardar log de autenticación
        $fecha = date('Y-m-d H:i:s');
        $logMessage['timestamp'] = $fecha;
        $logMessage['mensaje'] = "Solicitud autenticada exitosamente en el servidor";
        $logMessageFinal = json_encode($logMessage);
        file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
    } catch (\Throwable $th) {
        // Capturar todos los datos del error en un array asociativo
        $errorData = [
            'mensaje' => $th->getMessage(),
            'codigo' => $th->getCode(),
            'archivo' => $th->getFile(),
            'linea' => $th->getLine(),
            'traza_completa' => $th->getTraceAsString(),
            'traza_array' => $th->getTrace(),
            'clase_excepcion' => get_class($th),
            'excepcion_anterior' => null,
            'timestamp' => date('Y-m-d H:i:s'),
            'memoria_usada' => memory_get_usage(true),
            'memoria_pico' => memory_get_peak_usage(true)
        ];
        // Si hay una excepción anterior, capturarla también
        if ($th->getPrevious() !== null) {
            $previous = $th->getPrevious();
            $errorData['excepcion_anterior'] = [
                'mensaje' => $previous->getMessage(),
                'codigo' => $previous->getCode(),
                'archivo' => $previous->getFile(),
                'linea' => $previous->getLine(),
                'traza_completa' => $previous->getTraceAsString(),
                'clase_excepcion' => get_class($previous)
            ];
        }
        // Registrar el error en el archivo de logs
        $errorDataJson = json_encode($errorData);
        file_put_contents($logFile, $errorDataJson . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
} else {
    // Respuesta inmediata debido a las limitantes de Notion de tiempos de respuesta en las automatizaciones (las pausa si supera tiempo límite establecido por ellos);
    $response = new Respuestas;
    $respuestaFinal = $response->error_405("Método no permitido, solo se acepta POST");
    http_response_code(405);
    echo json_encode($respuestaFinal);
    // Inicio de procesos en el servidor
    // Guardar Log de inicio de la solicitud
    $fecha = date('Y-m-d H:i:s');
    $logMessage['timestamp'] = $fecha;
    $logMessage['mensaje'] = "Solicitud iniciada en el servidor";
    $logMessageFinal = json_encode($logMessage);
    file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
    // Registrar el error en el archivo de logs
    $fecha = date('Y-m-d H:i:s');
    $logMessage['timestamp'] = $fecha;
    $logMessage['error'] = "Método no permitido, solo se acepta POST";
    $logMessageFinal = json_encode($logMessage);
    file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
}

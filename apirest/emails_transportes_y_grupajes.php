<?php

ignore_user_abort(true); // Continuar aunque el cliente se desconecte (Notion se puede desconectar si la respuesta tarda más de sus milisegundos permitidos)

// Módulos y dependencias necesarios
require_once 'config.php';
require_once 'middlewares/jwtMiddleware.php';
require_once 'clases/respuestas_clase.php';
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Declaración de variables para el manejo de logs
$logFile = 'logs/emails_transportes_y_grupajes.txt';
$logMessage = [];

//Verificar método de la solicitud y actuar en consecuencia
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Guardar el token de la solicitud
    if (isset($_REQUEST['tokenJwt'])) {
        $token = $_REQUEST['tokenJwt'];
    } else {
        $token = null;
    }
    // Respuesta inmediata debido a las limitantes de Notion de tiempos de respuesta en las automatizaciones (las pausa si supera tiempo límite establecido por ellos)
    $response = new Respuestas;
    $respuestaFinal = $response->ok("Solicitud al servidor iniciada");
    http_response_code(200);
    echo json_encode($respuestaFinal);
    // IMPORTANTE: Cerrar la conexión HTTP para liberar a Notion, y evitar que Notion pause la automatización en el propio Notion por tiempos de espera
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request(); // Para PHP-FPM
    } else {
        // Alternativa para otros SAPIs
        header('Connection: close');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
        flush();
    }
    // Inicio de procesos en el servidor luego de cerrar la conexión HTTP con Notion
    try {
        // Guardar Log de inicio de la solicitud
        $fecha = date('Y-m-d H:i:s');
        $logMessage['timestamp'] = $fecha;
        $logMessage['mensaje'] = "Solicitud iniciada en el servidor";
        $logMessageFinal = json_encode($logMessage);
        file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
        // Guardar cuerpo en archivo de logs
        $cuerpoJson = file_get_contents('php://input');
        file_put_contents($logFile, $cuerpoJson . PHP_EOL, FILE_APPEND | LOCK_EX);
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
        // Obtener los datos del cuerpo de la solicitud
        $datos = json_decode($cuerpoJson, true);
        $nombrePlanificacion = $datos['data']['properties']['Nombre']['title'][0]['plain_text'] ?? 'Sin nombre';
        $departamentosRelacionados = $datos['data']['properties']['Departamentos relacionados']['multi_select'] ?? [];
        $departamentos = [];
        foreach ($departamentosRelacionados as $departamento) {
            $departamentos[] = $departamento['name'];
        }
        $idPlanificacion = $datos['data']['properties']['ID']['unique_id']['number'] ?? 'Sin ID';
        $dataPlanificacion = [
            'nombre' => $nombrePlanificacion,
            'departamentos' => $departamentos,
            'id' => $idPlanificacion
        ];
        // Guardar log de los datos de la planificación
        $fecha = date('Y-m-d H:i:s');
        $logMessage['timestamp'] = $fecha;
        $logMessage['mensaje'] = $dataPlanificacion;
        $logMessageFinal = json_encode($logMessage);
        file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
        // Cargamos las variables de entorno
        // Cargar el archivo .env desde la raíz del proyecto
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        // Acceder a las variables de entorno de la base de datos
        $dbHost = $_ENV['DB_HOST'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASSWORD'];
        // Acceder a las variables de entorno del correo electrónico
        $emailHost = $_ENV['EMAIL_HOST'];
        $emailUser = $_ENV['EMAIL_USER'];
        $emailPassword = $_ENV['EMAIL_PASSWORD'];
        $emailPort = $_ENV['EMAIL_PORT'];
        // Conectamos con la base de datos con PDO
        $pdo = new PDO(
            "mysql:host={$dbHost};dbname={$dbName}",
            $dbUser,
            $dbPass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        // Recorremos los departamentos, hacemos la consulta de los correos asociados al departamento en la base de datos en la tabla de usuarios y la tabla de departamentos, y enviamos el correo a cada uno de los integrantes del departamento
        foreach ($departamentos as $departamento) {
            $correos = getCorreosByDepartamento($pdo, $departamento);
            if (empty($correos)) {
                // Guardar log de error si no hay correos associados al departamento
                $fecha = date('Y-m-d H:i:s');
                $logMessage['timestamp'] = $fecha;
                $logMessage['error'] = "No se encontraron correos para el departamento: {$departamento}";
                $logMessageFinal = json_encode($logMessage);
                file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
                continue; // Saltar al siguiente departamento
            }
            // Preparar los datos del correo
            $emailData = [
                'from_email' => $emailUser,
                'subject' => "Nueva planificación confirmada de transportes y grupajes en Notion: {$nombrePlanificacion}",
                'body' => "Se ha confirmado la planificación de transportes y grupajes en Notion, con ID: {$idPlanificacion} y nombre: {$nombrePlanificacion}.",
                'to' => []
            ];
            foreach ($correos as $correo) {
                $emailData['to'][$correo['correo']] = ''; // Agregar destinatarios
            }
            // Enviar el correo
            if (sendEmail([
                'host' => $emailHost,
                'username' => $emailUser,
                'password' => $emailPassword,
                'port' => $emailPort
            ], $emailData)) {
                // Guardar log de éxito del envío del correo
                $fecha = date('Y-m-d H:i:s');
                $logMessage['timestamp'] = $fecha;
                $logMessage['mensaje'] = "Correo enviado exitosamente a los integrantes del departamento: {$departamento}";
                $logMessageFinal = json_encode($logMessage);
                file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
            } else {
                // Guardar log de error en el envío del correo
                $fecha = date('Y-m-d H:i:s');
                $logMessage['timestamp'] = $fecha;
                $logMessage['error'] = "Error al enviar el correo a los integrantes del departamento: {$departamento}";
                $logMessageFinal = json_encode($logMessage);
                file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
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

// Funciones reutilizables
function getCorreosByDepartamento($pdo, $nombreDepartamento)
{
    try {
        $sql = "SELECT u.correo 
            FROM usuarios u 
            INNER JOIN departamentos d ON u.departamento = d.id 
            WHERE d.name = :department_name AND u.eliminado = 0";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':department_name' => $nombreDepartamento]);

        return $stmt->fetchAll();
    } catch (\Throwable $th) {
        // Registrar el error en el archivo de logs
        $logFile = 'logs/emails_transportes_y_grupajes.txt';
        $logMessage = [];
        $fecha = date('Y-m-d H:i:s');
        $logMessage['timestamp'] = $fecha;
        $logMessage['error'] = "Error al obtener correos del departamento: " . $th->getMessage();
        $logMessageFinal = json_encode($logMessage);
        file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
        return []; // Retornar un array vacío en caso de error
    }
}

function sendEmail($smtpConfig, $emailData, $attachments = [])
{
    try {
        $mail = new PHPMailer(true);
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = $smtpConfig['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtpConfig['username'];
        $mail->Password = $smtpConfig['password'];
        $mail->SMTPSecure = $smtpConfig['encryption'] ?? 'tls';
        $mail->Port = $smtpConfig['port'];
        $mail->CharSet = 'UTF-8';
        // Configuración del email
        $mail->setFrom($emailData['from_email'], $emailData['from_name'] ?? '');
        // Destinatarios
        if (is_array($emailData['to'])) {
            foreach ($emailData['to'] as $email => $name) {
                if (is_numeric($email)) {
                    $mail->addAddress($name); // Solo email
                } else {
                    $mail->addAddress($email, $name); // Email y nombre
                }
            }
        } else {
            $mail->addAddress($emailData['to'], $emailData['to_name'] ?? '');
        }
        // CC y BCC (opcional)
        if (isset($emailData['cc'])) {
            if (is_array($emailData['cc'])) {
                foreach ($emailData['cc'] as $cc) {
                    $mail->addCC($cc);
                }
            } else {
                $mail->addCC($emailData['cc']);
            }
        }
        if (isset($emailData['bcc'])) {
            if (is_array($emailData['bcc'])) {
                foreach ($emailData['bcc'] as $bcc) {
                    $mail->addBCC($bcc);
                }
            } else {
                $mail->addBCC($emailData['bcc']);
            }
        }
        // Contenido del email
        $mail->isHTML($emailData['is_html'] ?? false);
        $mail->Subject = $emailData['subject'];
        $mail->Body = $emailData['body'];
        // Texto alternativo (opcional)
        if (isset($emailData['alt_body'])) {
            $mail->AltBody = $emailData['alt_body'];
        }
        // Archivos adjuntos
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if (is_string($attachment)) {
                    // Solo ruta del archivo
                    $mail->addAttachment($attachment);
                } elseif (is_array($attachment)) {
                    // Array con ruta y nombre personalizado
                    $mail->addAttachment(
                        $attachment['path'],
                        $attachment['name'] ?? '',
                        $attachment['encoding'] ?? 'base64',
                        $attachment['type'] ?? ''
                    );
                }
            }
        }
        // Enviar el correo
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        // Registrar el error en el archivo de logs
        $logFile = 'logs/emails_transportes_y_grupajes.txt';
        $logMessage = [];
        $fecha = date('Y-m-d H:i:s');
        $logMessage['timestamp'] = $fecha;
        $logMessage['error'] = "Error al enviar el correo: " . $e->getMessage();
        $logMessageFinal = json_encode($logMessage);
        file_put_contents($logFile, $logMessageFinal . PHP_EOL, FILE_APPEND | LOCK_EX);
        return false;
    }
}

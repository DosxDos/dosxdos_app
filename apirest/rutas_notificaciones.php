<?php
require_once __DIR__ . '/controladores/notificaciones_controlador.php';
require_once __DIR__ . '/clases/respuestas_clase.php';

//Aquí instanciamos la clase Notificaciones con todos los métodos que hemos creado
$notificacionesControlador = new NotificacionesControlador();

//Aquí instanciamos la clase Respuestas con todos los métodos que hemos creado
$respuestas = new Respuestasv2();

//Aquí obtenemos el método de la petición
$metodo = $_SERVER['REQUEST_METHOD'];

//Aquí obtenemos la ruta de la petición
$rutas = explode('/', trim($_SERVER['PATH_INFO'], '/'));

//convertir la ruta de un objeto a un string que concatene los objetos
$ruta = implode('/', $rutas);

//Aquí obtenemos los datos de la petición en formato JSON utf-8
$datos = json_decode(mb_convert_encoding(file_get_contents('php://input'), 'UTF-8', 'auto'), true);
switch ($metodo) {
    case 'GET':
        switch ($ruta) {
            case 'notificaciones':
                try {
                    $pageIndex = $_GET['pageIndex'] ?? 1;
                    $pageSize = $_GET['pageSize'] ?? 200;
                    $resultado = $notificacionesControlador->obtenerNotificaciones($pageIndex, $pageSize);
                    //Aquí instanciamos el objeto de la clase Respuestas
                    $respuesta = $respuestas->ok($resultado);
                    echo json_encode($respuesta);
                } catch (Throwable $e) {
                    $respuesta = $respuestas->error_500($e->getMessage());
                    echo json_encode($respuesta);
                }
                break;
            case (preg_match('/^notificaciones\/\d+$/', $ruta) ? true : false):
                try {
                    $pageIndex = $_GET['pageIndex'] ?? 1;
                    $pageSize = $_GET['pageSize'] ?? 200;
                    $id = $rutas[1];
                    if (isset($_GET['activa'])) {
                        $activa = $_GET['activa'];
                        if ($activa == 'true') {
                            $activa = 1;
                        } elseif ($activa == 'false') {
                            $activa = 0;
                        }
                        $resultado = $notificacionesControlador->obtenerNotificacionActivaPorId($pageIndex, $pageSize, $id, $activa);
                        $respuesta = $respuestas->ok($resultado);
                        echo json_encode($respuesta);
                        return;
                    }
                    $resultado = $notificacionesControlador->obtenerNotificacionPorId($pageIndex, $pageSize, $id);
                    $respuesta = $respuestas->ok($resultado);
                    echo json_encode($respuesta);
                } catch (Throwable $e) {
                    $respuesta = $respuestas->error_500($e->getMessage());
                    echo json_encode($respuesta);
                }
                break;
            default:
                $respuesta = $respuestas->error_400('Ruta no encontrada por el método GET');
                echo json_encode($respuesta);
                break;
        }
        break;
    case 'POST':
        switch ($ruta) {
            case 'notificaciones':
                try {
                    if (isset($datos['usuario_id']) && isset($datos['titulo']) && isset($datos['mensaje']) && isset($datos['tipo_usuario'])) {
                        $resultado = $notificacionesControlador->crearNotificacion($datos);
                        //Aquí instanciamos el objeto de la clase Respuestas
                        $respuesta = $respuestas->ok($resultado);
                        echo json_encode($respuesta);
                    } else {
                        $respuesta = $respuestas->error_400('Datos incompletos o incorrectos en la solicitud a la api de notificaciones');
                        echo json_encode($respuesta);
                    }
                } catch (Throwable $e) {
                    $respuesta = $respuestas->error_500($e->getMessage());
                    echo json_encode($respuesta);
                }
                break;
            case 'notificaciones/enviar':
                try {
                    if (isset($datos['usuario_id']) && isset($datos['titulo']) && isset($datos['mensaje']) && isset($datos['tipo_usuario'])) {
                        $resultado = $notificacionesControlador->enviarNotificacion($datos);
                        //Aquí instanciamos el objeto de la clase Respuestas
                        if($resultado['status'] == "error"){
                            $respuesta = $respuestas->error_400($resultado['message']);
                        }elseif($resultado['status'] == "success"){
                            $respuesta = $respuestas->ok($resultado['tokensNotificados']);
                        }
                        echo json_encode($respuesta);
                    } else {
                        $respuesta = $respuestas->error_400('Datos incompletos o incorrectos en la solicitud a la api de notificaciones');
                        echo json_encode($respuesta);
                    }
                } catch (Throwable $e) {
                    $respuesta = $respuestas->error_500($e->getMessage());
                    echo json_encode($respuesta);
                }
                break;
            default:
                $respuesta = $respuestas->error_400("Este endpoint no existe en la api");
                echo json_encode($respuesta);
                break;
        }
        break;
    case 'PUT':
        switch ($ruta) {
            case 'notificaciones':
                $id = $ruta[1];
                $notificacionesControlador->actualizarNotificacion($id, $datos);
                break;
            default:
                echo json_encode(['error' => 'Ruta no encontrada']);
                break;
        }
        break;
    case 'DELETE':
        switch ($ruta) {
            case 'notificaciones':
                $id = $ruta[1];
                $notificacionesControlador->eliminarNotificacion($id);
                break;
            default:
                echo json_encode(['error' => 'Ruta no encontrada']);
                break;
        }
        break;
    default:
        echo json_encode(['error' => 'Método no soportado']);
        break;
}

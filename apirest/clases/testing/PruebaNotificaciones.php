<?php
require_once __DIR__ . '/../notificaciones_clase.php';

//Así es como se deberían pasar las notificaciones
class PruebaNotificaciones {
    public function testSendNotification() {
        //recoger el cuerpo del json
        $cuerpo = file_get_contents('php://input');
        //decodificar el json
        $data = json_decode($cuerpo, true);
        if(!isset($data['titulo']) || !isset($data['cuerpo']) || !isset($data['user_id'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Faltan campos (titulo, cuerpo, user_id).'
            ]);
            exit;
        }
        $notificaciones = new Notificaciones();
        $titulo = $data['titulo'];
        $cuerpo = $data['cuerpo'];
        $user_id = $data['user_id']; // Cambia esto por un ID de usuario válido en tu base de datos

        //Esto es lo que mandaría las notificaciones
        $resultado = $notificaciones->sendNotification($titulo, $cuerpo, $user_id);
        echo json_encode($resultado);
    }
}

$prueba = new PruebaNotificaciones();
$prueba->testSendNotification();
?>
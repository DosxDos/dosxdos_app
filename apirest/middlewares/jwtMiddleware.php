<?php

require_once 'clases/JwtHandler.php';
require_once 'clases/respuestas_clase.php';

class JwtMiddleware
{
    public $jwt;
    public $respuesta;
    public array|null $payload = null;
    public array|null $error = null;
    public bool $autenticado = false;

    public function __construct()
    {
        try {
            $this->jwt = new JwtHandler;
            $this->respuesta = new Respuestas;
        } catch (\Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
            $respuesta = $this->respuesta->error_500($this->error);
            http_response_code(500);
            echo json_encode($respuesta);
            exit;
        }
    }

    public function verificar()
    {
        try {
            $token = null;

            // Leer token desde el cuerpo (JSON)
            $input = json_decode(file_get_contents("php://input"), true);
            if (is_array($input) && isset($input['tokenJwt'])) {
                $token = $input['tokenJwt'];
            }

            // O desde GET o POST
            if (!$token && isset($_REQUEST['tokenJwt'])) {
                $token = $_REQUEST['tokenJwt'];
            }

            if (!$token) {
                $this->error = [
                    'line' => "52",
                    'file' => "jwtMiddleware.php",
                    'message' => 'Token JWT no proporcionado'
                ];
                $respuesta = $this->respuesta->error_401($this->error);
                http_response_code(401);
                echo json_encode($respuesta);
                exit;
            }

            // Validar token
            if (!$this->jwt->validarToken($token)) {
                $this->error = $this->jwt->error;
                $respuesta = $this->respuesta->error_500($this->error);
                http_response_code(500);
                echo json_encode($respuesta);
                exit;
            }

            // Éxito
            $this->autenticado = true;
            $this->payload = $this->jwt->payload;
        } catch (Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
            $respuesta = $this->respuesta->error_500($this->error);
            http_response_code(500);
            echo json_encode($respuesta);
            exit;
        }
    }
}

/*
$jwtMiddleware = new JwtMiddleware;
$jwtMiddleware->verificar();
if ($jwtMiddleware->error) {
  var_dump($jwtMiddleware->error);
}
*/

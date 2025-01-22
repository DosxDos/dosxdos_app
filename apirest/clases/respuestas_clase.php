<?php

class Respuestas
{
    public  $response = [];

    public function ok($valor = '200 - Solicitud exitosa')
    {
        $this->response[0] = true;
        $this->response[1] = $valor;
        $this->response[2] = 200;
        return $this->response;
    }

    public function okF($valor = '200 - Solicitud exitosa')
    {
        $this->response[0] = false;
        $this->response[1] = $valor;
        $this->response[2] = 200;
        return $this->response;
    }

    public function error_400($valor = "400 - Datos incompletos o incorrectos en la solicitud a la api intermedia")
    {
        $this->response[0] = false;
        $this->response[1] = $valor;
        $this->response[2] = 400;
        return $this->response;
    }

    public function error_401($valor = "401 - No autorizado en la api intermedia")
    {
        $this->response[0] = false;
        $this->response[1] = $valor;
        $this->response[2] = 401;
        return $this->response;
    }

    public function error_405($valor = "405 - Método no permitido")
    {
        $this->response[0] = false;
        $this->response[1] = $valor;
        $this->response[2] = 405;
        return $this->response;
    }

    public function error_500($valor = "500 - Error interno del servidor")
    {
        $this->response[0] = false;
        $this->response[1] = $valor;
        $this->response[2] = 500;
        return $this->response;
    }
}

//método nuevo de respuesta
class Respuesta
{
    public $success;
    public $message;
    public $status;

    public function __construct($success, $message, $status)
    {
        $this->success = $success;
        $this->message = $message;
        $this->status = $status;
    }
}

class Respuestasv2
{
    private function setHeader($status)
    {
        header("HTTP/1.1 " . $status);
    }

    public function ok($message = '200 - Solicitud exitosa')
    {
        $this->setHeader(200);
        return new Respuesta(true, $message, 200);
    }

    public function error_400($message = "400 - Datos incompletos o incorrectos en la solicitud a la api intermedia")
    {
        $this->setHeader(400);
        return new Respuesta(false, $message, 400);
    }

    public function error_401($message = "401 - No autorizado en la api intermedia")
    {
        $this->setHeader(401);
        return new Respuesta(false, $message, 401);
    }

    public function error_405($message = "405 - Método no permitido")
    {
        $this->setHeader(405);
        return new Respuesta(false, $message, 405);
    }

    public function error_500($message = "500 - Error interno del servidor")
    {
        $this->setHeader(500);
        return new Respuesta(false, $message, 500);
    }
}

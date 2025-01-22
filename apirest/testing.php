<?php

require_once 'config.php';
require_once 'clases/lineas_clase.php';

$_respuestas = new Respuestas;

try {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $postBody = file_get_contents("php://input");
        $utf8PostBody = mb_convert_encoding($postBody, 'UTF-8', 'auto');
        if(file_put_contents("testing.json", $utf8PostBody)){
            $respuesta = $_respuestas->ok();
            $response = json_encode($respuesta);
            http_response_code(200);
            echo $response;
        } else {
            $respuesta = $_respuestas->error_500();
            $response = json_encode($respuesta);
            http_response_code(500);
            echo $response;
        }
    } else {
        $respuesta = $_respuestas->error_405();
        $response = json_encode($respuesta);
        http_response_code(405);
        echo $response;
    }
} catch (Exception $e) {
    $respuesta = $_respuestas->error_500();
    $response = json_encode($respuesta);
    http_response_code(500);
    echo $response;
}
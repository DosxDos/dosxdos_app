<?php

require_once 'config.php';
require_once 'clases/ot_clase.php';

$_respuestas = new Respuestas;
$_ot = new Ot;

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET["codOt"]) && !isset($_GET["nombreOt"])) {
        $codOt = $_GET['codOt'];
        $_ot->lineas($codOt);
        if ($_ot->error) {
            $cod = $_ot->error[2];
            http_response_code($cod);
            $response = json_encode($_ot->error);
            echo $response;
        } else {
            $cod = $_ot->respuesta[2];
            http_response_code($cod);
            $response = json_encode($_ot->respuesta);
            echo $response;
        }
    } else if (isset($_GET["codOt"]) && isset($_GET["nombreOt"])) {
        $codOt = $_GET['codOt'];
        $_ot->nombreOt($codOt);
        if ($_ot->error) {
            $cod = $_ot->error[2];
            http_response_code($cod);
            $response = json_encode($_ot->error);
            echo $response;
        } else {
            $cod = $_ot->respuesta[2];
            http_response_code($cod);
            $response = json_encode($_ot->respuesta);
            echo $response;
        }
    }
} else {
    $respuesta = $_respuestas->error_405();
    $response = json_encode($respuesta);
    http_response_code(405);
    echo $response;
}

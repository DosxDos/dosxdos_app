<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once 'middlewares/jwtMiddleware.php';
require_once 'clases/crm_clase.php';
require_once 'clases/a3Erp_clase.php';

if (!isset($_GET['idOt']) || !isset($_GET['codOt']) || !isset($_GET['tipoOt']) || !isset($_GET['cliente']) || !isset($_GET['tokenJwt'])) {
    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS</p>';
    scrollUpdate();
    @ob_flush();
    flush();
    die();
}

$jwtMiddleware = new JwtMiddleware;
$jwtMiddleware->verificar();

$idOt = $_GET['idOt'];
$codOt = $_GET['codOt'];
$tipoOt = $_GET['tipoOt'];
$cliente = $_GET['cliente'];

if (!$idOt || !$codOt || !$tipoOt || !$cliente) {
    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS</p>';
    scrollUpdate();
    @ob_flush();
    flush();
    die();
}



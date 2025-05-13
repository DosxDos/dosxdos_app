<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once 'middlewares/jwtMiddleware.php';
require_once 'clases/crm_clase.php';

if (!isset($_GET['idOt']) || !isset($_GET['codOt']) || !isset($_GET['tipoOt']) || !isset($_GET['cliente']) || !isset($_GET['tokenJwt'])) {
    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS</p>';
    scrollUpdate();
    @ob_flush();
    flush();
    die();
}

/* $jwtMiddleware = new JwtMiddleware;
$jwtMiddleware->verificar(); */

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

$crm = new Crm;
$numLineas = 0;
$lineas;

/* LINEAS */
$camposLineas = "Product_Name,Codigo_de_l_nea,Punto_de_venta,Tipo_de_trabajo,Incluir,Ancho_medida,Alto_medida,Material,Acabados1,Impuesto_Cliente,Alto_total,Ancho_total,Poner,Unit_Price,Realizaci_n,Montaje";
$query = "SELECT $camposLineas FROM Products WHERE OT_relacionada=$idOt";
$crm->query($query);
if ($crm->estado) {
    $lineas = $crm->respuesta[1]['data'];
    $lineas = ordenarArrayPorCampo($lineas, 'Codigo_de_l_nea');
    $numLineas = count($lineas);
    /* print_r($lineas); */
} else {
    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR LAS LÍNEAS DE LA OT ' . $codOt . ' EN LA API DEL CRM</p>';
    print_r($crm->respuestaError);
    scrollUpdate();
    @ob_flush();
    flush();
    die();
}

// FUNCIÓN PARA HACER SCROLL Y MANTENER LA VISIBILIDAD DEL ÚLTIMO MENSAJE DEL SERVIDOR
function scrollUpdate()
{
    $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
    echo '<p id="' . $uniqueId . '" style="display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">...</p>';
    echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
}

//FUNCIÓN PARA ORDENAR RESPUESTAS (ARRAY INDEXADOS) POR CAMPOS
function ordenarArrayPorCampo(array $array, string $campo, string $orden = 'asc'): array
{
    usort($array, function ($a, $b) use ($campo, $orden) {
        $valA = isset($a[$campo]) ? (int)$a[$campo] : 0;
        $valB = isset($b[$campo]) ? (int)$b[$campo] : 0;
        return $orden === 'asc' ? $valA <=> $valB : $valB <=> $valA;
    });
    return $array;
}

 // Agrupamos los datos por punto de venta
 $lineas = $crm->respuesta[1]['data'] ?? [];
 $informes = [];
 foreach ($lineas as $linea) {
    $clave = isset($linea['Punto_de_venta']) && is_scalar($linea['Punto_de_venta']) 
        ? (string)$linea['Punto_de_venta'] 
        : 'Desconocido';

    if (!isset($informes[$clave])) {
        $informes[$clave] = [
            'ot' => "V-" . " " . $codOt,
            'puntoVenta' => $linea['Product_Name'] ?? '',
            'direccion' => $linea['Direcci_n'] ?? '',
            'detalles' => []
        ];
    }

    $informes[$clave]['detalles'][] = [
        'tipo' => $linea['Tipo_de_trabajo'] ?? '',
        'firma' => $linea['firma'] ?? '',
        'quitar' => $linea['Realizaci_n'] ?? '',
        'poner' => $linea['Montaje'] ?? '',
        'dimensiones' => trim(($linea['Alto_medida'] ?? '') . ' x ' . ($linea['Ancho_medida'] ?? ''))
    ];
}

// Enviamos el array como JSON indexado
header('Content-Type: application/json');
echo json_encode(array_values($informes));
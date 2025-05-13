<?php

echo 'Soy el fichero informe_ot_montajes.php';

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
    print_r($lineas);
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

// FUNCIÓN PARA OBTENER LA INFORMACION DE LAS OTS Y MANDARLA AL LOCALSTORAGE


/* $conexion = new mysqli("localhost", "usuario", "contraseña", "base_datos");  */

/* if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} */

// Consulta a la tabla donde están tus órdenes de trabajo (OTs)
/* $sql = "SELECT ot, punto_venta, direccion, tipo, firma, quitar, poner, dimensiones FROM informes ORDER BY ot";
$resultado = $conexion->query($sql);

$informes = [];

while ($fila = $resultado->fetch_assoc()) {
    $otKey = $fila['ot']; */

    // Agrupa las OT y sus detalles
    /* if (!isset($informes[$otKey])) {
        $informes[$otKey] = [
            'ot' => $fila['ot'],
            'puntoVenta' => $fila['punto_venta'],
            'direccion' => $fila['direccion'],
            'detalles' => []
        ];
    }

    $informes[$otKey]['detalles'][] = [
        'tipo' => $fila['tipo'],
        'firma' => $fila['firma'],
        'quitar' => $fila['quitar'],
        'poner' => $fila['poner'],
        'dimensiones' => $fila['dimensiones']
    ];
}
 */
// Convierte a array indexado
/* $datos = array_values($informes); */

// Devuelve JSON
/* header('Content-Type: application/json');
echo json_encode($datos);
 */
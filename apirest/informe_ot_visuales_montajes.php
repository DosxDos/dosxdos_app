<?php

error_reporting(E_ALL); // Muestra los errores
ini_set('display_errors', 1); // Muestra los errores
ini_set('curl.cainfo', '/dev/null'); // Permite hacer cURRl con certificados
set_time_limit(0); // Elimina el límite de ejecución
ini_set('default_socket_timeout', 28800); // Tiempo de espera de conexión 8h
date_default_timezone_set('Atlantic/Canary');  // Zona horaria Canarias

require_once 'middlewares/jwtMiddleware.php'; // Autenticación
require_once 'clases/crm_clase.php'; // Para hacer consultas al CRM

header('Content-Type: application/json'); // Siempre respondemos JSON

// Función para responder error en JSON y terminar ejecución
function responderError($mensaje, $detalle = null) {
    $response = ['error' => true, 'message' => $mensaje];
    if ($detalle !== null) {
        $response['detalle'] = $detalle;
    }
    echo json_encode($response);
    exit;
}

// Validamos que recibimos todos los parámetros necesarios
if (!isset($_GET['idOt'], $_GET['codOt'], $_GET['tipoOt'], $_GET['cliente'], $_GET['tokenJwt'])) {
    responderError('ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS');
}

// Descomentar para usar JWT si lo deseas
/* $jwtMiddleware = new JwtMiddleware;
$jwtMiddleware->verificar(); */

// Asignamos variables sanitizadas
$idOt = trim($_GET['idOt']);
$codOt = trim($_GET['codOt']);
$tipoOt = trim($_GET['tipoOt']);
$cliente = trim($_GET['cliente']);

// Sacamos error si falta alguna variable o está vacía
if (!$idOt || !$codOt || !$tipoOt || !$cliente) {
    responderError('ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS');
}

$crm = new Crm; // Creamos una instancia para la clase crm

/* LINEAS */
// Campos necesarios, eliminados los que no se usan para reducir datos transferidos
$camposLineas = "Codigo_de_l_nea,Punto_de_venta,rea,Tipo_de_trabajo,Zona,Direcci_n,Poner,Quitar,Alto_medida,Ancho_medida,Firma_de_la_OT_relacionada,nombreCliente,nombreOt,nombrePv,Fecha_entrada,Alto_total,Ancho_total,Material,Ubicaci_n,Punto_de_venta.N_tel_fono";

// Descubrimiento importante, el N_tel_fono no se encuentra en la info de la línea, por lo que tenemos que buscarlo en el punto de venta
// Simplemente usando Punto_de_venta.N_tel_fono se soluciona el problema aunque estemos buscando un campo que no existe en la tabla de líneas, ya que el CRM lo interpreta como una búsqueda de campo relacionado.
$query = "SELECT $camposLineas FROM Products WHERE OT_relacionada=$idOt";

$crm->query($query); // Consulta SQL al CRM para obtener todas las líneas asociadas a idOt

if (!$crm->estado) {
    responderError("ERROR!!! AL CONSULTAR LAS LÍNEAS DE LA OT $codOt EN LA API DEL CRM", $crm->respuestaError);
}

// Extraemos las líneas y ordenamos
$lineas = $crm->respuesta[1]['data'] ?? [];

if (empty($lineas)) {
    // Respuesta vacía (sin líneas), se podría devolver estructura vacía o mensaje
    echo json_encode([
        'ot' => [
            'codOt' => "V- " . $codOt,
            'firma' => ''
        ],
        'pvs' => []
    ]);
    exit;
}

// FUNCIÓN PARA ORDENAR RESPUESTAS (ARRAY INDEXADOS) POR CAMPOS
function ordenarArrayPorCampo(array $array, string $campo, string $orden = 'asc'): array
{
    usort($array, function ($a, $b) use ($campo, $orden) {
        // Convertimos a string para evitar warnings si el campo no existe o no es numérico
        $valA = isset($a[$campo]) ? $a[$campo] : '';
        $valB = isset($b[$campo]) ? $b[$campo] : '';
        if (is_numeric($valA) && is_numeric($valB)) {
            $valA = (int)$valA;
            $valB = (int)$valB;
        }
        if ($orden === 'asc') {
            return $valA <=> $valB;
        } else {
            return $valB <=> $valA;
        }
    });
    return $array;
}

// Ordenamos líneas por Código_de_l_nea ascendente
$lineas = ordenarArrayPorCampo($lineas, 'Codigo_de_l_nea');

// Estructura base de la respuesta
$estructuraFinal = [
    'ot' => [
        'codOt' => "V- " . $codOt,
        'firma' => $lineas[0]['Firma_de_la_OT_relacionada'] ?? '',
        'cliente' => $lineas[0]['nombreCliente'] ?? '',
    ],
    'pvs' => []
];

$pvsAgrupados = [];

// Agrupamos líneas por punto de venta
foreach ($lineas as $linea) {
    // Aseguramos que la clave del array sea un string
    $valorBruto = $linea['Punto_de_venta'] ?? 'Desconocido';
    if (is_array($valorBruto) || is_object($valorBruto)) {
        $clavePv = json_encode($valorBruto) ?: 'Desconocido';
    } else {
        $clavePv = (string)$valorBruto;
    }

    if (!isset($pvsAgrupados[$clavePv])) {
        $pvsAgrupados[$clavePv] = [
            'nombre' => $linea['nombrePv'] ?? '',
            'direccion' => $linea['Direcci_n'] ?? '',
            'telefono' => $linea['Punto_de_venta.N_tel_fono'] ?? '',
            'area' => $linea['rea'] ?? '',
            'zona' => $linea['Zona'] ?? '',
            'nombreOt' => $linea['nombreOt'] ?? '',
            'lineas' => []
        ];
    }

    // Construimos línea con valores claros y fallback en dimensiones
    $ancho = (!empty($linea['Alto_total']) && !empty($linea['Ancho_total'])) ? $linea['Ancho_total'] : $linea['Ancho_medida'];
    $alto = (!empty($linea['Alto_total']) && !empty($linea['Ancho_total'])) ? $linea['Alto_total'] : $linea['Alto_medida'];

    $pvsAgrupados[$clavePv]['lineas'][] = [
        'linea' => $linea['Codigo_de_l_nea'] ?? '',
        'ubicacion' => $linea['Ubicaci_n'] ?? '',
        'tipo' => trim(($linea['Material'] ?? '') . " - " . ($linea['Tipo_de_trabajo'] ?? '')),
        'fechaEntrada' => $linea['Fecha_entrada'] ?? '',
        'quitar' => $linea['Quitar'] ?? '',
        'poner' => $linea['Poner'] ?? '',
        'ancho'=> $ancho,
        'alto'=> $alto
    ];
}

// Convertimos a array numérico para JSON limpio
$estructuraFinal['pvs'] = array_values($pvsAgrupados);

// Devolvemos como JSON con JSON_UNESCAPED_UNICODE para mejor legibilidad
echo json_encode($estructuraFinal, JSON_UNESCAPED_UNICODE);

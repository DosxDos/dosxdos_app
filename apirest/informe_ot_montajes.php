<?php

error_reporting(E_ALL); // Muestra los errores
ini_set('display_errors', 1); // Muestra los errores
ini_set('curl.cainfo', '/dev/null'); // Permite hacer cURRl con certificados
set_time_limit(0); // Elimina el límite de ejecucción
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

// Mostramos si falta algún parámetro
if (!isset($_GET['idOt'], $_GET['codOt'], $_GET['tipoOt'], $_GET['cliente'], $_GET['tokenJwt'])) {
    responderError('ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS');
}

/* $jwtMiddleware = new JwtMiddleware;
$jwtMiddleware->verificar(); */

// Asignamos variables
$idOt = $_GET['idOt'];
$codOt = $_GET['codOt'];
$tipoOt = $_GET['tipoOt'];
$cliente = $_GET['cliente'];

// Sacamos error si falta alguna variable
if (!$idOt || !$codOt || !$tipoOt || !$cliente) {
    responderError('ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS');
}

$crm = new Crm; // Creamos una instancia para la clase crm

/* LINEAS */
$camposLineas = "Codigo_de_l_nea,C_digo_de_OT_relacionada,Punto_de_venta,rea,Tipo_de_OT,Tipo_de_trabajo,Descripci_n_Tipo_Trabajo,Zona,Sector,Direcci_n,Nombre_de_Empresa,Fecha_actuaci_n,Fase,Motivo_de_incidencia,Observaciones_internas,Observaciones_montador,Horas_actuaci_n,D_as_actuaci_n,Minutos_actuaci_n,Poner,Quitar,Alto_medida,Ancho_medida,Fotos,Firma_de_la_OT_relacionada,Estado_de_Actuaci_n,nombreCliente,nombreOt,nombrePv,codPv,Fecha_entrada";
$query = "SELECT $camposLineas FROM Products WHERE OT_relacionada=$idOt"; //Definimos la lista de campos a recuperar

$crm->query($query); //Consulta SQL al CRM para obtener todas las líneas asociadas a idOt

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
        $valA = isset($a[$campo]) ? (int)$a[$campo] : 0;
        $valB = isset($b[$campo]) ? (int)$b[$campo] : 0;
        return $orden === 'asc' ? $valA <=> $valB : $valB <=> $valA;
    });
    return $array;
}

$lineas = ordenarArrayPorCampo($lineas, 'Codigo_de_l_nea');

$estructuraFinal = [
    'ot' => [
        'codOt' => "V- " . $codOt,
        'firma' => $lineas[0]['Firma_de_la_OT_relacionada'] ?? '',
        'cliente' => $lineas[0]['nombreCliente'] ?? '',
    ],
    'pvs' => []
];

$pvsAgrupados = [];

foreach ($lineas as $linea) {
    // Aseguramos que la clave del array sea un string (no array u objeto)
    $valorBruto = $linea['Punto_de_venta'] ?? 'Desconocido';
    if (is_array($valorBruto) || is_object($valorBruto)) {
        $clavePv = json_encode($valorBruto);
        if ($clavePv === false) {
            $clavePv = 'Desconocido';
        }
    } else {
        $clavePv = (string)$valorBruto;
    }

    if (!isset($pvsAgrupados[$clavePv])) {
        $pvsAgrupados[$clavePv] = [
            'nombre' => $linea['nombrePv'] ?? '',
            'direccion' => $linea['Direcci_n'] ?? '',
            'telefono' => $linea['N_tel_fono'] ?? '',
            'area' => $linea['rea'] ?? '',
            'zona' => $linea['Zona'] ?? '',
            'nombreOt' => $linea['nombreOt'] ?? '',
            'lineas' => []
        ];
    }

    $pvsAgrupados[$clavePv]['lineas'][] = [
        'Línea' => $linea['Tipo_de_OT'] ?? '',
        'Ubicación' => $linea[''] ?? '',
        'tipo' => $linea['Tipo_de_OT'] ?? '',
        'fechaEntrada' => $linea['Fecha_entrada'] ?? '',
        'quitar' => $linea['Quitar'] ?? '',
        'poner' => $linea['Poner'] ?? '',
        'alto' => $linea['Alto_medida'] ?? '',
        'ancho' => $linea['Ancho_medida'] ?? ''
        // Agrega más campos si los necesitas
    ];
}

// Convertimos a array numérico
$estructuraFinal['pvs'] = array_values($pvsAgrupados);

// Devolvemos como JSON
echo json_encode($estructuraFinal);

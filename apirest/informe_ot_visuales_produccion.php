<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once 'middlewares/jwtMiddleware.php';
require_once 'clases/crm_clase.php';

header('Content-Type: application/json');

function responderError($mensaje, $detalle = null) {
    $response = ['error' => true, 'message' => $mensaje];
    if ($detalle !== null) {
        $response['detalle'] = $detalle;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Validación de parámetros obligatorios
    $required = ['idOt', 'codOt', 'tipoOt', 'cliente', 'tokenJwt'];
    foreach ($required as $param) {
        if (!isset($_GET[$param]) || trim($_GET[$param]) === '') {
            responderError("ERROR!!! Falta el parámetro: $param");
        }
    }

    // JWT
   /*  $jwt = new JwtMiddleware(); */
   /*  $jwt->verificar(); */

    // Parámetros GET
    $idOt = trim($_GET['idOt']);
    $codOt = trim($_GET['codOt']);
    $tipoOt = trim($_GET['tipoOt']);
    $cliente = trim($_GET['cliente']);

    $crm = new Crm;

    // Lista de campos
    $camposLineas = "Codigo_de_l_nea,Punto_de_venta,rea,Tipo_de_trabajo,Zona,Direcci_n,Poner,Quitar,Alto_medida,Ancho_medida,Firma_de_la_OT_relacionada,nombreCliente,nombreOt,nombrePv,Alto_total,Ancho_total,Material,Ubicaci_n,Punto_de_venta.N_tel_fono";

    // Construcción de la consulta
    $query = "SELECT $camposLineas FROM Products WHERE OT_relacionada=$idOt AND Fase!='Perdidas'";

    // Log para depuración
    file_put_contents('debug.log', date('c') . " - Query: $query\n", FILE_APPEND);

    // Ejecutamos consulta
    $crm->query($query);
    if (!$crm->estado) {
        responderError("ERROR al consultar línea de OT $codOt", $crm->respuestaError);
    }

    // Validar estructura de la respuesta
    if (!isset($crm->respuesta) || !is_array($crm->respuesta) || 
        !isset($crm->respuesta[1]['data']) || !is_array($crm->respuesta[1]['data'])) {
        responderError("ERROR: La respuesta del CRM no tiene los datos esperados.", json_encode($crm->respuesta));
    }

    $lineas = $crm->respuesta[1]['data'];

    if (empty($lineas)) {
        echo json_encode(['ot' => ['codOt' => "V- $codOt", 'firma' => '', 'cliente' => ''], 'pvs' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Función para ordenar por campo
    function ordenarArrayPorCampo(array $array, string $campo, string $orden = 'asc'): array {
        usort($array, function ($a, $b) use ($campo, $orden) {
            $valA = $a[$campo] ?? '';
            $valB = $b[$campo] ?? '';
            if (is_numeric($valA) && is_numeric($valB)) {
                $valA = (int)$valA;
                $valB = (int)$valB;
            }
            return $orden === 'asc' ? $valA <=> $valB : $valB <=> $valA;
        });
        return $array;
    }

    $lineas = ordenarArrayPorCampo($lineas, 'Codigo_de_l_nea');

    // Inicializamos estructura de salida
    $estructura = [
        'ot' => [
            'codOt' => "V- $codOt",
            'firma' => $lineas[0]['Firma_de_la_OT_relacionada'] ?? '',
            'cliente' => $lineas[0]['nombreCliente'] ?? ''
        ],
        'pvs' => []
    ];

    // Agrupar por punto de venta
    $pvsAgrupados = [];

    foreach ($lineas as $linea) {
        $clavePv = is_scalar($linea['Punto_de_venta']) ? (string)$linea['Punto_de_venta'] : json_encode($linea['Punto_de_venta']);
        
        $pv = $pvsAgrupados[$clavePv] ?? [
            'nombre' => $linea['nombrePv'] ?? '',
            'direccion' => $linea['Direcci_n'] ?? '',
            'telefono' => $linea['Punto_de_venta.N_tel_fono'] ?? '',
            'area' => $linea['rea'] ?? '',
            'zona' => $linea['Zona'] ?? '',
            'nombreOt' => $linea['nombreOt'] ?? '',
            'lineas' => []
        ];

        $ancho = (!empty($linea['Alto_total']) && !empty($linea['Ancho_total'])) ? $linea['Ancho_total'] : $linea['Ancho_medida'];
        $alto  = (!empty($linea['Alto_total']) && !empty($linea['Ancho_total'])) ? $linea['Alto_total'] : $linea['Alto_medida'];

        $pv['lineas'][] = [
            'linea'      => $linea['Codigo_de_l_nea'] ?? '',
            'ubicacion'  => $linea['Ubicaci_n'] ?? '',
            'tipo'       => trim(($linea['Material'] ?? '') . " - " . ($linea['Tipo_de_trabajo'] ?? '')),
            'firma'      => $estructura['ot']['firma'],
            'quitar'     => $linea['Quitar'] ?? '',
            'poner'      => $linea['Poner'] ?? '',
            'ancho'      => $ancho,
            'alto'       => $alto
        ];

        $pvsAgrupados[$clavePv] = $pv;
    }

    $estructura['pvs'] = array_values($pvsAgrupados);

    // Respuesta final
    echo json_encode($estructura, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    responderError("Excepción capturada: " . $e->getMessage());
}

<?php

require_once __DIR__ . '\crm_clase.php';

ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

try {
    $file = __DIR__ . '\UPDATED_PDVS14.json';
    $pdvs = json_decode(file_get_contents($file), true);
    //print_r($pdvs);
    $crm = new Crm;
    //echo count($pdvs);
    $i = 0;
    foreach ($pdvs as $pdv) {
        if ($pdv['lat']) {
            $pdvVector = [];
            //$id_con_prefijo = $pdv['ID de registro'];
            $id_con_prefijo = $pdv['Record Id'];
            $id_numerico = preg_replace('/\D/', '', $id_con_prefijo); // Eliminar todo lo que no sea un dígito
            $nombrePv = $pdv['Punto de venta'];
            $pdvVector['data'][0]['id'] = $id_numerico;
            $stringLat = (string)$pdv['lat'];
            $stringLng = (string)$pdv['lng'];
            $pdvVector['data'][0]['lat'] =  $stringLat;
            $pdvVector['data'][0]['lng'] = $stringLng;
            $pdvJson = json_encode($pdvVector);
            $crm->actualizar("actualizarPdvs", $pdvJson);
            if ($crm->estado) {
                echo  "<p>" . $i . "___RES_____________________________</p>";
                echo 'PDV ACTUALIZADO : ' . $nombrePv;
                print_r($crm->respuesta);
                @ob_flush();
                flush();
            } else {
                echo  "<p>" . $i . "___ERROR_____________________________</p>";
                echo 'NO HA SIDO POSIBLE ACTUALIZAR: ' . $nombrePv;
                print_r($crm->respuestaError);
                @ob_flush();
                flush();
            }
            $i++;
            usleep(mt_rand(0, 999999));
        }
    }
} catch (\Throwable $th) {
    print_r($th);
}

<?php

ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once __DIR__ . '\crm_clase.php';
require_once __DIR__ . '\respuestas_clase.php';

try {
    // Nombre del archivo CSV
    $filename = 'lineas.csv';

    // Inicializar un array para almacenar los datos
    $lineas = [];

    // Abrir el archivo para lectura
    if (($handle = fopen($filename, 'r')) !== false) {
        // Leer la primera línea para obtener los nombres de las columnas
        $header = fgetcsv($handle, 1000, ',');

        // Leer cada línea del archivo CSV
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            // Combinar los nombres de las columnas con los datos para crear un array asociativo
            $lineas[] = array_combine($header, $data);
        }

        // Cerrar el archivo
        fclose($handle);
    }

    /*
    // Imprimir el array resultante
    $cantidadLineas = count($lineas);
    echo "<p>" . $cantidadLineas . "</p>";
    print_r($lineas);
    */

    $crm = new Crm;
    //echo count($lineas);
    $i = 0;
    foreach ($lineas as $linea) {
        if ($linea['Punto de venta.id']) {
            $id_pv_con_prefijo = $linea['Punto de venta.id'];
            $id_pv_numerico = preg_replace('/\D/', '', $id_pv_con_prefijo); // Eliminar todo lo que no sea un dígito
            $camposPv = "lat,lng";
            $query = "SELECT $camposPv FROM Puntos_de_venta WHERE id=$id_pv_numerico";
            $crm->query($query);
            if ($crm->estado) {
                /*
                echo  "<p>" . $i . "___RES_____________________________</p>";
                echo 'PUNTO DE VENTA CONSULTADO: ' . $linea['Punto de venta'];
                print_r($crm->respuesta[1]['data'][0]);
                @ob_flush();
                flush();
                */
                $responsePv = $crm->respuesta[1]['data'][0];
                $lat = $responsePv['lat'];
                $lng = $responsePv['lng'];

                $lineaVector = [];
                $id_con_prefijo = $linea['ID de registro'];
                //$id_con_prefijo = $linea['Record Id'];
                $id_numerico = preg_replace('/\D/', '', $id_con_prefijo); // Eliminar todo lo que no sea un dígito
                $nombreLinea = $linea['Nombre de Línea de OT'];
                $lineaVector['data'][0]['id'] = $id_numerico;
                /*
                $stringLat = (string)$lat;
                $stringLng = (string)$lng;
                */
                $lineaVector['data'][0]['lat'] =  $lat;
                $lineaVector['data'][0]['lng'] = $lng;
                $lineaJson = json_encode($lineaVector);
                $crm->actualizar("actualizarLinea", $lineaJson);
                if ($crm->estado) {
                    echo  "<p>" . $i . "___RES_____________________________</p>";
                    echo 'LINEA ACTUALIZADA : ' . $nombreLinea;
                    print_r($crm->respuesta);
                    @ob_flush();
                    flush();
                } else {
                    echo  "<p>" . $i . "___ERROR_____________________________</p>";
                    echo 'NO HA SIDO POSIBLE ACTUALIZAR LA LÍNEA: ' . $nombreLinea;
                    print_r($crm->respuestaError);
                    @ob_flush();
                    flush();
                }
            } else {
                echo  "<p>" . $i . "___ERROR_____________________________</p>";
                echo 'NO HA SIDO POSIBLE CONSULTAR EL PUNTO DE VENTA: ' . $linea['Punto de venta'];
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

<?php

ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once __DIR__ . '\..\..\clases\crm_clase.php';
require_once __DIR__ . '\..\..\clases\respuestas_clase.php';

try {
    // Nombre del archivo CSV
    $filename = __DIR__ . '\lineas.csv';

    // Inicializar un array para almacenar los datos
    $lineas = [];

    // Abrir el archivo para lectura
    if (($handle = fopen($filename, 'r')) !== false) {
        // Leer la primera línea para obtener los nombres de las columnas
        $header = fgetcsv($handle, 1000, ',');

        // Leer cada línea del archivo CSV
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($header) !== count($data)) {
                // Error: cantidad de columnas no coincide, puedes saltarlo o reportarlo
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! El número de columnas no coincide en línea: </p>';
                print_r($data);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }
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

    $montadores = [];
    $montadores['122'] = 707987000009153031;
    $montadores['120'] = 707987000007920663;
    $montadores['113'] = 707987000007608291;
    $montadores['118'] = 707987000007162258;
    $montadores['109'] = 707987000004569113;
    $montadores['5'] = 707987000001391463;
    $montadores['4'] = 707987000001391428;
    $montadores['3'] = 707987000001391408;
    $montadores['106'] = 707987000001387066;
    $montadores['105'] = 707987000001387065;
    $montadores['104'] = 707987000001387064;
    $montadores['61'] = 707987000001387063;
    $montadores['48'] = 707987000001387062;
    $montadores['45'] = 707987000001387061;
    $montadores['44'] = 707987000001387060;
    $montadores['43'] = 707987000001387059;
    $montadores['42'] = 707987000001387058;
    $montadores['40'] = 707987000001387057;
    $montadores['14'] = 707987000001387056;
    $montadores['13'] = 707987000001387055;
    $montadores['11'] = 707987000001387054;
    $montadores['10'] = 707987000001387053;
    $montadores['9'] = 707987000001387052;
    $montadores['8'] = 707987000001387051;
    $montadores['30'] = 707987000016645356;
    $montadores['111'] = 707987000016645362;

    $crm = new Crm;
    //echo count($lineas);
    $i = 0;
    foreach ($lineas as $linea) {
        $codLinea = $linea['Código de línea'];
        if ($linea['montadorUsuarioApp']) {
            $idApp = $linea['montadorUsuarioApp'];
            if ($montadores[$idApp]) {
                $idCrm = $montadores[$idApp];
                $lineaVector = [];
                $id_con_prefijo = $linea['ID de registro'];
                $id_numerico = preg_replace('/\D/', '', $id_con_prefijo); // Eliminar todo lo que no sea un dígito
                $lineaVector['data'][0]['id'] = $id_numerico;
                $lineaVector['data'][0]['Montador'] =  $idCrm;
                $lineaJson = json_encode($lineaVector);
                $crm->actualizar("actualizarLinea", $lineaJson);
                if ($crm->estado) {
                    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">' . $i . '</p>';
                    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LINEA ACTUALIZADA: ' . $codLinea . '</p>';
                    print_r($crm->respuesta);
                    scrollUpdate();
                    @ob_flush();
                    flush();
                } else {
                    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">' . $i . '</p>';
                    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EN LA LÍNEA: ' . $codLinea . '</p>';
                    print_r($crm->respuesta);
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
                $i++;
                usleep(mt_rand(0, 999999));
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">' . $i . '</p>';
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! El montador no existe en la lista de montadores</p>';
                print_r($linea);
                scrollUpdate();
                @ob_flush();
                flush();
                $i++;
            }
        } else {
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">' . $i . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LINEA SIN MONTADOR: ' . $codLinea . '</p>';
            print_r($linea);
            scrollUpdate();
            @ob_flush();
            flush();
            $i++;
        }
    }
} catch (\Throwable $th) {
    print_r($th);
}

function scrollUpdate()
{
    $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
    echo '<p id="' . $uniqueId . '" style="display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">...</p>';
    echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
}

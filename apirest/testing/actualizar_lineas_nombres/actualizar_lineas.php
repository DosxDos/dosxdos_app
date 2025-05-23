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

    $crm = new Crm;
    //echo count($lineas);
    $i = 0;
    foreach ($lineas as $linea) {
        $codLinea = $linea['Código de línea'];
        $nombreLinea = $linea['Nombre de Línea de OT'];
        $nuevoNombre = $nombreLinea . ', ' . $codLinea;
        $lineaVector = [];
        $id_con_prefijo = $linea['ID de registro'];
        $id_numerico = preg_replace('/\D/', '', $id_con_prefijo); // Eliminar todo lo que no sea un dígito
        $lineaVector['data'][0]['id'] = $id_numerico;
        $lineaVector['data'][0]['Product_Name'] =  $nuevoNombre;
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
            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">' . $i . '</p>';
            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EN LA LÍNEA: ' . $codLinea . '</p>';
            print_r($crm->respuesta);
            scrollUpdate();
            @ob_flush();
            flush();
        }
        $i++;
        usleep(mt_rand(0, 999999));
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

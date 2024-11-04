
<?php

require_once __DIR__ . '\crm_clase.php';
require_once __DIR__ . '\respuestas_clase.php';

try {
    // Nombre del archivo CSV
    $filename = 'pdvs.csv';

    // Inicializar un array para almacenar los datos
    $pdvs = [];

    // Abrir el archivo para lectura
    if (($handle = fopen($filename, 'r')) !== false) {
        // Leer la primera línea para obtener los nombres de las columnas
        $header = fgetcsv($handle, 1000, ',');

        // Leer cada línea del archivo CSV
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            // Combinar los nombres de las columnas con los datos para crear un array asociativo
            $pdvs[] = array_combine($header, $data);
        }

        // Cerrar el archivo
        fclose($handle);
    }

    /*
    // Imprimir el array resultante
    $cantidadPdvs = count($pdvs);
    echo $cantidadPdvs;
    print_r($pdvs);
    */

    // Suponiendo que $datos es tu array original con más de 2000 elementos

    // Tamaño del subarray
    $tamano_subarray = 200;

    // Inicializar un array para almacenar los subarrays
    $subarrays = [];

    // Calcular cuántos subarrays se necesitan
    $total_subarrays = ceil(count($pdvs) / $tamano_subarray);

    // Llenar el array de subarrays
    for ($i = 0; $i < $total_subarrays; $i++) {
        // Obtener un subarray de $datos
        $subarrays[$i] = array_slice($pdvs, $i * $tamano_subarray, $tamano_subarray);
    }

    /*
    // Imprimir el resultado
    echo count($subarrays);
    print_r($subarrays);
    */

    foreach ($subarrays as $lote) {
        $json = json_encode($lote, JSON_UNESCAPED_UNICODE);
        echo $json;
        echo "________________________________________________________JSON";
    }


    /*
    $pdvVector = [];
    $pdvVector['data'][0]['id'] = $lineaDatos['id'];
    $pdvVector['data'][0]['montadorUsuarioApp'] = $usuario;
    $pdvVector['data'][0]['Estado_de_Actuaci_n'] = $json['Estado'];
    if ($json['Estado'] == "Realizado") {
        $pdvVector['data'][0]['Fase'] = "Terminadas";
    }
    if ($json['Incidencia']) {
        $pdvVector['data'][0]['Motivo_de_incidencia'] = $json['Incidencia'];
        $pdvVector['data'][0]['Montador_de_la_incidencia'] = $usuario;
        $pdvVector['data'][0]['Fase'] = "Incidencias";
    }
    $pdvVector['data'][0]['D_as_actuaci_n'] = $json['DiasActuacion'];
    $pdvVector['data'][0]['Fecha_actuaci_n'] = $json['FechaActuacion'];
    $pdvVector['data'][0]['Horas_actuaci_n'] = $json['HorasActuacion'];
    $pdvVector['data'][0]['Minutos_actuaci_n'] = $json['MinutosActuacion'];
    $pdvVector['data'][0]['Observaciones_montador'] = $json['ObservacionesTecnico'];
    $pdvVector['data'][0]['Fotos'] = $json['LinkWeb'];

    $LineaJson = json_encode($pdvVector);
    $crm->actualizar("actualizarLinea", $LineaJson);
    */
} catch (\Throwable $th) {
    print_r($th);
}

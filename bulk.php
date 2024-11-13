<?php
ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once './apirest/clases/crm_clase.php';

try {
    //TODOS LOS DATOS DE UN MÓDULO
    $crm = new Crm;
    $fecha1 = "2024-10-01";
    $fecha1 = "2024-10-31";
    $json = '{
        "query": {
            "module": {
                "api_name": "Products"
            },
            "criteria": {
                "comparator": "between",
                "field": {
                    "api_name": "Fecha_actuaci_n"
                },
                "value": [' . '"' . $fecha1 . '"' . ',' . 
                '"' . $fecha2 . '"' . ']
            }
        }
    }';
    /*
    $vectorJson = [];
    $vectorJson['callback']['url'] = "https://dosxdos.app.iidos.com/callBackBulkCrm.php";
    $vectorJson['callback']['method'] = "post";
    $vectorJson['query']['module']['api_name'] = "Products";
    $vectorJson['query']['criteria']['field']['api_name']= "Fecha_actuaci_n";
    $vectorJson['query']['criteria']['comparator']= "between";
    $vectorJson['file_type'] = "csv";
    $json = json_encode($vectorJson, JSON_FORCE_OBJECT);
    //echo $json;
    */
    $response = $crm->agregar('bulkRead', $json);
    print_r($response);
    if ($crm->estado) {
        if ($crm->respuesta[1]['data'][0]['status'] == "success") {
            echo '<p id="p1" style="font-size: 20px; font-weight: bold; color: green">Solicitud al CRM exitosa, esperando recibir respuesta de los datos solicitados...</p>';
            echo '<script>window.location.href = "#p1";</script>';
            @ob_flush();
            flush();
            $crmResponse;
            while (true) {
                sleep(5);
                if (file_get_contents("callBackBulkCrm.json")) {
                    $crmResponse = file_get_contents("callBackBulkCrm.json");
                    $crmResponse = json_decode($crmResponse, true);
                    print_r($crmResponse);
                    if (unlink("callBackBulkCrm.json")) {
                        echo '<p id="p5" style="font-size: 20px; font-weight: bold; color: green">Datos recibidos y archivo json eliminado. Solicitando descargar los datos...</p>';
                        echo '<script>window.location.href = "#p5";</script>';
                        @ob_flush();
                        flush();
                    } else {
                        echo '<p id="p6" style="font-size: 20px; font-weight: bold; color: red">Datos recibidos, error al eliminar el archivo json. Solicitando descargar los datos...</p>';
                        echo '<script>window.location.href = "#p6";</script>';
                        @ob_flush();
                        flush();
                    }
                    break;
                }
            }
            if (isset($crmResponse['state']) && $crmResponse['state'] == "COMPLETED") {
                echo '<p id="p4" style="font-size: 20px; font-weight: bold; color: green">Los datos comprimidos se han generado en el CRM, obteniendo los datos...</p>';
                echo '<script>window.location.href = "#p4";</script>';
                @ob_flush();
                flush();

                $link = $crmResponse['result']['download_url'];
                $getArchivo = $crm->bulkFile($link);

                if ($getArchivo) {
                    $tempZipPath = "./temp_data.zip";
                    // Extraer el contenido del archivo .zip
                    $zip = new ZipArchive;
                    if ($zip->open($tempZipPath) === true) {
                        // Asumimos que el archivo .csv es el primero en el .zip
                        $csvFileName = $zip->getNameIndex(0);
                        $zip->extractTo('./'); // Extrae el .csv en el directorio actual
                        $zip->close();

                        // Leer el archivo .csv y convertirlo en un array asociativo
                        $csvData = [];
                        if (($handle = fopen($csvFileName, "r")) !== false) {
                            $headers = fgetcsv($handle); // Leer la primera línea como encabezados
                            while (($row = fgetcsv($handle)) !== false) {
                                $csvData[] = array_combine($headers, $row);
                            }
                            fclose($handle);
                        } else {
                            die('<p id="p7" style="font-size: 20px; font-weight: bold; color: red">Error al abrir el archivo csv del CRM...</p><script>window.location.href = "#p7";</script>');
                        }

                        // Eliminar el archivo temporal .zip y el .csv extraído si no necesitas almacenarlos
                        unlink($tempZipPath);
                        unlink($csvFileName);

                        // Mostrar el array asociativo resultante
                        print_r($csvData);
                    } else {
                        die('<p id="p7" style="font-size: 20px; font-weight: bold; color: red">Error al abrir el archivo comprimido de los datos del CRM...</p><script>window.location.href = "#p7";</script>');
                    }
                } else {
                    die('<p id="p7" style="font-size: 20px; font-weight: bold; color: red">Error al descargar el archivo comprimido de los datos del CRM...</p><script>window.location.href = "#p7";</script>');
                }
            }
        } else {
            print_r($crm->respuesta);
            echo '<p id="p2" style="font-size: 20px; font-weight: bold; color: red">Error en la solicitud al CRM...</p>';
            echo '<script>window.location.href = "#p2";</script>';
            @ob_flush();
            flush();
        }
    } else {
        print_r($crm->respuestaError);
        echo '<p id="p3" style="font-size: 20px; font-weight: bold; color: red">Error en la solicitud al CRM...</p>';
        echo '<script>window.location.href = "#p3";</script>';
        @ob_flush();
        flush();
    }
} catch (\Throwable $th) {
    var_dump($th);
    echo '<p id="p0" style="font-size: 20px; font-weight: bold; color: red">Error en el calculador....</p>';
    echo '<script>window.location.href = "#p0";</script>';
    @ob_flush();
    flush();
}

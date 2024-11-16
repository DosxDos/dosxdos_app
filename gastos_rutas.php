<?php

/*
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
mysqli_report(MYSQLI_REPORT_OFF);
*/

header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://dosxdos.app.iidos.com');

ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once './apirest/clases/crm_clase.php';
require_once './apirest/clases/crm_clase.php';



try {
    $crm = new Crm;
    $respuesta = new Respuestas;


    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $postBody = file_get_contents("php://input");
        $body = json_decode($postBody, true);

        $fecha1 = $body['fechas']['fechaInicial'];
        $fecha2 = $body['fechas']['fechaFinal'];
        $montadores = $body['montadores'];
        $rutas = $body['montadores'];

        $json = '{"query": {"module": {"api_name": "Products"},"criteria": {"comparator": "between","field": {"api_name": "Fecha_actuaci_n"}, "value": [' . '"' . $fecha1 . '"' . ',' . '"' . $fecha2 . '"' . ']}}}';

        $response = $crm->agregar('bulkRead', $json);

        if ($crm->estado) {

            $response = $respuesta->ok($crm->respuesta);
            http_response_code(200);
            echo json_encode($response);

            /*                
            if ($crm->respuesta[1]['data'][0]['status'] == "success") {
                $crmResponse;
                while (true) {
                    sleep(5);
                    if (file_get_contents("callBackBulkCrm.json")) {
                        $crmResponse = file_get_contents("callBackBulkCrm.json");
                        $crmResponse = json_decode($crmResponse, true);
                        unlink("callBackBulkCrm.json");
                        //print_r($crmResponse);
                        break;
                    }
                }
                if (isset($crmResponse['state']) && $crmResponse['state'] == "COMPLETED") {
                
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
                                $response = $respuesta->error_500('Error al abrir el archivo csv del CRM');
                                http_response_code(500);
                                echo json_encode($response);
                                die();
                            }

                            // Eliminar el archivo temporal .zip y el .csv extraído si no necesitas almacenarlos
                            unlink($tempZipPath);
                            unlink($csvFileName);

                            $response = $respuesta->ok($csvData);
                            http_response_code(200);
                            echo json_encode($response);

                        } else {
                            $response = $respuesta->error_500('Error al abrir el archivo comprimido de los datos del CRM');
                            http_response_code(500);
                            echo json_encode($response);
                            die();
                        }
                    } else {
                        $response = $respuesta->error_500('Error al descargar el archivo comprimido de los datos del CRM');
                        http_response_code(500);
                        echo json_encode($response);
                        die();
                    }
                }
            } else {
                http_response_code(500);
                echo json_encode($crm->respuesta);
            }

            */
        } else {
            http_response_code(500);
            echo json_encode($crm->respuestaError);
        }
    } else {
        $response = $respuesta->error_405();
        http_response_code(405);
        echo json_encode($response);
    }
} catch (\Throwable $th) {
    $mensajeError = $th->getMessage();
    $archivoError = $th->getFile();
    $lineaError = $th->getLine();
    $trazaError = $th->getTraceAsString();
    $errores = [];
    $errores['mensajeError'] = $mensajeError;
    $errores['archivoError'] = $archivoError;
    $errores['lineaError'] = $lineaError;
    $errores['trazaError'] = $trazaError;
    $response = $respuesta->error_500($errores);
    http_response_code(500);
    echo json_encode($response);
}

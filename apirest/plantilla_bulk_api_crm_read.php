<?php

// Habilitar la visualización de errores en PHP
ini_set("display_errors", 1); // Muestra errores en pantalla
ini_set("display_startup_errors", 1); // Muestra errores en el inicio de PHP
error_reporting(E_ALL); // Reporta todos los errores de PHP
// Configurar MySQLi para reportar errores y lanzar excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:8080');

ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once './clases/crm_clase.php';

try {
    $crm = new Crm;
    $respuesta = new Respuestas;
    $csvData = [];

    $json = '{ "callback": 
        {"url": "http://localhost:8080/callBackBulkCrm.php", "method": "post"}, 
        "query": {"module": {"api_name": "Products"},
        "criteria": 
        {"field": {"api_name": "nombrePv"}, 
        "comparator": "equal",
        "value": "${EMPTY}"}}}';

    $response = $crm->agregar('bulkRead', $json);

    if ($crm->estado) {

        if ($crm->respuesta[1]['data'][0]['status'] == "success") {
            $crmResponse;
            $tiempoLimite = 0;
            $archivoJson = __DIR__ . "/../callBackBulkCrm.json"; // Genera una ruta absoluta
            while (true) {
                sleep(5);
                if (file_exists($archivoJson)) { // Comprueba si el archivo existe
                    $crmResponse = file_get_contents($archivoJson);
                    if ($crmResponse !== false) { // Verifica que se haya leído correctamente
                        $crmResponse = json_decode($crmResponse, true);
                        unlink($archivoJson); // Elimina el archivo después de procesarlo
                        break; // Sale del bucle
                    } else {
                        $response = $respuesta->error_500('Error al leer el archivo de respuesta del CRM');
                        http_response_code(500);
                        echo json_encode($response);
                        die();
                    }
                }
                $tiempoLimite = $tiempoLimite + 5;
                if ($tiempoLimite >= 300) {
                    $response = $respuesta->error_500('La API del CRM ha tardado mucho en responder, por favor inténtalo nuevamente');
                    http_response_code(500);
                    echo json_encode($response);
                    die();
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

                        echo count($csvData);
                        print_r($csvData);
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
            } else {
                $response = $respuesta->error_500($crmResponse);
                http_response_code(500);
                echo json_encode($response);
                die();
            }
        } else {
            http_response_code(500);
            echo json_encode($crm->respuesta);
        }
    } else {
        http_response_code(500);
        echo json_encode($crm->respuestaError);
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

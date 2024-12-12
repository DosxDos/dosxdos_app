<?php

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

        //Funcionalidad: Recoger 2 fechas y el montador o array de montadores para los filtros

        if (isset($body['fecha1']) && isset($body['fecha2']) && isset($body['montadores'])) {

            if (empty($body['montadores'])) {
                $response = $respuesta->error_400('No se encuentra el identificador del montador');
                http_response_code(400);
                echo json_encode($response);
                die();
            } else {
                if ($_COOKIE['usuario']) {
                    $usuario = $_COOKIE['usuario'];
                } else {
                    $response = $respuesta->error_400('No se encuentra el identificador del montador');
                    http_response_code(400);
                    echo json_encode($response);
                    die();
                }
            }

            if (empty(trim($body['fecha1'])) || empty(trim($body['fecha2']))) {
                $fecha1 = date("Y-m-d", strtotime("-3 months")); // 3 meses atrás
                $fecha2 = date("Y-m-d"); //fecha actual
            } else {
                $fecha1 = $body['fecha1'];
                $fecha2 = $body['fecha2'];
            }
            $montadores = isset($body['montadores']) ? $body['montadores'] : $usuario;

            $json = '{ "callback": 
        {"url": "https://dosxdos.app.iidos.com/callBackBulkCrm.php", "method": "post"}, 
        "query": {"module": {"api_name": "Products"},
        "criteria": 
        {"comparator": "between",
        "field": {"api_name": "Fecha_actuaci_n"}, 
        "value": [' . '"' . $fecha1 . '"' . ',' . '"' . $fecha2 . '"' . ']}}}';

            $response = $crm->agregar('bulkRead', $json);

            if ($crm->estado) {

                if ($crm->respuesta[1]['data'][0]['status'] == "success") {
                    $crmResponse;
                    while (true) {
                        sleep(5);
                        if (file_exists("callBackBulkCrm.json")) { // Comprueba si el archivo existe
                            $crmResponse = file_get_contents("callBackBulkCrm.json");
                            if ($crmResponse !== false) { // Verifica que se haya leído correctamente
                                $crmResponse = json_decode($crmResponse, true);
                                unlink("callBackBulkCrm.json"); // Elimina el archivo después de procesarlo
                                break; // Sale del bucle
                            }
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

                                $lineasFiltradas = [];

                                //FILTRO DE MONTADORES PARA LA BULK API
                                if ($montadores) {
                                    if (is_array($montado)) {
                                        foreach ($montadores as $montador) {
                                            foreach ($csvData as $linea) {
                                                if($linea['montadorUsuarioApp'] == $montador){
                                                    array_push($lineasFiltradas,$linea);
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($csvData as $linea) {
                                            if($linea['montadorUsuarioApp'] == $montadores){
                                                array_push($lineasFiltradas,$linea);
                                            }
                                        }
                                    }
                                } else {
                                    $response = $respuesta->error_400('Error no se ha enviado el id del montador');
                                    http_response_code(500);
                                    echo json_encode($response);
                                    die();
                                }
                                $response = $respuesta->ok($lineasFiltradas);
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
            } else {
                http_response_code(500);
                echo json_encode($crm->respuestaError);
            }
        } else {
            $response = $respuesta->error_400('cuerpo de la solicitud incompleto');
            http_response_code(400);
            echo json_encode($response);
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

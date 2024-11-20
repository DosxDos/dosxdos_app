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

        $fecha1 = $body['fechas']['fechaInicial'];
        $fecha2 = $body['fechas']['fechaFinal'];
        $montadores = $body['montadores'];
        $rutas = $body['rutas'];

        $json = '{ "callback": {"url": "https://dosxdos.app.iidos.com/callBackBulkCrm.php", "method": "post"}, "query": {"module": {"api_name": "Products"},"criteria": {"comparator": "between","field": {"api_name": "Fecha_actuaci_n"}, "value": [' . '"' . $fecha1 . '"' . ',' . '"' . $fecha2 . '"' . ']}}}';

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
                            $lineasFiltradas2 = [];
                            $otsCrm = [];
                            $otsCalculo = [];
                            $totalMinutos = 0;

                            if ($rutas) {
                                foreach ($csvData as $linea) {
                                    foreach ($rutas as $ruta) {
                                        if ($linea['RutaSelect'] == $ruta['Name']) {
                                            array_push($lineasFiltradas, $linea);
                                        }
                                    }
                                }
                            } else {
                                $lineasFiltradas = $csvData;
                            }


                            if ($montadores) {
                                foreach ($lineasFiltradas as $linea) {
                                    foreach ($montadores as $montador) {
                                        if ($linea['montadorUsuarioApp'] == $montador['idApp']) {
                                            array_push($lineasFiltradas2, $linea);
                                        }
                                    }
                                }
                                foreach ($lineasFiltradas2 as $linea) {
                                    $ot = $linea['C_digo_de_OT_relacionada'];
                                    array_push($otsCrm, $ot);
                                }
                                $otsCrm = array_values(array_unique($otsCrm));
                                foreach ($lineasFiltradas2 as $linea) {
                                    foreach ($otsCrm as $ot) {
                                        if ($linea['C_digo_de_OT_relacionada'] == $ot) {
                                            $otsCalculo[$ot]['navision'] = $linea['Navision_OT'];
                                            $otsCalculo[$ot]['dias'] = 0;
                                            $otsCalculo[$ot]['horas'] = 0;
                                            $otsCalculo[$ot]['minutos'] = 0;
                                            $otsCalculo[$ot]['totalMinutos'] = 0;
                                            $otsCalculo[$ot]['porcentaje'] = 0;
                                        }
                                    }
                                }
                                foreach ($lineasFiltradas2 as $linea) {
                                    foreach ($otsCrm as $ot) {
                                        if ($linea['C_digo_de_OT_relacionada'] == $ot) {
                                            $otsCalculo[$ot]['dias'] += (floatval($linea['D_as_actuaci_n']) * 24) * 60;
                                            $otsCalculo[$ot]['horas'] += floatval($linea['Horas_actuaci_n']) * 60;
                                            $otsCalculo[$ot]['minutos'] += floatval($linea['Minutos_actuaci_n']);
                                        }
                                    }
                                }
                            } else {
                                foreach ($lineasFiltradas as $linea) {
                                    $ot = $linea['C_digo_de_OT_relacionada'];
                                    array_push($otsCrm, $ot);
                                }
                                $otsCrm = array_values(array_unique($otsCrm));
                                foreach ($lineasFiltradas as $linea) {
                                    foreach ($otsCrm as $ot) {
                                        if ($linea['C_digo_de_OT_relacionada'] == $ot) {
                                            $otsCalculo[$ot]['navision'] = $linea['Navision_OT'];
                                            $otsCalculo[$ot]['dias'] = 0;
                                            $otsCalculo[$ot]['horas'] = 0;
                                            $otsCalculo[$ot]['minutos'] = 0;
                                            $otsCalculo[$ot]['totalMinutos'] = 0;
                                            $otsCalculo[$ot]['porcentaje'] = 0;
                                        }
                                    }
                                }
                                foreach ($lineasFiltradas as $linea) {
                                    foreach ($otsCrm as $ot) {
                                        if ($linea['C_digo_de_OT_relacionada'] == $ot) {
                                            $otsCalculo[$ot]['dias'] += (floatval($linea['D_as_actuaci_n']) * 24) * 60;
                                            $otsCalculo[$ot]['horas'] += floatval($linea['Horas_actuaci_n']) * 60;
                                            $otsCalculo[$ot]['minutos'] += floatval($linea['Minutos_actuaci_n']);
                                        }
                                    }
                                }
                            }

                            foreach ($otsCalculo as $ot => $vector) {
                                $otsCalculo[$ot]['totalMinutos'] =  $vector['dias'] + $vector['horas'] + $vector['minutos'];
                                $totalMinutos += $otsCalculo[$ot]['totalMinutos'];
                            }

                            foreach ($otsCalculo as $ot => $vector) {
                                $otsCalculo[$ot]['porcentaje'] =  ($vector['totalMinutos'] * 100) / $totalMinutos;
                            }

                            $response = $respuesta->ok($otsCalculo);
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

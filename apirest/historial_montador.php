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

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $postBody = file_get_contents("php://input");
        $body = json_decode($postBody, true);

        //Funcionalidad: Recoger 2 fechas y el montador o array de montadores para los filtros

        if (isset($body['montadores'])) {

            // Validar montadores
            $montadores = $body['montadores'];

            // Función auxiliar para verificar si montadores está vacío
            $montadoresVacios = false;
            if (is_string($montadores)) {
                $montadoresVacios = trim($montadores) === "";
            } elseif (is_array($montadores)) {
                // Eliminamos vacíos tras trim, si después no queda nada, están vacíos
                $montadoresFiltrados = array_filter(array_map('trim', $montadores));
                $montadoresVacios = empty($montadoresFiltrados);
            } elseif (is_null($montadores)) {
                $montadoresVacios = true;
            }
        }
        if (!isset($montadoresVacios)) {
            $montadoresVacios = true;
        }
        if ($montadoresVacios) {
            // Si montadores están vacíos, intentamos usar la cookie de usuario
            if (isset($_COOKIE['usuario']) && !empty($_COOKIE['usuario'])) {
                $usuario = $_COOKIE['usuario'];
            } else {
                $response = $respuesta->error_400('No se encuentra el identificador del montador');
                http_response_code(400);
                echo json_encode($response);
                die();
            }
        } else {
            // Si montadores no está vacío, trabajaremos con él directamente.
            $usuario = null;
        }
        if (!isset($body['fecha1']) || !isset($body['fecha2'])) {
            $fecha1 = date("Y-m-d", strtotime("-3 months")); // 3 meses atrás
            $fecha2 = date("Y-m-d"); //fecha actual
        } else {
            $fecha1 = $body['fecha1'];
            $fecha2 = $body['fecha2'];
        }
        $montadores = isset($body['montadores']) ? $body['montadores'] : $usuario;

        $json = '{ "callback": 
        {"url": "http://localhost:8080/callBackBulkCrm.php", "method": "post"}, 
        "query": {"module": {"api_name": "Products"},
        "criteria": 
        {"comparator": "between",
        "field": {"api_name": "Fecha_actuaci_n"}, 
        "value": [' . '"' . $fecha1 . '"' . ',' . '"' . $fecha2 . '"' . ']}}}';

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

                            $lineasFiltradas = [];

                            //FILTRO DE MONTADORES PARA LA BULK API
                            if ($montadores) {
                                if (is_array($montadores)) {
                                    foreach ($montadores as $montador) {
                                        foreach ($csvData as $linea) {
                                            if ($linea['montadorUsuarioApp'] == $montador) {
                                                array_push($lineasFiltradas, $linea);
                                            }
                                        }
                                    }
                                } else {
                                    foreach ($csvData as $linea) {
                                        if ($linea['montadorUsuarioApp'] == $montadores) {
                                            array_push($lineasFiltradas, $linea);
                                        }
                                    }
                                }
                            } else {
                                $response = $respuesta->error_400('Error no se ha enviado el id del montador');
                                http_response_code(500);
                                echo json_encode($response);
                                die();
                            }
                            if (count($lineasFiltradas)) {
                                $lineasOrdenadas = bubbleSortByKey($lineasFiltradas, "Fecha_actuaci_n");
                                if ($lineasOrdenadas) {
                                    $response = $respuesta->ok($lineasOrdenadas);
                                    http_response_code(200);
                                    echo json_encode($response);
                                } else {
                                    $response = $respuesta->error_500('Error al ordenar las líneas filtradas');
                                    http_response_code(500);
                                    echo json_encode($response);
                                    die();
                                }
                            } else {
                                $response = $respuesta->ok([]);
                                http_response_code(200);
                                echo json_encode($response);
                            }
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
function bubbleSortByKey($array, $key)
{
    $n = count($array);
    if ($n <= 1) return $array; // Si tiene 0 o 1 elemento, ya está ordenado.

    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - 1 - $i; $j++) {
            // Obtenemos los valores a comparar
            $valorActual = $array[$j][$key];
            $valorSiguiente = $array[$j + 1][$key];

            // Convertimos a timestamp si es una fecha (opcional, depende de tu criterio)
            // Si sabes que es fecha, descomenta las siguientes líneas:
            $valorActual = strtotime($valorActual);
            $valorSiguiente = strtotime($valorSiguiente);

            // Comparamos los valores
            if ($valorActual < $valorSiguiente) {
                // Intercambiamos los elementos
                $temp = $array[$j];
                $array[$j] = $array[$j + 1];
                $array[$j + 1] = $temp;
            }
        }
    }
    return $array;
}

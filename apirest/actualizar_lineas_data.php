<?php

// Habilitar la visualización de errores en PHP
ini_set("display_errors", 1); // Muestra errores en pantalla
ini_set("display_startup_errors", 1); // Muestra errores en el inicio de PHP
error_reporting(E_ALL); // Reporta todos los errores de PHP
// Configurar MySQLi para reportar errores y lanzar excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Origin: http://localhost/dosxdos_app');

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
        {"url": "http://localhost/dosxdos_app/callBackBulkCrm.php", "method": "post"}, 
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

                        $numLineas = count($csvData);

                        if ($numLineas) {
                            $i = 0;
                            foreach ($csvData as $linea) {
                                //CONSULTAR EL PUNTO DE VENTA
                                $idPv = $linea['Punto_de_venta'];
                                $nombrePv;
                                $codPv;
                                $lat;
                                $lng;
                                $camposPv = "Name,N,lat,lng";
                                if ($idPv) {
                                    $query = "SELECT $camposPv FROM Puntos_de_venta WHERE id = '$idPv'";
                                    // $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                                    // echo '<p id="' . $uniqueId . '" style="color:red">' . $query . '</p>';
                                    // echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                                    // @ob_flush();
                                    // flush();
                                    $crm->query($query);
                                    if ($crm->estado) {
                                        if ($crm->respuesta[1]) {
                                            $pvData = $crm->respuesta[1]['data'];
                                            $nombrePv = $pvData[0]['Name'];
                                            $codPv = $pvData[0]['N'];
                                            $lat = $pvData[0]['lat'];
                                            $lng = $pvData[0]['lng'];
                                            // $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                                            // echo '<p id="' . $uniqueId . '" style="color:red">' . $idPv . '</p>';
                                            // echo '<p style="color:red">' . $i . '__ PV de la línea ' . $linea['Codigo_de_l_nea'] . '</p>';
                                            // echo '<p style="color:blue">NombrePv: ' . $nombrePv . 'codPv: ' . $codPv . '</p>';
                                            // echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                                            // print_r($pvData);
                                            // @ob_flush();
                                            // flush();
                                        } else {
                                            $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                                            echo '<p style="color:red">' . $i . '__ Error, el punto de venta no existe en el CRM. Línea: ' . $linea['Codigo_de_l_nea'] . '</p>';
                                            var_dump($crm->respuestaError);
                                            echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                                            $i++;
                                            @ob_flush();
                                            flush();
                                            usleep(mt_rand(0, 1000000));
                                            continue;
                                        }
                                    } else {
                                        $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                                        echo '<p style="color:red">' . $i . '__ Error al consultar los datos del punto de venta de la línea: ' . $linea['Codigo_de_l_nea'] . '</p>';
                                        var_dump($crm->respuestaError);
                                        echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                                        @ob_flush();
                                        flush();
                                        $i++;
                                        usleep(mt_rand(0, 1000000));
                                        continue;
                                    }
                                } else {
                                    $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                                    echo '<p style="color:red">' . $i . '__ Error, no hay un punto de venta relacionado en la línea: ' . $linea['Codigo_de_l_nea'] . '</p>';
                                    var_dump($crm->respuestaError);
                                    echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                                    @ob_flush();
                                    flush();
                                    $i++;
                                    usleep(mt_rand(0, 1000000));
                                    continue;
                                }

                                if ($nombrePv || $codPv) {
                                    // ACTUALIZAR LÍNEA 
                                    $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                                    echo '<p id="' . $uniqueId . '" style="color:gray">Actualizando en el CRM la línea: ' . $linea['Codigo_de_l_nea'] . '...</p>';
                                    echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                                    @ob_flush();
                                    flush();
                                    $LineaVector = [];
                                    $LineaVector['data'][0]['id'] = $linea['Id'];
                                    $LineaVector['data'][0]['nombrePv'] = $nombrePv;
                                    $LineaVector['data'][0]['codPv'] = $codPv;
                                    $LineaVector['data'][0]['lat'] = $lat;
                                    $LineaVector['data'][0]['lng'] = $lng;
                                    $LineaJson = json_encode($LineaVector);
                                    $crm->actualizar("actualizarLinea", $LineaJson);
                                    if ($crm->estado) {
                                        $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                                        echo '<p id="' . $uniqueId . '" style="color:green">Línea actualizada en el CRM: ' . $linea['Codigo_de_l_nea'] . '</p>';
                                        @ob_flush();
                                        flush();
                                    } else {
                                        echo '<p id="' . $uniqueId . '" style="color:red">Error, no se ha actualizado la línea: ' . $linea['Codigo_de_l_nea'] . ' EN EL CRM</p>';
                                        var_dump($crm->respuestaError);
                                        continue;
                                    }
                                }

                                $i++;
                                usleep(mt_rand(0, 1000000));
                            }
                        } else {
                            $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                            echo '<p id="' . $uniqueId . '" style="color:red">No existen líneas que deban actualizar sus datos en el CRM</p>';
                            echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                            @ob_flush();
                            flush();
                            die();
                        }
                    } else {
                        $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                        echo '<p id="' . $uniqueId . '" style="color:red">Error al abrir el archivo comprimido de los datos del CRM</p>';
                        echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                        @ob_flush();
                        flush();
                        die();
                    }
                } else {
                    $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                    echo '<p id="' . $uniqueId . '" style="color:red">Error al descargar el archivo comprimido de los datos del CRM</p>';
                    echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                    @ob_flush();
                    flush();
                    die();
                }
            } else {
                $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
                echo '<p id="' . $uniqueId . '" style="color:red">Error al descargar el archivo comprimido de los datos del CRM</p>';
                var_dump($crmResponse);
                echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
                @ob_flush();
                flush();
                die();
            }
        } else {
            $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
            echo '<p id="' . $uniqueId . '" style="color:red">Error</p>';
            var_dump($crm->respuesta);
            echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
            @ob_flush();
            flush();
            die();
        }
    } else {
        var_dump($crm->respuestaError);
        @ob_flush();
        flush();
        die();
    }
} catch (\Throwable $th) {
    $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
    echo '<p id="' . $uniqueId . '" style="color:red">Error</p>';
    var_dump($th);
    echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
    @ob_flush();
    flush();
    die();
}

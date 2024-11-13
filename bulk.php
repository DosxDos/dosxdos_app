<?php
ini_set('curl.cainfo', '/dev/null');
set_time_limit(0);
ini_set('default_socket_timeout', 28800);
date_default_timezone_set('Atlantic/Canary');

require_once './apirest/clases/crm_clase.php';

try {
    //TODOS LOS DATOS DE UN MÓDULO
    $crm = new Crm;
    $vectorJson = [];
    $vectorJson['callback']['url'] = "https://dosxdos.app.iidos.com/callBackBulkCrm.php";
    $vectorJson['callback']['method'] = "post";
    $vectorJson['query']['module']['api_name'] = "Products";
    $vectorJson['file_type'] = "csv";
    $json = json_encode($vectorJson, JSON_FORCE_OBJECT);
    //echo $json;
    $response = $crm->agregar('bulkRead', $json);
    print_r ($response);
    if ($crm->estado) {
        if ($crm->respuesta[1]['data'][0]['status'] == "success") {
            echo '<p id="p1" style="font-size: 20px; font-weight: bold; color: green">Solicitud al CRM exitosa, esperando recibir respuesta de los datos solicitados...</p>';
            echo '<script>window.location.href = "#p1";</script>';
            @ob_flush();
            flush();
            $crmResponse;
            while (true) {
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
                sleep(1);
            }
            if (isset($crmResponse['state']) && $crmResponse['state'] == "COMPLETED") {
                echo '<p id="p4" style="font-size: 20px; font-weight: bold; color: green">Los datos comprimidos se han generado en el CRM, obteniendo los datos...</p>';
                echo '<script>window.location.href = "#p4";</script>';
                @ob_flush();
                flush();
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

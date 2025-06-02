<?php

try {
    // Configuraciones de PHP
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('curl.cainfo', '/dev/null');
    set_time_limit(0);
    ini_set('default_socket_timeout', 28800);
    date_default_timezone_set('Atlantic/Canary');

    // Módulos
    require_once 'middlewares/jwtMiddleware.php';
    require_once 'clases/crm_clase.php';
    require_once 'clases/respuestas_clase.php';

    $respuesta = new Respuestas;

    // Autenticación JWT
    $jwtMiddleware = new JwtMiddleware;
    $jwtMiddleware->verificar();

    // Leer el cuerpo de la solicitud (JSON)
    $json = file_get_contents("php://input");
    $input = json_decode($json, true);
    // Guardar el cuerpo recibido para análisis y pruebas
    $rutaArchivo = __DIR__ . '/datosActualizarCaso.json';
    file_put_contents($rutaArchivo, $json);

    // Validación del cuerpo y de los datos enviados en el cuerpo
    if (!is_array($input)) {
        $mensaje = 'No se ha enviado un JSON válido para esta solicitud';
        $respuesta = $respuesta->error_401($mensaje);
        http_response_code(401);
        echo json_encode($respuesta);
        // Guardar la respuesta para análisis y pruebas
        $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
        file_put_contents($rutaArchivo, json_encode($respuesta));
        die();
    }
    if (!isset($input['idLinea'])) {
        $mensaje = 'Cuerpo no válido, para esta solicitud se requiere el campo: idLinea';
        $respuesta = $respuesta->error_401($mensaje);
        http_response_code(401);
        echo json_encode($respuesta);
        // Guardar la respuesta para análisis y pruebas
        $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
        file_put_contents($rutaArchivo, json_encode($respuesta));
        die();
    }
    if (!isset($input['accion'])) {
        $mensaje = 'Cuerpo no válido, para esta solicitud se requieren el campo: accion';
        $respuesta = $respuesta->error_401($mensaje);
        http_response_code(401);
        echo json_encode($respuesta);
        // Guardar la respuesta para análisis y pruebas
        $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
        file_put_contents($rutaArchivo, json_encode($respuesta));
        die();
    }
    if (!isset($input['fechaIncidencia'])) {
        $mensaje = 'Cuerpo no válido, para esta solicitud se requieren el campo fechaIncidencia';
        $respuesta = $respuesta->error_401($mensaje);
        http_response_code(401);
        echo json_encode($respuesta);
        // Guardar la respuesta para análisis y pruebas
        $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
        file_put_contents($rutaArchivo, json_encode($respuesta));
        die();
    }

    // Datos del cuerpo
    $idLinea = $input['idLinea'];
    $accion = $input['accion'];
    $fechaIncidencia = $input['fechaIncidencia'];

    // Validación de los datos y ejecución de automatizaciones
    if ($fechaIncidencia != 'null' && $fechaIncidencia != '' && $fechaIncidencia != null) {
        if ($accion != 'Incidencias montador' && $accion != 'Incidencias' && $accion != 'Facturadas' && $accion != 'Perdidas') {
            $casoVector['data'][0]['Status'] =  "Resuelta";
            // Consultar casos asociados a la línea
            $crm = new Crm;
            $casos;
            $camposCasos = "Subject,Status,C_digo_de_l_nea,C_digo_de_OT,C_digo_punto_de_venta,Departamentos_afectados,Description,Fase_de_l_nea,Importe_de_la_l_nea,Importe,Incidencia_en_montaje,Incidencia_reportada_en_montaje,Reported_By,Montador,Account_Name,Product_Name,Deal_Name,Case_Number,Case_Origin,Priority,Punto_de_venta,Related_To,Solution,Type";
            $query = "SELECT $camposCasos FROM Cases WHERE Product_Name=$idLinea";
            $crm->query($query);
            if ($crm->estado) {
                if (isset($crm->respuesta[1]['data'])) {
                    $casos = $crm->respuesta[1]['data'];
                    $mensaje = [];
                    $mensaje['mensaje'] = '';
                    $validarError = false;
                    //print_r($casos);
                    foreach ($casos as $caso) {
                        if ($caso['Status'] != 'Resuelta') {
                            $idCaso = $caso['id'];
                            $casoVector['data'][0]['id'] = $idCaso;
                            if ($accion == 'Revisadas') {
                                $casoVector['data'][0]['Status'] =  "Resuelta";
                            } else {
                                $casoVector['data'][0]['Status'] =  "En proceso";
                            }
                            $casoJson = json_encode($casoVector);
                            $crm->actualizar("actualizarCaso", $casoJson);
                            if ($crm->estado) {
                                $mensaje['mensaje'] .= '_' . 'Caso actualizado correctamente: ' . $idCaso . '_';
                            } else {
                                $validarError = true;
                                $mensaje['mensaje'] = 'Error al actualizar el caso: ' . $idCaso;
                            }
                        } else {
                            $mensaje['mensaje'] .= '_' . 'El caso ya está resuelto: ' . $idCaso . '_';
                        }
                    }
                    if ($validarError) {
                        $mensaje['error'] = $validarError;
                        $respuesta = $respuesta->error_500($mensaje);
                        http_response_code(500);
                        echo json_encode($respuesta);
                        // Guardar la respuesta para análisis y pruebas
                        $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
                        file_put_contents($rutaArchivo, json_encode($respuesta));
                    } else {
                        $mensaje['error'] = $validarError;
                        $respuesta = $respuesta->ok($mensaje);
                        http_response_code(200);
                        echo json_encode($respuesta);
                        // Guardar la respuesta para análisis y pruebas
                        $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
                        file_put_contents($rutaArchivo, json_encode($respuesta));
                    }
                } else {
                    $mensaje = 'No se han encontrado casos asociados a la línea';
                    $respuesta = $respuesta->okF($mensaje);
                    http_response_code(200);
                    echo json_encode($respuesta);
                    // Guardar la respuesta para análisis y pruebas
                    $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
                    file_put_contents($rutaArchivo, json_encode($respuesta));
                    die();
                }
            } else {
                $mensaje = 'Error al consultar los casos asociados a la línea';
                $respuesta = $respuesta->error_500($mensaje);
                http_response_code(500);
                echo json_encode($respuesta);
                // Guardar la respuesta para análisis y pruebas
                $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
                file_put_contents($rutaArchivo, json_encode($respuesta));
                die();
            }
        } else {
            $mensaje = 'La línea no tiene una fase que determine una acción en sus casos relacionados';
            $respuesta = $respuesta->okF($mensaje);
            http_response_code(200);
            echo json_encode($respuesta);
            // Guardar la respuesta para análisis y pruebas
            $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
            file_put_contents($rutaArchivo, json_encode($respuesta));
        }
    } else {
        $mensaje = 'Línea sin fecha de incidencia';
        $respuesta = $respuesta->okF($mensaje);
        http_response_code(200);
        echo json_encode($respuesta);
        // Guardar la respuesta para análisis y pruebas
        $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
        file_put_contents($rutaArchivo, json_encode($respuesta));
    }
} catch (\Throwable $th) {
    print_r($th);
    // Convertir el error a texto para guardarlo como JSON
    $respuesta = json_encode([
        "mensaje" => $th->getMessage(),
        "archivo" => $th->getFile(),
        "linea" => $th->getLine(),
        "codigo" => $th->getCode(),
        "traza"  => $th->getTraceAsString()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    // Guardar la respuesta para análisis y pruebas
    $rutaArchivo = __DIR__ . '/respuestaActualizarCaso.json';
    file_put_contents($rutaArchivo, $respuesta);
}

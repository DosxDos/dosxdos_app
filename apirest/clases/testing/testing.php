<?php

require_once "respuestas_clase.php";
require_once "crm_clase.php";

try {
    $crm = new Crm;
    $camposLineas = "Codigo_de_l_nea,C_digo_de_OT_relacionada,montadorUsuarioApp,Navision_OT,Tipo_de_OT,D_as_actuaci_n,Horas_actuaci_n,Minutos_actuaci_n,Navision_L_nea,RutaSelect,Fecha_actuaci_n";
    $fecha1 = '2024-09-01';
    $fecha2 = '2024-09-15';

    $fechas = [];
    $fechaInicio = new DateTime($fecha1);
    $fechaFin = new DateTime($fecha2);

    // Incluir la fecha de finalización en el intervalo
    $fechaFin->modify('+1 day');

    $intervalo = new DateInterval('P1D'); // Intervalo de 1 día
    $periodo = new DatePeriod($fechaInicio, $intervalo, $fechaFin);

    foreach ($periodo as $fecha) {
        $fechas[] = $fecha->format('Y-m-d');
    }

    // Imprimir el array de fechas
    //print_r($fechas);

    $totalLineasData = [];
    $i = 0;

    foreach ($fechas as $fecha) {
        //echo '<p>' . $fecha . '</p>';
        $query = "SELECT $camposLineas FROM Products WHERE Fecha_actuaci_n = '$fecha'";
        $crm->query($query);
        if ($crm->estado) {
            if ($crm->respuesta[1]) {
                if (isset($crm->respuesta[1]['data'])) {
                    //print_r($crm->respuesta[1]['data']);
                    $lineasData = $crm->respuesta[1]['data'];
                    /*
                    echo '<p>' . count($lineasData) . '</p>';
                    @ob_flush();
                    flush();
                    */
                    foreach ($lineasData as $linea) {
                        $totalLineasData[$i] = $linea;
                        $i++;
                    }
                } else {
                    echo '<p>crm->respuesta[1][data]</p>';
                    print_r($crm->respuesta[1]['data']);
                    echo '<p>//</p>';
                    die();
                }
            } else {
                continue;
            }
        } else {
            $respuestas = new Respuestas;
            $answer = $respuestas->error_500($crm->respuestaError);
            print_r($answer);
            die();
        }
    }
    echo '<p>' . count($totalLineasData) . '</p>';
    print_r($totalLineasData);
} catch (\Throwable $th) {
    $respuestas = new Respuestas;
    $answer = $respuestas->error_500($th);
    print_r($answer);
}

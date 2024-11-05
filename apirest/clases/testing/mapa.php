<?php

require_once "respuestas_clase.php";
require_once "crm_clase.php";

try {
    $crm = new Crm;
    $camposLineas = "Product_Name,Codigo_de_l_nea,C_digo_de_OT_relacionada,Navision_OT,nombrePv,Direcci_n,rea,Sector,Zona,C_digo_postal";
    $query = "SELECT $camposLineas FROM Products WHERE Fase = 'TENERIFE_(C)'";
    $crm->query($query);
    if ($crm->estado) {
        if ($crm->respuesta[1]) {
            $lineasData = json_encode($crm->respuesta[1]['data']);
            echo $lineasData;
        } else {
            echo 'Error en la respuesta del objeto CRM';
        }
    } else {
        $respuestas = new Respuestas;
        $answer = $respuestas->error_500($crm->respuestaError);
        echo json_encode($answer);
    }
} catch (\Throwable $th) {
    $respuestas = new Respuestas;
    $answer = $respuestas->error_500($th);
    echo json_encode($answer);
}

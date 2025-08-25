<?php

require_once "conexion_clase.php";
require_once "respuestas_clase.php";
require_once "crm_clase.php";

class Ot extends Conexion
{
    public $respuesta = '';
    public $error = '';

    public function lineas($codOt)
    {
        try {
            $crm = new Crm;
            $camposLineas = "Product_Name,Codigo_de_l_nea,C_digo_de_OT_relacionada,Punto_de_venta,rea,Tipo_de_OT,Tipo_de_trabajo,Descripci_n_Tipo_Trabajo,Zona,Sector,Direcci_n,Nombre_de_Empresa,Fecha_actuaci_n,Fase,Motivo_de_incidencia,Observaciones_internas,Observaciones_montador,Horas_actuaci_n,D_as_actuaci_n,Minutos_actuaci_n,Firma_de_la_OT_relacionada,Estado_de_Actuaci_n,nombreCliente,nombreOt,nombrePv,codPv,Navision_OT,lat,lng,RutaSelect";
            $query = "SELECT $camposLineas FROM Products WHERE C_digo_de_OT_relacionada=\"$codOt\"";
            $crm->query($query);
            if ($crm->estado) {
                if ($crm->respuesta[1]) {
                    $lineasData = $crm->respuesta[1]['data'];
                    $respuestas = new Respuestas;
                    $answer = $respuestas->ok($lineasData);
                    $this->respuesta = $answer;
                } else {
                    $lineas = [];
                    $respuestas = new Respuestas;
                    $answer = $respuestas->ok($lineas);
                    $this->respuesta = $answer;
                }
            } else {
                $respuestas = new Respuestas;
                $answer = $respuestas->error_500($crm->respuestaError);
                $this->error = $answer;
                return;
            }
        } catch (\Throwable $th) {
            $respuestas = new Respuestas;
            $answer = $respuestas->error_500($th);
            $this->error = $answer;
            return;
        }
    }

    public function nombreOt($codOt)
    {
        try {
            $crm = new Crm;
            $camposLineas = "Deal_Name";
            $query = "SELECT $camposLineas FROM Deals WHERE C_digo=\"$codOt\"";
            $crm->query($query);
            if ($crm->estado) {
                if ($crm->respuesta[1]) {
                    $lineasData = $crm->respuesta[1]['data'];
                    $respuestas = new Respuestas;
                    $answer = $respuestas->ok($lineasData);
                    $this->respuesta = $answer;
                } else {
                    $lineas = [];
                    $respuestas = new Respuestas;
                    $answer = $respuestas->ok($lineas);
                    $this->respuesta = $answer;
                }
            } else {
                $respuestas = new Respuestas;
                $answer = $respuestas->error_500($crm->respuestaError);
                $this->error = $answer;
                return;
            }
        } catch (\Throwable $th) {
            $respuestas = new Respuestas;
            $answer = $respuestas->error_500($th);
            $this->error = $answer;
            return;
        }
    }
}

/*
$_lineas = new Lineas;
$navision = __DIR__.'\navision.json';
$json = file_get_contents($navision);
$_lineas->put($json);
var_dump($_lineas->respuesta);
var_dump($_lineas->error);
*/

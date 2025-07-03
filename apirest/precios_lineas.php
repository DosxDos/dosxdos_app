<?php

try {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('curl.cainfo', '/dev/null');
    set_time_limit(0);
    ini_set('default_socket_timeout', 28800);
    date_default_timezone_set('Atlantic/Canary');

    require_once 'middlewares/jwtMiddleware.php';
    require_once 'clases/crm_clase.php';
    require_once 'clases/a3Erp_clase.php';

    if (!isset($_GET['idOt']) || !isset($_GET['codOt']) || !isset($_GET['tipoOt']) || !isset($_GET['cliente']) || !isset($_GET['descuentoOt']) || !isset($_GET['descuentosAutomaticos']) || !isset($_GET['margenGanancia']) || !isset($_GET['tokenJwt'])) {
        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS</p>';
        scrollUpdate();
        @ob_flush();
        flush();
        die();
    }

    $jwtMiddleware = new JwtMiddleware;
    $jwtMiddleware->verificar();

    $idOt = $_GET['idOt'];
    $codOt = $_GET['codOt'];
    $tipoOt = $_GET['tipoOt'];
    $cliente = $_GET['cliente'];
    $descuentoOt;
    $descuentosAutomaticos = $_GET['descuentosAutomaticos'];
    if ($descuentosAutomaticos == "true") {
        $descuentosAutomaticos = true;
    } else {
        $descuentosAutomaticos = false;
    }

    if (!$_GET['descuentoOt']) {
        $descuentoOt = 0;
    } else {
        $descuentoOt = $_GET['descuentoOt'];
        $descuentoOt = (float) str_replace(',', '.', $descuentoOt);
        $descuentoOt = number_format($descuentoOt, 2, '.', '');
    }

    if (!$_GET['margenGanancia']) {
        $margenGanancia = 0;
        $margenGananciaValue = 0;
    } else {
        $margenGanancia = $_GET['margenGanancia'];
        $margenGanancia = (float) str_replace(',', '.', $margenGanancia);
        $margenGanancia = number_format($margenGanancia, 2, '.', '');
        $margenGananciaValue = 0;
    }

    if (!$idOt || !$codOt || !$tipoOt || !$cliente) {
        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HAN RECIBIDO LOS DATOS NECESARIOS</p>';
        scrollUpdate();
        @ob_flush();
        flush();
        die();
    }

    $crm = new Crm;
    $a3Erp = new a3Erp;
    $lineas;
    $a3ErpData = [];
    $lineasA3Erp = [];

    $numLineas = 0;
    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CÁLCULO PARA LA OT ' . $codOt . ' INICIADO...</p>';
    scrollUpdate();
    @ob_flush();
    flush();

    /* LINEAS */
    $camposLineas = "Product_Name,Codigo_de_l_nea,Punto_de_venta,Tipo_de_trabajo,Incluir,Ancho_medida,Alto_medida,Material,Acabados1,Impuesto_Cliente,Alto_total,Ancho_total,Poner,Unit_Price,Realizaci_n,Montaje";
    $query = "SELECT $camposLineas FROM Products WHERE OT_relacionada=$idOt";
    $crm->query($query);
    if ($crm->estado) {
        $lineas = $crm->respuesta[1]['data'];
        $lineas = ordenarArrayPorCampo($lineas, 'Codigo_de_l_nea');
        //print_r($lineas);
        $numLineas = count($lineas);
        echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO ' . $numLineas . ' LÍNEAS...</p>';
        scrollUpdate();
        @ob_flush();
        flush();
    } else {
        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR LAS LÍNEAS DE LA OT ' . $codOt . ' EN LA API DEL CRM</p>';
        print_r($crm->respuestaError);
        scrollUpdate();
        @ob_flush();
        flush();
        die();
    }
    $descPorcRealización;
    $descPorcMontaje;
    $impuesto;
    $nifCliente;
    $impuestoPorc;
    $impuestoCodA3;

    /* CLIENTE - DESCUENTOS E IMPUESTOS */
    $camposCliente = "Descuento_montaje,Descuento_realizaci_n,Grupo_registro_IVA_neg,CIF_NIF1";
    $query = "SELECT $camposCliente FROM Accounts WHERE Account_Name=\"$cliente\"";
    $crm->query($query);
    if ($crm->estado) {
        $descPorcRealización = $crm->respuesta[1]['data'][0]['Descuento_realizaci_n'];
        if (!$descPorcRealización) {
            $descPorcRealización = 0;
        }
        $descPorcMontaje = $crm->respuesta[1]['data'][0]['Descuento_montaje'];
        if (!$descPorcMontaje) {
            $descPorcMontaje = 0;
        }
        $impuesto = $crm->respuesta[1]['data'][0]['Grupo_registro_IVA_neg'];
        $nifCliente = $crm->respuesta[1]['data'][0]['CIF_NIF1'];
        if (!$nifCliente) {
            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!!, EL CLIENTE NO TIENE NIF EN EL CRM, POR FAVOR ACTUALIZA ESTA INFORMACIÓN, LA CUAL ES VITAL PARA LA SINCRONIZACIÓN CON A3ERP, NOTION, DOSXDOS.APP Y OTROS SOFTWARES FUTUROS</p>';
            scrollUpdate();
            @ob_flush();
            flush();
            die();
        }
        $camposImpuesto = "Porcentaje,codA3";
        $query = "SELECT $camposImpuesto FROM Impuestos WHERE Name=\"$impuesto\"";
        $crm->query($query);
        if ($crm->estado) {
            $impuestoPorc = $crm->respuesta[1]['data'][0]['Porcentaje'];
            $impuestoCodA3 = $crm->respuesta[1]['data'][0]['codA3'];
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">nifCliente: ' . $nifCliente . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descPorcRealización: ' . $descPorcRealización . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descPorcMontaje: ' . $descPorcMontaje . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentosAutomaticos: ' . $descuentosAutomaticos . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOt: ' . $descuentoOt . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGanancia: ' . $margenGanancia . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Impuesto ' . $impuesto . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">impuestoPorc: ' . $impuestoPorc . '</p>';
            scrollUpdate();
            @ob_flush();
            flush();
        } else {
            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR AL CONSULTAR LOS DATOS DEL IMPUESTO QUE TIENE ASOCIADO EL CLIENTE EN EL CRM!!!</p>';
            print_r($crm->respuestaError);
            scrollUpdate();
            @ob_flush();
            flush();
            die();
        }
    } else {
        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR AL CONSULTAR LOS DATOS DEL CLIENTE!!!</p>';
        print_r($crm->respuestaError);
        scrollUpdate();
        @ob_flush();
        flush();
        die();
    }

    /* MATERIALES Y SERVICIOS (IDS A3 ERP Y PRECIOS) */
    $materialesServicios = [];
    $crm->get("materialesServicios");
    if ($crm->estado) {
        echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">materialesServicios:</p>';
        $materialesServicios = $crm->respuesta[1]['data'];
        print_r($materialesServicios);
        scrollUpdate();
        @ob_flush();
        flush();
    } else {
        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR EL MÓDULO DE LOS MATERIALES Y LOS SERVICIOS EN EL CRM</p>';
        print_r($crm->respuestaError);
        scrollUpdate();
        @ob_flush();
        flush();
        die();
    }

    //DATETIME
    $fecha = date('Y-m-d\TH:i:s');
    //USO DEL CÓDIGO DE LA OT EN A3 ERP
    $referenciaA3Erp = $codOt;
    $centroCosteA3Erp = $codOt;
    //DESCUENTO MANUAL DE LA OT
    $totalDescOt = 0;
    //TOTALES
    $totalSinImpuesto = 0;
    $totalConImpuesto = 0;
    $totalPreciosLineas = 0;

    //INCLUSIÓN DE LÍNEAS
    $lineasNoIncluidas = [];
    $lineasIncluidas = [];

    // INFORMACIÓN INICIAL DE A3ERP Y VARIABLES
    $filterA3Erp = "NIF eq " . "'$nifCliente'";
    $endpointA3Erp = "cliente?externalFields=true&" . urlencode('$filter') . "=" . urlencode($filterA3Erp);
    $responseA3Erp = $a3Erp->get($endpointA3Erp);
    $clienteA3Erp;
    if ($responseA3Erp) {
        $clienteA3Erp = $responseA3Erp['PageData'][0];
        if ($clienteA3Erp) {
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">clienteA3Erp:</p>';
            print_r($clienteA3Erp);
            scrollUpdate();
            @ob_flush();
            flush();
        } else {
            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE ENCONTRÓ EL CLIENTE EN A3ERP, POR FAVOR VERIFICA QUE EL NIF DEL CLIENTE EN EL CRM CORRESPONDA A EL MISMO NIF DEL CLIENTE EN A3ERP</p>';
            scrollUpdate();
            @ob_flush();
            flush();
            die();
        }
    } else {
        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA RECIBIDO UNA RESPUESTA DE LA API DE A3ERP. POR FAVOR VERIFICA QUE EL NIF DEL CLIENTE EN EL CRM CORRESPONDA A EL MISMO NIF DEL CLIENTE EN A3ERP</p>';
        scrollUpdate();
        @ob_flush();
        flush();
        die();
    }

    //CABECERA DEL PEDIDO EN A3 ERP
    $codigoClienteA3Erp = $clienteA3Erp['Codigo'];
    $a3ErpData['Fecha'] = $fecha;
    $a3ErpData['Referencia'] = $referenciaA3Erp;
    $a3ErpData['CentroCoste'] = $centroCosteA3Erp;
    $a3ErpData['CodigoCliente'] = $codigoClienteA3Erp;
    $codigoArticuloA3Erp;
    $codigoArticuloAcabadoA3Erp;
    /* NOTAS */
    $observacionesA3Erp = '';

    if ($tipoOt == "VIS") {
        try {

            /* PRECIOS MONTAJE IMÁGENES */
            $preciosMontajeImagenes = [];
            $crm->get("preciosMontajeImagenes");
            if ($crm->estado) {
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">preciosMontajeImagenes:</p>';
                $preciosMontajeImagenes = $crm->respuesta[1]['data'];
                print_r($preciosMontajeImagenes);
                scrollUpdate();
                @ob_flush();
                flush();
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR EL MÓDULO DE PRECIOS DE MONTAJE DE LAS IMÁGENES EN EL CRM</p>';
                print_r($crm->respuestaError);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }

            /* PRECIOS LOGOS */
            $preciosLogos = [];
            $crm->get("preciosLogos");
            if ($crm->estado) {
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">preciosLogos:</p>';
                $preciosLogos = $crm->respuesta[1]['data'];
                print_r($preciosLogos);
                scrollUpdate();
                @ob_flush();
                flush();
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR EL MÓDULO DE PRECIOS DE LOS LOGOS EN EL CRM</p>';
                print_r($crm->respuestaError);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }

            /* PRECIOS MONTAJE LOGOS */
            $preciosLogosMontaje = [];
            $crm->get("preciosLogosMontaje");
            if ($crm->estado) {
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">preciosLogosMontaje:</p>';
                $preciosLogosMontaje = $crm->respuesta[1]['data'];
                print_r($preciosLogosMontaje);
                scrollUpdate();
                @ob_flush();
                flush();
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR EL MÓDULO DE PRECIOS DE MONTAJE DE LOGOS EN EL CRM</p>';
                print_r($crm->respuestaError);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }

            /* DESCUENTOS MONTAJE LOGOS */
            $descuentosLogosMontaje = [];
            $crm->get("descuentosLogosMontaje");
            if ($crm->estado) {
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentosLogosMontaje:</p>';
                $descuentosLogosMontaje = $crm->respuesta[1]['data'];
                print_r($descuentosLogosMontaje);
                scrollUpdate();
                @ob_flush();
                flush();
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR EL MÓDULO DE DESCUENTOS DE MONTAJE DE LOGOS EN EL CRM</p>';
                print_r($crm->respuestaError);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }

            // ESTABLECER DATOS CONSTANTES DE LOS MATERIALES Y SERVICIOS
            $precioTomaDeMedidas;
            $tomaDeMedidasIdA3Erp;
            $viniloIdA3Erp;
            $montajeLogoIdA3Erp;
            $montajeImagenIdA3Erp;
            $desmontajeLogoIdA3Erp;
            $desmontajeImagenIdA3Erp;
            foreach ($materialesServicios as $materialServicio) {
                if ($materialServicio['Material'] == "TOMA DE MEDIDAS") {
                    $precioTomaDeMedidas = $materialServicio['Precio'];
                    $tomaDeMedidasIdA3Erp = $materialServicio['idA3Erp'];
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioTomaDeMedidas: ' . $precioTomaDeMedidas . '</p>';
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">tomaDeMedidasIdA3Erp: ' . $tomaDeMedidasIdA3Erp . '</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
                if ($materialServicio['Material'] == "VINILO") {
                    $viniloIdA3Erp = $materialServicio['idA3Erp'];
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">viniloIdA3Erp: ' . $viniloIdA3Erp . '</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
                if ($materialServicio['Material'] == "MONTAJE LOGO") {
                    $montajeLogoIdA3Erp = $materialServicio['idA3Erp'];
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">montajeLogoIdA3Erp: ' . $montajeLogoIdA3Erp . '</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
                if ($materialServicio['Material'] == "MONTAJE IMAGEN") {
                    $montajeImagenIdA3Erp = $materialServicio['idA3Erp'];
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">montajeImagenIdA3Erp: ' . $montajeImagenIdA3Erp . '</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
                if ($materialServicio['Material'] == "DESMONTAJE LOGO") {
                    $desmontajeLogoIdA3Erp = $materialServicio['idA3Erp'];
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">desmontajeLogoIdA3Erp: ' . $desmontajeLogoIdA3Erp . '</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
                if ($materialServicio['Material'] == "DESMONTAJE IMAGEN") {
                    $desmontajeImagenIdA3Erp = $materialServicio['idA3Erp'];
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">desmontajeImagenIdA3Erp: ' . $desmontajeImagenIdA3Erp . '</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
            }

            /* ACABADOS */
            $acabadosData = [];
            $crm->get("acabados");
            if ($crm->estado) {
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">acabadosData:</p>';
                $acabadosData = $crm->respuesta[1]['data'];
                print_r($acabadosData);
                scrollUpdate();
                @ob_flush();
                flush();
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR EL MÓDULO DE LOS ACABADOS EN EL CRM</p>';
                print_r($crm->respuestaError);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }

            //VARIABLES PARA LA ITERACIÓN Y CÁLCULO DE LAS LÍNEAS
            //CUENTA DE TIPOS DE TRABAJOS (GLOBALES)
            $numTomasDeMedida = 0;
            $numVisuales = 0;
            $numLogos = 0;
            //CUENTA DE TIPOS DE TRABAJOS (NO GLOBALES)
            $numMontajeVisuales = 0;
            $numDesmontajeVisuales = 0;
            $numMontajeLogos = 0;
            $numDesmontajeLogos = 0;
            //M2
            $totalM2 = 0;
            //TOMA DE MEDIDAS
            $tomaDeMedidas = 0;
            //REALIZACIÓN VISUALES (IMÁGENES)
            $realizacion = 0;
            $totalDescRealizacion = 0;
            $totalRealizacion = 0;
            //MONTAJE VISUALES (IMÁGENES)
            $montaje = 0;
            $totalDescMontaje = 0;
            $totalMontaje = 0;
            //DESMONTAJE VISUALES (IMÁGENES)
            $desmontaje = 0;
            $totalDescDesmontaje = 0;
            $totalDesmontaje = 0;
            //PUNTOS DE VENTA DE LOS LOGOS
            $pvsLogos = [];
            //REALIZACIÓN DE LOGOS
            $logos = 0;
            //MONTAJE DE LOGOS
            $montajeLogos = 0;
            $totalDescMontajeLogos = 0;
            $totalMontajeLogos = 0;
            //DESMONTAJE DE LOGOS
            $desmontajeLogos = 0;
            $totalDescDesmontajeLogos = 0;
            $totalDesmontajeLogos = 0;
            //ACABADOS
            $acabados = 0;

            //BUCLE DE ITERACIÓN POR CADA LÍNEA DE LA OT
            foreach ($lineas as $linea) {
                $id = $linea['id'];
                $nombreDeLinea = $linea['Product_Name'];
                $codLinea = $linea['Codigo_de_l_nea'];
                $pv = $linea['Punto_de_venta']['id'];
                $incluir = $linea['Incluir'];
                $tipoTrabajo = $linea['Tipo_de_trabajo'];
                $ancho = floatval($linea['Ancho_medida']);
                $alto = floatval($linea['Alto_medida']);
                $anchoTotal = $linea['Ancho_total'];
                $altoTotal = $linea['Alto_total'];
                if ($anchoTotal && $altoTotal) {
                    $ancho = floatval($anchoTotal);
                    $alto = floatval($altoTotal);
                }
                $material = $linea['Material'];
                $acabado = $linea['Acabados1'];
                $poner = $linea['Poner'];
                $precioLinea = 0;
                $m2 = 0;
                $realizacionLinea = 0;
                if ($incluir) {
                    array_push($lineasIncluidas, $codLinea);

                    //ACCIÓN SEGÚN EL TIPO DE TRABAJO DE LA LÍNEA
                    switch ($tipoTrabajo) {

                        // TOMA DE MEDIDAS IN SITU
                        case 'Toma de medidas in situ':
                            $numTomasDeMedida++;
                            echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO LÍNEA ' . $codLinea . ' ...</p>';
                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Toma de medidas in situ</p>';
                            scrollUpdate();
                            @ob_flush();
                            flush();
                            $precioLinea = $precioTomaDeMedidas;
                            $precioLinea = number_format($precioLinea, 2, '.', '');
                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                            scrollUpdate();
                            @ob_flush();
                            flush();
                            $tomaDeMedidas = $tomaDeMedidas + $precioLinea;
                            // MARGEN DE GANANCIA
                            if ($margenGanancia) {
                                $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                            }
                            // DESCUENTO OT
                            if ($descuentoOt) {
                                $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                            }
                            $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                            /* ACTUALIZAR LÍNEA */
                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Actualizando en el CRM la línea: ' . $codLinea . '...</p>';
                            scrollUpdate();
                            @ob_flush();
                            flush();
                            $LineaVector = [];
                            $LineaVector['data'][0]['id'] = $id;
                            $LineaVector['data'][0]['Unit_Price'] = $precioLinea;
                            $LineaJson = json_encode($LineaVector);
                            $crm->actualizar("actualizarLinea", $LineaJson);
                            if ($crm->estado) {
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Línea actualizada en el CRM: ' . $codLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $codigoArticuloA3Erp = $tomaDeMedidasIdA3Erp;
                                //LÍNEA A3 ERP (TOMA DE MEDIDAS)
                                $lineaA3Erp = [];
                                $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                                $lineaA3Erp['Unidades'] = 1;
                                $lineaA3Erp['Precio'] = $precioLinea;
                                $lineaA3Erp['Texto'] = $codLinea . ' - ' . $nombreDeLinea;
                                array_push($lineasA3Erp, $lineaA3Erp);
                            } else {
                                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA ACTUALIZADO LA LÍNEA: ' . $codLinea . ' EN EL CRM</p>';
                                print_r($crm->respuestaError);
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                die();
                            }
                            break;

                        case 'Realizacion y montaje de logos':
                            if ($alto && $ancho) {
                                $numLogos++;
                                echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO LÍNEA ' . $codLinea . ' ...</p>';
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Realizacion y montaje de logos</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                array_push($pvsLogos, $pv);
                                $m2 = (($ancho / 100) * ($alto / 100));
                                $m2 = number_format($m2, 2, '.', '');
                                if ($m2 == 0.00) {
                                    $m2 = 0.01;
                                }
                                $totalM2 = $totalM2 + $m2;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">m2: ' . $m2 . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $nPv;
                                $areaPv;
                                $realizacionLineaLogo;
                                $montajeLineaLogo;
                                $porcDescuentoMontajeLogo = 0;
                                $descuentoMontajeLineaLogo;
                                $valorAcabado = 0;
                                $camposPv = "N,rea";
                                $query = "SELECT $camposPv FROM Puntos_de_venta WHERE id=\"$pv\"";
                                $crm->query($query);
                                if ($crm->estado) {
                                    $nPv = $crm->respuesta[1]['data'][0]['N'];
                                    $areaPv = $crm->respuesta[1]['data'][0]['rea'];
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">nPv: ' . $nPv . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">areaPv: ' . $areaPv . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    if (!$areaPv) {
                                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EL PUNTO DE VENTA ' . $nPv . ' NO TIENE EL CAMPO DE ÁREA DEFINIDO. POR FAVOR ACTUALIZA LOS DATOS DE UBICACIÓN DEL PUNTO DE VENTA, ACTUALIZA POR FAVOR TODOS LOS CAMPOS: ÁREA, SÉCTOR, ZONA, DIRECCIÓN</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        die();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR LOS DATOS DEL PUNTO DE VENTA CON ID ' . $pv . ' EN EL CRM!!!</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                //CÁLCULO DEL DESCUENTO EN EL MONTAJE DE LOGOS POR ISLAS Y CANTIDAD DE LOGOS EN PUNTO DE VENTA
                                $numPv = 0;
                                foreach ($pvsLogos as $pvLogo) {
                                    if ($pvLogo == $pv) {
                                        $numPv++;
                                    }
                                }
                                if ($descuentosAutomaticos) {
                                    foreach ($descuentosLogosMontaje as $descuento) {
                                        if (($numPv > ($descuento['PDVS_minimo'] - 1)) && ($areaPv == $descuento['Isla'])) {
                                            $porcDescuentoMontajeLogo = $descuento['Porcentaje'];
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">numPv: ' . $numPv . '</p>';
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">porcDescuentoMontajeLogo: ' . $porcDescuentoMontajeLogo . '%</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                        }
                                    }
                                }
                                /* REALIZACIÓN LOGO */
                                $validarPrecioLogo = false;
                                $precioDelLogo;
                                foreach ($preciosLogos as $precio) {
                                    if (($m2 >= $precio['Rango1']) && ($m2 <= $precio['Rango2'])) {
                                        $validarPrecioLogo = true;
                                        $precioDelLogo = $precio['Precio'];
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioDelLogo: ' . $precioDelLogo . '</p>';
                                        $codigoArticuloA3Erp = $viniloIdA3Erp;
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">codigoArticuloA3Erp: ' . $codigoArticuloA3Erp . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                    }
                                }
                                if ($validarPrecioLogo) {
                                    $realizacionLineaLogo = $precioDelLogo;
                                    $realizacionLineaLogo = number_format($realizacionLineaLogo, 2, '.', '');
                                    $logos = $logos + $realizacionLineaLogo;
                                    $precioLinea = $realizacionLineaLogo;
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">realizacionLineaLogo: ' . $realizacionLineaLogo . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    /* ACABADO */
                                    if ($acabado) {
                                        $validarAcabado = false;
                                        foreach ($acabadosData as $acabadoDataVector) {
                                            if ($acabadoDataVector['Acabado'] == $acabado) {
                                                $validarAcabado = true;
                                                $precioAcabado = $acabadoDataVector['Precio'];
                                                $codigoArticuloAcabadoA3Erp = $acabadoDataVector['idA3Erp'];
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">acabado: ' . $acabado . '</p>';
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioAcabado: ' . $precioAcabado . '</p>';
                                                scrollUpdate();
                                                @ob_flush();
                                                flush();
                                                $valorAcabado = $m2 * $precioAcabado;
                                                $valorAcabado = number_format($valorAcabado, 2, '.', '');
                                                $acabados = $acabados + $valorAcabado;
                                                $precioLinea = $precioLinea + $valorAcabado;
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">valorAcabado: ' . $valorAcabado . '</p>';
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                                scrollUpdate();
                                                @ob_flush();
                                                flush();
                                            }
                                        }
                                        if (!$validarAcabado) {
                                            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' SE LE HA ASIGNADO UN ACABADO QUE NO EXISTE EN EL MÓDULO DE ACABADOS EN EL CRM...</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                            die();
                                        }
                                    }
                                    /* MONTAJE LOGO */
                                    $validarPrecioMontajeLogo = false;
                                    $precioDelMontaje;
                                    foreach ($preciosLogosMontaje as $precio) {
                                        if (($m2 >= $precio['Rango1']) && ($m2 <= $precio['Rango2'])) {
                                            $validarPrecioMontajeLogo = true;
                                            $precioDelMontaje = $precio['Precio'];
                                        }
                                    }
                                    if ($validarPrecioMontajeLogo) {
                                        $montajeLogos = $montajeLogos + $precioDelMontaje;
                                        $descuentoMontajeLineaLogo = 0;
                                        $precioLinea = $precioLinea + $precioDelMontaje;
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioDelMontaje: ' . $precioDelMontaje . '</p>';
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        if ($porcDescuentoMontajeLogo) {
                                            $descuentoMontajeLineaLogo = ($precioDelMontaje * $porcDescuentoMontajeLogo) / 100;
                                            $descuentoMontajeLineaLogo = number_format($descuentoMontajeLineaLogo, 2, '.', '');
                                            $totalDescMontajeLogos = $totalDescMontajeLogos + $descuentoMontajeLineaLogo;
                                            $precioLinea = $precioLinea - $descuentoMontajeLineaLogo;
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoMontajeLineaLogo: ' . $descuentoMontajeLineaLogo . '</p>';
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                        }
                                    } else {
                                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LOS METROS CUADRADOS SON MAYORES O MENORES A LOS RANGOS ESTABLECIDOS EN LOS PRECIOS DE MONTAJE DE LOGOS, EN LA LÍNEA: ' . $codLinea . '</p>';
                                        print_r($crm->respuestaError);
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        die();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LOS METROS CUADRADOS SON MAYORES O MENORES A LOS RANGOS ESTABLECIDOS EN LOS PRECIOS DE LA REALIZACIÓN DE LOGOS, EN LA LÍNEA: ' . $codLinea . '</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                // MARGEN DE GANANCIA
                                if ($margenGanancia) {
                                    $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                    $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                    $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                // DESCUENTO OT
                                if ($descuentoOt) {
                                    $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                    $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                    $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                    $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                                /* ACTUALIZAR LÍNEA */
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Actualizando en el CRM la línea: ' . $codLinea . '...</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $LineaVector = [];
                                $LineaVector['data'][0]['id'] = $id;
                                $LineaVector['data'][0]['Metros_cuadrados'] = $m2;
                                $LineaVector['data'][0]['Realizaci_n'] = $realizacionLineaLogo;
                                $LineaVector['data'][0]['Porcentaje_Descuento_Realizaci_n'] = 0;
                                $LineaVector['data'][0]['Descuento_Realizaci_n'] = 0;
                                $LineaVector['data'][0]['Montaje'] = $precioDelMontaje;
                                $LineaVector['data'][0]['Porcentaje_Descuento_Montaje'] = $porcDescuentoMontajeLogo;
                                $LineaVector['data'][0]['Descuento_de_Montaje'] = $descuentoMontajeLineaLogo;
                                $LineaVector['data'][0]['Unit_Price'] = $precioLinea;
                                $LineaJson = json_encode($LineaVector);
                                $crm->actualizar("actualizarLinea", $LineaJson);
                                if ($crm->estado) {
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Línea actualizada en el CRM: ' . $codLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    // LÍNEA DE A3 ERP DE LA REALIZACIÓN Y EL MONTAJE (REALIZACIÓN Y MONTAJE DE LOGOS)
                                    $lineaA3Erp = [];
                                    $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                                    $lineaA3Erp['Unidades'] = $m2;
                                    $lineaA3Erp['Precio'] = ($precioLinea - $valorAcabado) / $m2;
                                    $lineaA3Erp['Param1'] = $m2;
                                    $lineaA3Erp['Param2'] = $realizacionLineaLogo;
                                    $lineaA3Erp['Param3'] = $precioDelMontaje;
                                    $lineaA3Erp['Param4'] = $poner;
                                    $lineaA3Erp['Texto'] = $codLinea . ' - ' . 'LOGO - ' . $nombreDeLinea;
                                    array_push($lineasA3Erp, $lineaA3Erp);
                                    // LÍNEA DE A3 ERP DEL ACABADO (REALIZACIÓN Y MONTAJE DE LOGOS)
                                    if ($acabado) {
                                        $lineaA3Erp = [];
                                        $lineaA3Erp['CodigoArticulo'] = $codigoArticuloAcabadoA3Erp;
                                        $lineaA3Erp['Unidades'] = $m2;
                                        $lineaA3Erp['Precio'] = $valorAcabado / $m2;
                                        $lineaA3Erp['Param1'] = $m2;
                                        $lineaA3Erp['Param4'] = $poner;
                                        $lineaA3Erp['Param5'] = $acabado;
                                        $lineaA3Erp['Texto'] = $codLinea . ' - ' . 'ACABADO - ' . 'LOGO - ' . $nombreDeLinea;
                                        array_push($lineasA3Erp, $lineaA3Erp);
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA ACTUALIZADO LA LÍNEA: ' . $codLinea . ' EN EL CRM</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                            } else {
                                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA DE LOGO ' . $codLinea . ' NO TIENE DEFINIDO EL ALTO O EL ANCHO</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                die();
                            }
                            break;

                        /* REALIZACIÓN Y MONTAJE DE IMÁGENES */
                        case 'Realizacion y montaje de imagenes':
                            if ($alto && $ancho) {
                                /* REALIZACIÓN */
                                $numVisuales++;
                                echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO LÍNEA ' . $codLinea . ' ...</p>';
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Realizacion y montaje de imagenes</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $m2 = (($ancho / 100) * ($alto / 100));
                                $m2 = number_format($m2, 2, '.', '');
                                if ($m2 == 0.00) {
                                    $m2 = 0.01;
                                }
                                $totalM2 = $totalM2 + $m2;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">m2: ' . $m2 . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                if ($material) {
                                    /* MATERIAL */
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">material: ' . $material . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    $precioMaterial;
                                    $validarMaterial = false;
                                    foreach ($materialesServicios as $materialServicio) {
                                        if ($materialServicio['Material'] == $material) {
                                            $validarMaterial = true;
                                            $precioMaterial = $materialServicio['Precio'];
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioMaterial: ' . $precioMaterial . '</p>';
                                            $codigoArticuloA3Erp = $materialServicio['idA3Erp'];
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">codigoArticuloA3Erp: ' . $codigoArticuloA3Erp . '</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                            $realizacionLinea = $m2 * $precioMaterial;
                                            $realizacionLinea = number_format($realizacionLinea, 2, '.', '');
                                            $realizacion = $realizacion + $realizacionLinea;
                                            $precioLinea = $realizacionLinea;
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">realizacionLinea: ' . $realizacionLinea . '</p>';
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                            if ($descPorcRealización && $descuentosAutomaticos) {
                                                $descuentoRealizacionLinea = ($realizacionLinea * $descPorcRealización) / 100;
                                                $descuentoRealizacionLinea = number_format($descuentoRealizacionLinea, 2, '.', '');
                                                $totalDescRealizacion = $totalDescRealizacion + $descuentoRealizacionLinea;
                                                $precioLinea = $precioLinea - $descuentoRealizacionLinea;
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoRealizacionLinea: ' . $descuentoRealizacionLinea . '</p>';
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                                scrollUpdate();
                                                @ob_flush();
                                                flush();
                                            }
                                        }
                                    }
                                    if (!$validarMaterial) {
                                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' SE LE HA ASIGNADO UN MATERIAL QUE NO EXISTE EN EL MÓDULO DE MATERIALES Y SERVICIOS EN EL CRM...</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        die();
                                    }
                                    $valorAcabado = 0;
                                    /* ACABADO */
                                    if ($acabado) {
                                        $validarAcabado = false;
                                        foreach ($acabadosData as $acabadoDataVector) {
                                            if ($acabadoDataVector['Acabado'] == $acabado) {
                                                $validarAcabado = true;
                                                $precioAcabado = $acabadoDataVector['Precio'];
                                                $codigoArticuloAcabadoA3Erp = $acabadoDataVector['idA3Erp'];
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">acabado: ' . $acabado . '</p>';
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioAcabado: ' . $precioAcabado . '</p>';
                                                scrollUpdate();
                                                @ob_flush();
                                                flush();
                                                $valorAcabado = $m2 * $precioAcabado;
                                                $valorAcabado = number_format($valorAcabado, 2, '.', '');
                                                $acabados = $acabados + $valorAcabado;
                                                $precioLinea = $precioLinea + $valorAcabado;
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">valorAcabado: ' . $valorAcabado . '</p>';
                                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                                scrollUpdate();
                                                @ob_flush();
                                                flush();
                                            }
                                        }
                                        if (!$validarAcabado) {
                                            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' SE LE HA ASIGNADO UN ACABADO QUE NO EXISTE EN EL MÓDULO DE ACABADOS EN EL CRM...</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                            die();
                                        }
                                    }
                                    /* MONTAJE */
                                    $validarPrecioMOntaje = false;
                                    $precioDelMontaje;
                                    foreach ($preciosMontajeImagenes as $precio) {
                                        if (($m2 >= $precio['Rango1']) && ($m2 <= $precio['Rango2'])) {
                                            $validarPrecioMOntaje = true;
                                            $precioDelMontaje = $precio['Precio'];
                                        }
                                    }
                                    if ($validarPrecioMOntaje) {
                                        $montaje = $montaje + $precioDelMontaje;
                                        $precioLinea = $precioLinea + $precioDelMontaje;
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioDelMontaje: ' . $precioDelMontaje . '</p>';
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        if ($descPorcMontaje && $descuentosAutomaticos) {
                                            $descuentoMontajeLinea = ($precioDelMontaje * $descPorcMontaje) / 100;
                                            $descuentoMontajeLinea = number_format($descuentoMontajeLinea, 2, '.', '');
                                            $totalDescMontaje = $totalDescMontaje + $descuentoMontajeLinea;
                                            $precioLinea = $precioLinea - $descuentoMontajeLinea;
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoMontajeLinea: ' . $descuentoMontajeLinea . '</p>';
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                        }
                                    } else {
                                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LOS METROS CUADRADOS SON MAYORES O MENORES A LOS RANGOS ESTABLECIDOS EN LOS PRECIOS DE MONTAJE DE IMÁGENES, EN LA LÍNEA: ' . $codLinea . '</p>';
                                        print_r($crm->respuestaError);
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        die();
                                    }

                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    // MARGEN DE GANANCIA
                                    if ($margenGanancia) {
                                        $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                        $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                        $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                        $precioLinea = number_format($precioLinea, 2, '.', '');
                                        echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                        echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                    }
                                    // DESCUENTO OT
                                    if ($descuentoOt) {
                                        $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                        $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                        $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                        $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                        $precioLinea = number_format($precioLinea, 2, '.', '');
                                        echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                        echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                    }
                                    $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                                    /* ACTUALIZAR LÍNEA */
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Actualizando en el CRM la línea: ' . $codLinea . '...</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    $LineaVector = [];
                                    $LineaVector['data'][0]['id'] = $id;
                                    $LineaVector['data'][0]['Metros_cuadrados'] = $m2;
                                    $LineaVector['data'][0]['Realizaci_n'] = $realizacionLinea;
                                    if ($descPorcRealización && $descuentosAutomaticos) {
                                        $LineaVector['data'][0]['Porcentaje_Descuento_Realizaci_n'] = $descPorcRealización;
                                        $LineaVector['data'][0]['Descuento_Realizaci_n'] = $descuentoRealizacionLinea;
                                    }
                                    $LineaVector['data'][0]['Montaje'] = $precioDelMontaje;
                                    if ($descPorcMontaje && $descuentosAutomaticos) {
                                        $LineaVector['data'][0]['Porcentaje_Descuento_Montaje'] = $descPorcMontaje;
                                        $LineaVector['data'][0]['Descuento_de_Montaje'] = $descuentoMontajeLinea;
                                    }
                                    $LineaVector['data'][0]['Unit_Price'] = $precioLinea;
                                    $LineaJson = json_encode($LineaVector);
                                    $crm->actualizar("actualizarLinea", $LineaJson);
                                    if ($crm->estado) {
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Línea actualizada en el CRM: ' . $codLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        // LÍNEA DE A3 ERP DE LA REALIZACIÓN Y EL MONTAJE (REALIZACIÓN Y MONTAJE DE IMÁGENES)
                                        $lineaA3Erp = [];
                                        $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                                        $lineaA3Erp['Unidades'] = $m2;
                                        $lineaA3Erp['Precio'] = ($precioLinea - $valorAcabado) / $m2;
                                        $lineaA3Erp['Param1'] = $m2;
                                        $lineaA3Erp['Param2'] = $realizacionLinea;
                                        $lineaA3Erp['Param3'] = $precioDelMontaje;
                                        $lineaA3Erp['Param4'] = $poner;
                                        $lineaA3Erp['Texto'] = $codLinea . ' - ' . $nombreDeLinea;
                                        array_push($lineasA3Erp, $lineaA3Erp);
                                        // LÍNEA DE A3 ERP DEL ACABADO (REALIZACIÓN Y MONTAJE DE IMÁGENES)
                                        if ($acabado) {
                                            $lineaA3Erp = [];
                                            $lineaA3Erp['CodigoArticulo'] = $codigoArticuloAcabadoA3Erp;
                                            $lineaA3Erp['Unidades'] = $m2;
                                            $lineaA3Erp['Precio'] = $valorAcabado / $m2;
                                            $lineaA3Erp['Param1'] = $m2;
                                            $lineaA3Erp['Param4'] = $poner;
                                            $lineaA3Erp['Param5'] = $acabado;
                                            $lineaA3Erp['Texto'] = $codLinea . ' - ' . 'ACABADO - ' . $nombreDeLinea;
                                            array_push($lineasA3Erp, $lineaA3Erp);
                                        }
                                    } else {
                                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA ACTUALIZADO LA LÍNEA: ' . $codLinea . ' EN EL CRM</p>';
                                        print_r($crm->respuestaError);
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        die();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' NO TIENE DEFINIDO EL MATERIAL</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                            } else {
                                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' NO TIENE DEFINIDO EL ALTO O EL ANCHO</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                die();
                            }
                            break;

                        /* MONTAJE DE LOGOS */
                        case 'Montaje de logos':
                            if ($alto && $ancho) {
                                $numLogos++;
                                $numMontajeLogos++;
                                echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO LÍNEA ' . $codLinea . ' ...</p>';
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Montaje de logos</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                array_push($pvsLogos, $pv);
                                $m2 = (($ancho / 100) * ($alto / 100));
                                $m2 = number_format($m2, 2, '.', '');
                                if ($m2 == 0.00) {
                                    $m2 = 0.01;
                                }
                                $totalM2 = $totalM2 + $m2;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">m2: ' . $m2 . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $nPv;
                                $areaPv;
                                $montajeLineaLogo;
                                $porcDescuentoMontajeLogo = 0;
                                $descuentoMontajeLineaLogo;
                                $camposPv = "N,rea";
                                $query = "SELECT $camposPv FROM Puntos_de_venta WHERE id=\"$pv\"";
                                $crm->query($query);
                                if ($crm->estado) {
                                    $nPv = $crm->respuesta[1]['data'][0]['N'];
                                    $areaPv = $crm->respuesta[1]['data'][0]['rea'];
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">nPv: ' . $nPv . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">areaPv: ' . $areaPv . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    if (!$areaPv) {
                                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EL PUNTO DE VENTA ' . $nPv . ' NO TIENE EL CAMPO DE ÁREA DEFINIDO. POR FAVOR ACTUALIZA LOS DATOS DE UBICACIÓN DEL PUNTO DE VENTA, ACTUALIZA POR FAVOR TODOS LOS CAMPOS: ÁREA, SÉCTOR, ZONA, DIRECCIÓN</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        die();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR LOS DATOS DEL PUNTO DE VENTA CON ID ' . $pv . ' EN EL CRM!!!</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                //CÁLCULO DEL DESCUENTO EN EL MONTAJE DE LOGOS POR ISLAS Y CANTIDAD DE LOGOS EN PUNTO DE VENTA
                                $numPv = 0;
                                foreach ($pvsLogos as $pvLogo) {
                                    if ($pvLogo == $pv) {
                                        $numPv++;
                                    }
                                }
                                if ($descuentosAutomaticos) {
                                    foreach ($descuentosLogosMontaje as $descuento) {
                                        if (($numPv > ($descuento['PDVS_minimo'] - 1)) && ($areaPv == $descuento['Isla'])) {
                                            $porcDescuentoMontajeLogo = $descuento['Porcentaje'];
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">numPv: ' . $numPv . '</p>';
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">porcDescuentoMontajeLogo: ' . $porcDescuentoMontajeLogo . '%</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                        }
                                    }
                                }
                                $codigoArticuloA3Erp = $montajeLogoIdA3Erp;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">codigoArticuloA3Erp: ' . $codigoArticuloA3Erp . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                /* MONTAJE LOGO */
                                $validarPrecioMontajeLogo = false;
                                $precioDelMontaje;
                                foreach ($preciosLogosMontaje as $precio) {
                                    if (($m2 >= $precio['Rango1']) && ($m2 <= $precio['Rango2'])) {
                                        $validarPrecioMontajeLogo = true;
                                        $precioDelMontaje = $precio['Precio'];
                                    }
                                }
                                if ($validarPrecioMontajeLogo) {
                                    $montajeLogos = $montajeLogos + $precioDelMontaje;
                                    $descuentoMontajeLineaLogo = 0;
                                    $precioLinea = $precioLinea + $precioDelMontaje;
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioDelMontaje: ' . $precioDelMontaje . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    if ($porcDescuentoMontajeLogo) {
                                        $descuentoMontajeLineaLogo = ($precioDelMontaje * $porcDescuentoMontajeLogo) / 100;
                                        $descuentoMontajeLineaLogo = number_format($descuentoMontajeLineaLogo, 2, '.', '');
                                        $totalDescMontajeLogos = $totalDescMontajeLogos + $descuentoMontajeLineaLogo;
                                        $precioLinea = $precioLinea - $descuentoMontajeLineaLogo;
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoMontajeLineaLogo: ' . $descuentoMontajeLineaLogo . '</p>';
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LOS METROS CUADRADOS SON MAYORES O MENORES A LOS RANGOS ESTABLECIDOS EN LOS PRECIOS DE MONTAJE DE LOGOS, EN LA LÍNEA: ' . $codLinea . '</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                // MARGEN DE GANANCIA
                                if ($margenGanancia) {
                                    $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                    $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                    $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                // DESCUENTO OT
                                if ($descuentoOt) {
                                    $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                    $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                    $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                    $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                                /* ACTUALIZAR LÍNEA */
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Actualizando en el CRM la línea: ' . $codLinea . '...</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $LineaVector = [];
                                $LineaVector['data'][0]['id'] = $id;
                                $LineaVector['data'][0]['Metros_cuadrados'] = $m2;
                                $LineaVector['data'][0]['Montaje'] = $precioDelMontaje;
                                $LineaVector['data'][0]['Porcentaje_Descuento_Montaje'] = $porcDescuentoMontajeLogo;
                                $LineaVector['data'][0]['Descuento_de_Montaje'] = $descuentoMontajeLineaLogo;
                                $LineaVector['data'][0]['Unit_Price'] = $precioLinea;
                                $LineaJson = json_encode($LineaVector);
                                $crm->actualizar("actualizarLinea", $LineaJson);
                                if ($crm->estado) {
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Línea actualizada en el CRM: ' . $codLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    //LÍNEA DE A3 ERP DEL MONTAJE (MONTAJE DE LOGOS)
                                    $lineaA3Erp = [];
                                    $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                                    $lineaA3Erp['Unidades'] = 1;
                                    $lineaA3Erp['Precio'] = $precioLinea;
                                    $lineaA3Erp['Param1'] = $m2;
                                    $lineaA3Erp['Param3'] = $precioDelMontaje;
                                    $lineaA3Erp['Param4'] = $poner;
                                    $lineaA3Erp['Texto'] = $codLinea . ' - ' . $nombreDeLinea;
                                    array_push($lineasA3Erp, $lineaA3Erp);
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA ACTUALIZADO LA LÍNEA: ' . $codLinea . ' EN EL CRM</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                            } else {
                                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA DE LOGO ' . $codLinea . ' NO TIENE DEFINIDO EL ALTO O EL ANCHO</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                die();
                            }
                            break;

                        /* MONTAJE DE IMAGENES */
                        case 'Montaje de imagenes':
                            if ($alto && $ancho) {
                                $numVisuales++;
                                $numMontajeVisuales++;
                                echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO LÍNEA ' . $codLinea . ' ...</p>';
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Montaje de imagenes</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $m2 = (($ancho / 100) * ($alto / 100));
                                $m2 = number_format($m2, 2, '.', '');
                                if ($m2 == 0.00) {
                                    $m2 = 0.01;
                                }
                                $totalM2 = $totalM2 + $m2;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">m2: ' . $m2 . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $codigoArticuloA3Erp = $montajeImagenIdA3Erp;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">codigoArticuloA3Erp: ' . $codigoArticuloA3Erp . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                /* MONTAJE */
                                $validarPrecioMOntaje = false;
                                $precioDelMontaje;
                                foreach ($preciosMontajeImagenes as $precio) {
                                    if (($m2 >= $precio['Rango1']) && ($m2 <= $precio['Rango2'])) {
                                        $validarPrecioMOntaje = true;
                                        $precioDelMontaje = $precio['Precio'];
                                    }
                                }
                                if ($validarPrecioMOntaje) {
                                    $montaje = $montaje + $precioDelMontaje;
                                    $precioLinea = $precioLinea + $precioDelMontaje;
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioDelMontaje: ' . $precioDelMontaje . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    if ($descPorcMontaje && $descuentosAutomaticos) {
                                        $descuentoMontajeLinea = ($precioDelMontaje * $descPorcMontaje) / 100;
                                        $descuentoMontajeLinea = number_format($descuentoMontajeLinea, 2, '.', '');
                                        $totalDescMontaje = $totalDescMontaje + $descuentoMontajeLinea;
                                        $precioLinea = $precioLinea - $descuentoMontajeLinea;
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoMontajeLinea: ' . $descuentoMontajeLinea . '</p>';
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LOS METROS CUADRADOS SON MAYORES O MENORES A LOS RANGOS ESTABLECIDOS EN LOS PRECIOS DE MONTAJE DE IMÁGENES, EN LA LÍNEA: ' . $codLinea . '</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                // MARGEN DE GANANCIA
                                if ($margenGanancia) {
                                    $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                    $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                    $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                // DESCUENTO OT
                                if ($descuentoOt) {
                                    $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                    $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                    $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                    $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                                /* ACTUALIZAR LÍNEA */
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Actualizando en el CRM la línea: ' . $codLinea . '...</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $LineaVector = [];
                                $LineaVector['data'][0]['id'] = $id;
                                $LineaVector['data'][0]['Metros_cuadrados'] = $m2;
                                $LineaVector['data'][0]['Montaje'] = $precioDelMontaje;
                                if ($descPorcMontaje && $descuentosAutomaticos) {
                                    $LineaVector['data'][0]['Porcentaje_Descuento_Montaje'] = $descPorcMontaje;
                                    $LineaVector['data'][0]['Descuento_de_Montaje'] = $descuentoMontajeLinea;
                                }
                                $LineaVector['data'][0]['Unit_Price'] = $precioLinea;
                                $LineaJson = json_encode($LineaVector);
                                $crm->actualizar("actualizarLinea", $LineaJson);
                                if ($crm->estado) {
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Línea actualizada en el CRM: ' . $codLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    //LÍNEA DE A3 ERP DEL MONTAJE (MONTAJE DE IMÁGENES)
                                    $lineaA3Erp = [];
                                    $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                                    $lineaA3Erp['Unidades'] = 1;
                                    $lineaA3Erp['Precio'] = $precioLinea;
                                    $lineaA3Erp['Param1'] = $m2;
                                    $lineaA3Erp['Param3'] = $precioDelMontaje;
                                    $lineaA3Erp['Param4'] = $poner;
                                    $lineaA3Erp['Texto'] = $codLinea . ' - ' . $nombreDeLinea;
                                    array_push($lineasA3Erp, $lineaA3Erp);
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA ACTUALIZADO LA LÍNEA: ' . $codLinea . ' EN EL CRM</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                            } else {
                                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' NO TIENE DEFINIDO EL ALTO O EL ANCHO</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                die();
                            }
                            break;

                        case 'Desmontaje de logos':
                            if ($alto && $ancho) {
                                $numLogos++;
                                $numDesmontajeLogos++;
                                echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO LÍNEA ' . $codLinea . ' ...</p>';
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Desmontaje de logos</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                array_push($pvsLogos, $pv);
                                $m2 = (($ancho / 100) * ($alto / 100));
                                $m2 = number_format($m2, 2, '.', '');
                                if ($m2 == 0.00) {
                                    $m2 = 0.01;
                                }
                                $totalM2 = $totalM2 + $m2;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">m2: ' . $m2 . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $nPv;
                                $areaPv;
                                $montajeLineaLogo;
                                $porcDescuentoMontajeLogo = 0;
                                $descuentoMontajeLineaLogo;
                                $camposPv = "N,rea";
                                $query = "SELECT $camposPv FROM Puntos_de_venta WHERE id=\"$pv\"";
                                $crm->query($query);
                                if ($crm->estado) {
                                    $nPv = $crm->respuesta[1]['data'][0]['N'];
                                    $areaPv = $crm->respuesta[1]['data'][0]['rea'];
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">nPv: ' . $nPv . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">areaPv: ' . $areaPv . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    if (!$areaPv) {
                                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EL PUNTO DE VENTA ' . $nPv . ' NO TIENE EL CAMPO DE ÁREA DEFINIDO. POR FAVOR ACTUALIZA LOS DATOS DE UBICACIÓN DEL PUNTO DE VENTA, ACTUALIZA POR FAVOR TODOS LOS CAMPOS: ÁREA, SÉCTOR, ZONA, DIRECCIÓN</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                        die();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CONSULTAR LOS DATOS DEL PUNTO DE VENTA CON ID ' . $pv . ' EN EL CRM!!!</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                //CÁLCULO DEL DESCUENTO EN EL MONTAJE DE LOGOS POR ISLAS Y CANTIDAD DE LOGOS EN PUNTO DE VENTA
                                $numPv = 0;
                                foreach ($pvsLogos as $pvLogo) {
                                    if ($pvLogo == $pv) {
                                        $numPv++;
                                    }
                                }
                                if ($descuentosAutomaticos) {
                                    foreach ($descuentosLogosMontaje as $descuento) {
                                        if (($numPv > ($descuento['PDVS_minimo'] - 1)) && ($areaPv == $descuento['Isla'])) {
                                            $porcDescuentoMontajeLogo = $descuento['Porcentaje'];
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">numPv: ' . $numPv . '</p>';
                                            echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">porcDescuentoMontajeLogo: ' . $porcDescuentoMontajeLogo . '%</p>';
                                            scrollUpdate();
                                            @ob_flush();
                                            flush();
                                        }
                                    }
                                }
                                $codigoArticuloA3Erp = $desmontajeLogoIdA3Erp;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">codigoArticuloA3Erp: ' . $codigoArticuloA3Erp . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                /* MONTAJE LOGO */
                                $validarPrecioMontajeLogo = false;
                                $precioDelMontaje;
                                foreach ($preciosLogosMontaje as $precio) {
                                    if (($m2 >= $precio['Rango1']) && ($m2 <= $precio['Rango2'])) {
                                        $validarPrecioMontajeLogo = true;
                                        $precioDelMontaje = $precio['Precio'];
                                    }
                                }
                                if ($validarPrecioMontajeLogo) {
                                    $desmontajeLogos = $desmontajeLogos + $precioDelMontaje;
                                    $descuentoMontajeLineaLogo = 0;
                                    $precioLinea = $precioLinea + $precioDelMontaje;
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioDelMontaje: ' . $precioDelMontaje . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    if ($porcDescuentoMontajeLogo) {
                                        $descuentoMontajeLineaLogo = ($precioDelMontaje * $porcDescuentoMontajeLogo) / 100;
                                        $descuentoMontajeLineaLogo = number_format($descuentoMontajeLineaLogo, 2, '.', '');
                                        $totalDescDesmontajeLogos = $totalDescDesmontajeLogos + $descuentoMontajeLineaLogo;
                                        $precioLinea = $precioLinea - $descuentoMontajeLineaLogo;
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoMontajeLineaLogo: ' . $descuentoMontajeLineaLogo . '</p>';
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LOS METROS CUADRADOS SON MAYORES O MENORES A LOS RANGOS ESTABLECIDOS EN LOS PRECIOS DE MONTAJE DE LOGOS, EN LA LÍNEA: ' . $codLinea . '</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                // MARGEN DE GANANCIA
                                if ($margenGanancia) {
                                    $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                    $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                    $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                // DESCUENTO OT
                                if ($descuentoOt) {
                                    $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                    $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                    $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                    $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                                /* ACTUALIZAR LÍNEA */
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Actualizando en el CRM la línea: ' . $codLinea . '...</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $LineaVector = [];
                                $LineaVector['data'][0]['id'] = $id;
                                $LineaVector['data'][0]['Metros_cuadrados'] = $m2;
                                $LineaVector['data'][0]['Montaje'] = $precioDelMontaje;
                                $LineaVector['data'][0]['Porcentaje_Descuento_Montaje'] = $porcDescuentoMontajeLogo;
                                $LineaVector['data'][0]['Descuento_de_Montaje'] = $descuentoMontajeLineaLogo;
                                $LineaVector['data'][0]['Unit_Price'] = $precioLinea;
                                $LineaJson = json_encode($LineaVector);
                                $crm->actualizar("actualizarLinea", $LineaJson);
                                if ($crm->estado) {
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Línea actualizada en el CRM: ' . $codLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    //LÍNEA DE A3 ERP DEL MONTAJE (MONTAJE DE LOGOS)
                                    $lineaA3Erp = [];
                                    $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                                    $lineaA3Erp['Unidades'] = 1;
                                    $lineaA3Erp['Precio'] = $precioLinea;
                                    $lineaA3Erp['Param1'] = $m2;
                                    $lineaA3Erp['Param3'] = $precioDelMontaje;
                                    $lineaA3Erp['Param4'] = $poner;
                                    $lineaA3Erp['Texto'] = $codLinea . ' - ' . $nombreDeLinea;
                                    array_push($lineasA3Erp, $lineaA3Erp);
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA ACTUALIZADO LA LÍNEA: ' . $codLinea . ' EN EL CRM</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                            } else {
                                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA DE LOGO ' . $codLinea . ' NO TIENE DEFINIDO EL ALTO O EL ANCHO</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                die();
                            }
                            break;

                        case 'Desmontaje de imagenes':
                            if ($alto && $ancho) {
                                $numVisuales++;
                                $numDesmontajeVisuales++;
                                echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CALCULANDO LÍNEA ' . $codLinea . ' ...</p>';
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Desmontaje de imagenes</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $m2 = (($ancho / 100) * ($alto / 100));
                                $m2 = number_format($m2, 2, '.', '');
                                if ($m2 == 0.00) {
                                    $m2 = 0.01;
                                }
                                $totalM2 = $totalM2 + $m2;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">m2: ' . $m2 . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $codigoArticuloA3Erp = $desmontajeImagenIdA3Erp;
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">codigoArticuloA3Erp: ' . $codigoArticuloA3Erp . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                /* MONTAJE */
                                $validarPrecioMOntaje = false;
                                $precioDelMontaje;
                                foreach ($preciosMontajeImagenes as $precio) {
                                    if (($m2 >= $precio['Rango1']) && ($m2 <= $precio['Rango2'])) {
                                        $validarPrecioMOntaje = true;
                                        $precioDelMontaje = $precio['Precio'];
                                    }
                                }
                                if ($validarPrecioMOntaje) {
                                    $desmontaje = $desmontaje + $precioDelMontaje;
                                    $precioLinea = $precioLinea + $precioDelMontaje;
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioDelMontaje: ' . $precioDelMontaje . '</p>';
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    if ($descPorcMontaje && $descuentosAutomaticos) {
                                        $descuentoMontajeLinea = ($precioDelMontaje * $descPorcMontaje) / 100;
                                        $descuentoMontajeLinea = number_format($descuentoMontajeLinea, 2, '.', '');
                                        $totalDescDesmontaje = $totalDescDesmontaje + $descuentoMontajeLinea;
                                        $precioLinea = $precioLinea - $descuentoMontajeLinea;
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoMontajeLinea: ' . $descuentoMontajeLinea . '</p>';
                                        echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                        scrollUpdate();
                                        @ob_flush();
                                        flush();
                                    }
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LOS METROS CUADRADOS SON MAYORES O MENORES A LOS RANGOS ESTABLECIDOS EN LOS PRECIOS DE MONTAJE DE IMÁGENES, EN LA LÍNEA: ' . $codLinea . '</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                // MARGEN DE GANANCIA
                                if ($margenGanancia) {
                                    $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                    $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                    $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                // DESCUENTO OT
                                if ($descuentoOt) {
                                    $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                    $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                    $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                    $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                    $precioLinea = number_format($precioLinea, 2, '.', '');
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                                /* ACTUALIZAR LÍNEA */
                                echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Actualizando en el CRM la línea: ' . $codLinea . '...</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                $LineaVector = [];
                                $LineaVector['data'][0]['id'] = $id;
                                $LineaVector['data'][0]['Metros_cuadrados'] = $m2;
                                $LineaVector['data'][0]['Montaje'] = $precioDelMontaje;
                                if ($descPorcMontaje && $descuentosAutomaticos) {
                                    $LineaVector['data'][0]['Porcentaje_Descuento_Montaje'] = $descPorcMontaje;
                                    $LineaVector['data'][0]['Descuento_de_Montaje'] = $descuentoMontajeLinea;
                                }
                                $LineaVector['data'][0]['Unit_Price'] = $precioLinea;
                                $LineaJson = json_encode($LineaVector);
                                $crm->actualizar("actualizarLinea", $LineaJson);
                                if ($crm->estado) {
                                    echo '<p style="color:gray;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Línea actualizada en el CRM: ' . $codLinea . '</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    //LÍNEA DE A3 ERP DEL MONTAJE (MONTAJE DE IMÁGENES)
                                    $lineaA3Erp = [];
                                    $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                                    $lineaA3Erp['Unidades'] = 1;
                                    $lineaA3Erp['Precio'] = $precioLinea;
                                    $lineaA3Erp['Param1'] = $m2;
                                    $lineaA3Erp['Param3'] = $precioDelMontaje;
                                    $lineaA3Erp['Param4'] = $poner;
                                    $lineaA3Erp['Texto'] = $codLinea . ' - ' . $nombreDeLinea;
                                    array_push($lineasA3Erp, $lineaA3Erp);
                                } else {
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA ACTUALIZADO LA LÍNEA: ' . $codLinea . ' EN EL CRM</p>';
                                    print_r($crm->respuestaError);
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                    die();
                                }
                            } else {
                                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' NO TIENE DEFINIDO EL ALTO O EL ANCHO</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                                die();
                            }
                            break;

                        default:
                            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! LA LÍNEA ' . $codLinea . ' TIENE UN TIPO DE TRABAJO QUE NO PERTENECE A LAS OT DE VISUALES, POR FAVOR CORRIGE EL TIPO DE TRABAJO.</p>';
                            scrollUpdate();
                            @ob_flush();
                            flush();
                            die();
                    }
                } else {
                    array_push($lineasNoIncluidas, $codLinea);
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LÍNEA ' . $codLinea . ' NO ESTÁ INCLUIDA EN EL CÁLCULO...</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
            }

            /* RESULTADOS */
            echo '<h1 style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">RESULTADOS OT ' . $codOt . '</h1>';
            if (count($lineasNoIncluidas)) {
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LÍNEAS NO INCLUIDAS:</p>';
                print_r($lineasNoIncluidas);
            }
            echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LÍNEAS INCLUIDAS:</p>';
            print_r($lineasIncluidas);
            /* TOTALES */
            $totalRealizacion = $realizacion - $totalDescRealizacion;
            $totalMontaje = $montaje - $totalDescMontaje;
            $totalMontajeLogos = $montajeLogos - $totalDescMontajeLogos;
            $totalDesmontaje = $desmontaje - $totalDescDesmontaje;
            $totalDesmontajeLogos = $desmontajeLogos - $totalDescDesmontajeLogos;
            $totalSinImpuesto = $totalRealizacion + $totalMontaje + $acabados + $logos + $totalMontajeLogos + $tomaDeMedidas + $totalDesmontaje + $totalDesmontajeLogos + $margenGananciaValue;
            //DESCUENTO MANUAL DE LA OT
            $descuentoOtValue = ($totalSinImpuesto * $descuentoOt) / 100;
            $totalSinImpuestoConDescuentoOt = $totalSinImpuesto - $descuentoOtValue;
            //APLICACIÓN DEL IMPUESTO
            $impuestoAplicado = 0;
            $totalConImpuesto = 0;
            if ($descuentoOt) {
                $impuestoAplicado = ($totalSinImpuestoConDescuentoOt * $impuestoPorc) / 100;
                $totalConImpuesto = $totalSinImpuestoConDescuentoOt + $impuestoAplicado;
            } else {
                $impuestoAplicado = ($totalSinImpuesto * $impuestoPorc) / 100;
                $totalConImpuesto = $totalSinImpuesto + $impuestoAplicado;
            }
            /* ACTUALIZAR IMPORTE DE LA OT */
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalSinImpuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalSinImpuestoConDescuentoOt: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€</p>';
            echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ACTUALIZANDO EL IMPORTE DE LA OT EN EL CRM...</p>';
            scrollUpdate();
            @ob_flush();
            flush();
            $otVector = [];
            $otVector['data'][0]['id'] = $idOt;
            $descuentoOt ? $otVector['data'][0]['Amount'] = number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') : $otVector['data'][0]['Amount'] = number_format($totalSinImpuesto, 2, '.', '');
            $otJson = json_encode($otVector);
            $crm->actualizar("actualizarOt", $otJson);
            if ($crm->estado) {
                echo '<p style="color:pink;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">EL IMPORTE DE LA OT HA SIDO ACTUALIZADO EN EL CRM</p>';
                scrollUpdate();
                @ob_flush();
                flush();
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL ACTUALIZAR EL IMPORTE DE LA OT EN EL CRM</p>';
                print_r($crm->respuestaError);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }
            // DATOS GENERALES DEL CÁLCULO
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalRealizacion: ' . number_format($totalRealizacion, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalMontaje: ' . number_format($totalMontaje, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">acabados: ' . number_format($acabados, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">logos: ' . number_format($logos, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalMontajeLogos: ' . number_format($totalMontajeLogos, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">tomaDeMedidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalDesmontaje: ' . number_format($totalDesmontaje, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalDesmontajeLogos: ' . number_format($totalDesmontajeLogos, 2, '.', '') . '€</p>';
            if ($margenGanancia) {
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Margen de ganancia: ' . $margenGanancia . '%</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Valor del margen de ganancia: ' . $margenGananciaValue . '€</p>';
            }
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalSinImpuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€</p>';
            if ($descuentoOt) {
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOt: ' . $descuentoOt . '%</p>';
                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalSinImpuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€</p>';
            }
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Impuesto ' . $impuesto . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">impuestoPorc: ' . $impuestoPorc . '</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">impuestoAplicado: ' . number_format($impuestoAplicado, 2, '.', '') . '€</p>';
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalConImpuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€</p>';
            echo '<p style="color:pink;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total sumatoria bucles de los precios de las líneas: ' . number_format($totalPreciosLineas, 2, '.', '') . '€</p>';
            echo '<p style="color:pink;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total sumatoria bucles del descuento general: ' . number_format($totalDescOt, 2, '.', '') . '€</p>';
            scrollUpdate();
            @ob_flush();
            flush();
            //SI EXISTEN VISUALES Y LOGOS
            if ($numVisuales && $numLogos) {
                echo '<p style="display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">EXISTE UNA MEZCLA DE VISUALES Y LOGOS EN LAS LÍNEAS</p>';
                $totalRealizacionVL = $totalRealizacion + $acabados + $logos;
                $totalMontajeVL = $totalMontaje + $totalMontajeLogos + $totalDesmontaje + $totalDesmontajeLogos;
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . '</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Realización visuales: ' . number_format($realizacion, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . ' - Realización visuales: ' . number_format($realizacion, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . '\nRealización visuales: ' . number_format($realizacion, 2, '.', '') . '€\n';
                if ($descPorcRealización && $descuentosAutomaticos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento pactado en realización visuales: ' . $descPorcRealización . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en realización visuales: ' . number_format($totalDescRealizacion, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total realización visuales: ' . number_format($totalRealizacion, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Descuento pactado en realización visuales: ' . $descPorcRealización . '% - Descuento aplicado en realización visuales: ' . number_format($totalDescRealizacion, 2, '.', '') . '€ - Total realización visuales: ' . number_format($totalRealizacion, 2, '.', '') . '€ --- ';
                    $observacionesA3Erp .= 'Descuento pactado en realización visuales: ' . $descPorcRealización . '%\nDescuento aplicado en realización visuales: ' . number_format($totalDescRealizacion, 2, '.', '') . '€\nTotal realización visuales: ' . number_format($totalRealizacion, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Montaje visuales: ' . number_format($montaje, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Montaje visuales: ' . number_format($montaje, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Montaje visuales: ' . number_format($montaje, 2, '.', '') . '€\n';
                if ($descPorcMontaje && $descuentosAutomaticos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento pactado en montaje visuales: ' . $descPorcMontaje . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en montaje visuales: ' . number_format($totalDescMontaje, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total montaje visuales: ' . number_format($totalMontaje, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Descuento pactado en montaje visuales: ' . $descPorcMontaje . '% - Descuento aplicado en montaje visuales: ' . number_format($totalDescMontaje, 2, '.', '') . '€ - Total montaje visuales: ' . number_format($totalMontaje, 2, '.', '') . '€ --- ';
                    $observacionesA3Erp .= 'Descuento pactado en montaje visuales: ' . $descPorcMontaje . '%\nDescuento aplicado en montaje visuales: ' . number_format($totalDescMontaje, 2, '.', '') . '€\nTotal montaje visuales: ' . number_format($totalMontaje, 2, '.', '') . '€\n';
                }
                if ($numDesmontajeVisuales) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Desmontaje visuales: ' . number_format($desmontaje, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Desmontaje visuales: ' . number_format($desmontaje, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Desmontaje visuales: ' . number_format($desmontaje, 2, '.', '') . '€\n';
                    if ($descPorcMontaje && $descuentosAutomaticos) {
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento pactado en desmontaje visuales: ' . $descPorcMontaje . '%</p>';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en desmontaje visuales: ' . number_format($totalDescDesmontaje, 2, '.', '') . '€</p>';
                        $observacionesA3Erp .= 'Descuento pactado en desmontaje visuales: ' . $descPorcMontaje . '%\nDescuento aplicado en desmontaje visuales: ' . number_format($totalDescDesmontaje, 2, '.', '') . '€\n';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total desmontaje visuales: ' . number_format($totalDesmontaje, 2, '.', '') . '€</p>';
                        //$observacionesA3Erp .= 'Total desmontaje visuales: ' . number_format($totalDesmontaje, 2, '.', '') . '€ - ';
                        $observacionesA3Erp .= 'Total desmontaje visuales: ' . number_format($totalDesmontaje, 2, '.', '') . '€\n';
                    }
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Realización logos: ' . number_format($logos, 2, '.', '') . '€</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Montaje logos: ' . number_format($montajeLogos, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Realización logos: ' . number_format($logos, 2, '.', '') . '€ - Montaje logos: ' . number_format($montajeLogos, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Realización logos: ' . number_format($logos, 2, '.', '') . '€\nMontaje logos: ' . number_format($montajeLogos, 2, '.', '') . '€\n';
                if ($totalDescMontajeLogos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en Montaje de Logos: ' . number_format($totalDescMontajeLogos, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total Montaje de Logos: ' . number_format($totalMontajeLogos, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Descuento aplicado en Montaje de Logos: ' . number_format($totalDescMontajeLogos, 2, '.', '') . '€ - Total Montaje de Logos: ' . number_format($totalMontajeLogos, 2, '.', '') . '€ --- ';
                    $observacionesA3Erp .= 'Descuento aplicado en Montaje de Logos: ' . number_format($totalDescMontajeLogos, 2, '.', '') . '€\nTotal Montaje de Logos: ' . number_format($totalMontajeLogos, 2, '.', '') . '€\n';
                }
                if ($numDesmontajeLogos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Desmontaje logos: ' . number_format($desmontajeLogos, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Desmontaje logos: ' . number_format($desmontajeLogos, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Desmontaje logos: ' . number_format($desmontajeLogos, 2, '.', '') . '€\n';
                    if ($totalDescDesmontajeLogos) {
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en desmontaje logos: ' . number_format($totalDescDesmontajeLogos, 2, '.', '') . '€</p>';
                        $observacionesA3Erp .= 'Descuento aplicado en desmontaje logos: ' . number_format($totalDescDesmontajeLogos, 2, '.', '') . '€\n';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total desmontaje logos: ' . number_format($totalDesmontajeLogos, 2, '.', '') . '€</p>';
                        //$observacionesA3Erp .= 'Total desmontaje logos: ' . number_format($totalDesmontajeLogos, 2, '.', '') . '€ - ';
                        $observacionesA3Erp .= 'Total desmontaje logos: ' . number_format($totalDesmontajeLogos, 2, '.', '') . '€\n';
                    }
                }
                if ($acabados) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Acabados: ' . number_format($acabados, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Acabados: ' . number_format($acabados, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Acabados: ' . number_format($acabados, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total realización (Visuales + Logos + Acabados): ' . number_format($totalRealizacionVL, 2, '.', '') . '€</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total montaje (Visuales + Logos + Desmontajes): ' . number_format($totalMontajeVL, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Total realización (Visuales + Logos + Acabados): ' . number_format($totalRealizacionVL, 2, '.', '') . '€ - Total montaje (Visuales + Logos + Desmontajes): ' . number_format($totalMontajeVL, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Total realización (Visuales + Logos + Acabados): ' . number_format($totalRealizacionVL, 2, '.', '') . '€\nTotal montaje (Visuales + Logos + Desmontajes): ' . number_format($totalMontajeVL, 2, '.', '') . '€\n';
                if ($tomaDeMedidas) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€</p>';
                $observacionesA3Erp .= 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€\n';
                if ($descuentoOt) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . $descuentoOt . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€</p>';
                    $observacionesA3Erp .= 'Descuento general: ' . $descuentoOt . '%\nDescuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€\nTotal presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Impuesto ' . $impuesto . '</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€ - Impuesto ' . $impuesto . ' - Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Impuesto ' . $impuesto . '\nTotal presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€\n';
            }
            /* SI SÓLO EXISTEN VISUALES */ else if ($numVisuales) {
                echo '<p style="display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LAS LÍNEAS SON VISUALES</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . '</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Realización: ' . number_format($realizacion, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . ' - Realización: ' . number_format($realizacion, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . '\nRealización: ' . number_format($realizacion, 2, '.', '') . '€\n';
                if ($descPorcRealización && $descuentosAutomaticos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento pactado en Realización: ' . $descPorcRealización . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en Realización: ' . number_format($totalDescRealizacion, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total Realización: ' . number_format($totalRealizacion, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Descuento pactado en Realización: ' . $descPorcRealización . '% - Descuento aplicado en Realización: ' . number_format($totalDescRealizacion, 2, '.', '') . '€ - Total Realización: ' . number_format($totalRealizacion, 2, '.', '') . '€ --- ';
                    $observacionesA3Erp .= 'Descuento pactado en Realización: ' . $descPorcRealización . '%\nDescuento aplicado en Realización: ' . number_format($totalDescRealizacion, 2, '.', '') . '€\nTotal Realización: ' . number_format($totalRealizacion, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Montaje: ' . number_format($montaje, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Montaje: ' . number_format($montaje, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Montaje: ' . number_format($montaje, 2, '.', '') . '€\n';
                if ($descPorcMontaje && $descuentosAutomaticos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento pactado en Montaje: ' . $descPorcMontaje . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en Montaje: ' . number_format($totalDescMontaje, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total Montaje: ' . number_format($totalMontaje, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Descuento pactado en Montaje: ' . $descPorcMontaje . '% - Descuento aplicado en Montaje: ' . number_format($totalDescMontaje, 2, '.', '') . '€ - Total Montaje: ' . number_format($totalMontaje, 2, '.', '') . '€ --- ';
                    $observacionesA3Erp .= 'Descuento pactado en Montaje: ' . $descPorcMontaje . '%\nDescuento aplicado en Montaje: ' . number_format($totalDescMontaje, 2, '.', '') . '€\nTotal Montaje: ' . number_format($totalMontaje, 2, '.', '') . '€\n';
                }
                if ($numDesmontajeVisuales) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Desmontaje visuales: ' . number_format($desmontaje, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Desmontaje visuales: ' . number_format($desmontaje, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Desmontaje visuales: ' . number_format($desmontaje, 2, '.', '') . '€\n';
                    if ($descPorcMontaje && $descuentosAutomaticos) {
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento pactado en desmontaje visuales: ' . $descPorcMontaje . '%</p>';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en desmontaje visuales: ' . number_format($totalDescDesmontaje, 2, '.', '') . '€</p>';
                        $observacionesA3Erp .= 'Descuento pactado en desmontaje visuales: ' . $descPorcMontaje . '%\nDescuento aplicado en desmontaje visuales: ' . number_format($totalDescDesmontaje, 2, '.', '') . '€\n';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total desmontaje visuales: ' . number_format($totalDesmontaje, 2, '.', '') . '€</p>';
                        //$observacionesA3Erp .= 'Total desmontaje visuales: ' . number_format($totalDesmontaje, 2, '.', '') . '€ - ';
                        $observacionesA3Erp .= 'Total desmontaje visuales: ' . number_format($totalDesmontaje, 2, '.', '') . '€\n';
                    }
                }
                if ($acabados) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Acabados: ' . number_format($acabados, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Acabados: ' . number_format($acabados, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Acabados: ' . number_format($acabados, 2, '.', '') . '€\n';
                }
                if ($tomaDeMedidas) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€</p>';
                $observacionesA3Erp .= 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€\n';
                if ($descuentoOt) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . $descuentoOt . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€</p>';
                    $observacionesA3Erp .= 'Descuento general: ' . $descuentoOt . '%\nDescuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€\nTotal presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Impuesto ' . $impuesto . '</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€ - Impuesto ' . $impuesto . ' - Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Impuesto ' . $impuesto . '\nTotal presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€\n';
            }
            /* SI SÓLO EXISTEN LOGOS */ else if ($numLogos) {
                echo '<p style="display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LAS LÍNEAS SON LOGOS</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . '</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Realización: ' . number_format($logos, 2, '.', '') . '€</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Montaje: ' . number_format($montajeLogos, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . ' - Realización: ' . number_format($logos, 2, '.', '') . '€ - Montaje: ' . number_format($montajeLogos, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Metros cuadrados: ' . number_format($totalM2, 2, '.', '') . '\nRealización: ' . number_format($logos, 2, '.', '') . '€\nMontaje: ' . number_format($montajeLogos, 2, '.', '') . '€\n';
                if ($totalDescMontajeLogos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en Montaje: ' . number_format($totalDescMontajeLogos, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total Montaje: ' . number_format($totalMontajeLogos, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Descuento aplicado en Montaje: ' . number_format($totalDescMontajeLogos, 2, '.', '') . '€ - Total Montaje: ' . number_format($totalMontajeLogos, 2, '.', '') . '€ --- ';
                    $observacionesA3Erp .= 'Descuento aplicado en Montaje: ' . number_format($totalDescMontajeLogos, 2, '.', '') . '€\nTotal Montaje: ' . number_format($totalMontajeLogos, 2, '.', '') . '€\n';
                }
                if ($numDesmontajeLogos) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Desmontaje logos: ' . number_format($desmontajeLogos, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Desmontaje logos: ' . number_format($desmontajeLogos, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Desmontaje logos: ' . number_format($desmontajeLogos, 2, '.', '') . '€\n';
                    if ($totalDescDesmontajeLogos) {
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento aplicado en desmontaje logos: ' . number_format($totalDescDesmontajeLogos, 2, '.', '') . '€</p>';
                        $observacionesA3Erp .= 'Descuento aplicado en desmontaje logos: ' . number_format($totalDescDesmontajeLogos, 2, '.', '') . '€\n';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total desmontaje logos: ' . number_format($totalDesmontajeLogos, 2, '.', '') . '€</p>';
                        //$observacionesA3Erp .= 'Total desmontaje logos: ' . number_format($totalDesmontajeLogos, 2, '.', '') . '€ - ';
                        $observacionesA3Erp .= 'Total desmontaje logos: ' . number_format($totalDesmontajeLogos, 2, '.', '') . '€\n';
                    }
                }
                if ($acabados) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Acabados: ' . number_format($acabados, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Acabados: ' . number_format($acabados, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Acabados: ' . number_format($acabados, 2, '.', '') . '€\n';
                }
                if ($tomaDeMedidas) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€</p>';
                $observacionesA3Erp .= 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€\n';
                if ($descuentoOt) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . $descuentoOt . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€</p>';
                    $observacionesA3Erp .= 'Descuento general: ' . $descuentoOt . '%\nDescuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€\nTotal presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Impuesto ' . $impuesto . '</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€ - Impuesto ' . $impuesto . ' - Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Impuesto ' . $impuesto . '\nTotal presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€\n';
            }
            /* SI SÓLO EXISTEN TOMAS DE MEDIDA */ else if ($numTomasDeMedida) {
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€</p>';
                $observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€\n';
                if ($descuentoOt) {
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . $descuentoOt . '%</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€</p>';
                    $observacionesA3Erp .= 'Descuento general: ' . $descuentoOt . '%\nDescuento general: ' . number_format($descuentoOtValue, 2, '.', '') . '€\nTotal presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€\n';
                }
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Impuesto ' . $impuesto . '</p>';
                echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€</p>';
                //$observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€ - Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€ - Impuesto ' . $impuesto . ' - Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€ - ';
                $observacionesA3Erp .= 'Impuesto ' . $impuesto . '\nTotal presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€\n';
            }

            $observacionesA3Erp = str_replace('\n', "\n", $observacionesA3Erp);
            scrollUpdate();
            @ob_flush();
            flush();
            echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">' . $observacionesA3Erp . '</p>';
            scrollUpdate();
            @ob_flush();
            flush();

            //OGANIZAR LA INFORMACIÓN PARA A3ERP Y CREAR EL PRESUPUESTO EN A3ERP
            $a3ErpData['Observaciones'] = $observacionesA3Erp;
            $a3ErpData['Lineas'] = $lineasA3Erp;
            echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CREANDO EL PRESUPUESTO EN A3 ERP...</p>';
            scrollUpdate();
            @ob_flush();
            flush();
            $responseA3Erp = $a3Erp->post('pedidoVenta', $a3ErpData);
            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">responseA3Erp: </p>';
            var_dump($responseA3Erp);
            scrollUpdate();
            @ob_flush();
            flush();
            if ($responseA3Erp) {
                echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">RESPUESTA DE A3 ERP RECIBIDA!!!</p>';
                scrollUpdate();
                @ob_flush();
                flush();
                if (isset($responseA3Erp['Error'])) {
                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! HA HABIDO UN ERROR EN LA API DE A3 ERP A LA HORA DE CREAR EL PRESUPUESTO, EL PRESUPUESTO NO HA SIDO CREADO EN A3 ERP. VERIFICA POR FAVOR QUE LA OT O EL CENTRO DE COSTE EN A3 ERP EXISTA. SI EL ERROR PERSISTE POR FAVOR REVISA LA RESPUESTA DE A3 ERP QUE SE HA IMPRIMIDO EN PANTALLA, PARA VERIFICAR SI PUEDES ENTENDER EL ERROR ANTES DE CONTACTAR A IT.</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                }
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CREAR EL PRESUPUESTO EN A3 ERP, NO SE HA RECIBIDO UNA RESPUESTA DE LA API DE A3 ERP QUE TENGA VALOR</p>';
                print_r($a3Erp->error);
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }
        } catch (\Throwable $th) {
            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EN EL SERVIDOR</p>';
            var_dump($th);
        }
    } else {
        try {
            //OBTENEMOS EL CÓDIGO DE ARTÍCULO DE A3 ERP DEL TIPO DE OT
            foreach ($materialesServicios as $materialServicio) {
                if ($materialServicio['Abreviatura'] == $tipoOt) {
                    $codigoArticuloA3Erp = $materialServicio['idA3Erp'];
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">codigoArticuloA3Erp: ' . $codigoArticuloA3Erp . '</p>';
                    print_r($materialServicio);
                    scrollUpdate();
                    @ob_flush();
                    flush();
                    break;
                }
            }

            //VARIABLES DEL CÁLCULO
            $realizacion = 0;
            $montaje = 0;
            $precioLinea = 0;
            $totalPreciosLineas = 0;

            if ($codigoArticuloA3Erp) {
                if ($lineas) {
                    //BUCLE DE ITERACIÓN POR CADA LÍNEA DE LA OT
                    foreach ($lineas as $linea) {
                        $incluir = $linea['Incluir'];
                        $nombreDeLinea = $linea['Product_Name'];
                        $codLinea = $linea['Codigo_de_l_nea'];
                        $realizacionLinea = $linea['Realizaci_n'];
                        $montajeLinea = $linea['Montaje'];
                        if ($incluir) {
                            array_push($lineasIncluidas, $codLinea);
                            if (!$realizacionLinea || !$montajeLinea) {
                                if (!$realizacionLinea) {
                                    $realizacionLinea = 0.00;
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LA LÍNEA' . $codLinea . ' NO TIENE ESTABLECIDO LA REALIZACIÓN.</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                                if (!$montajeLinea) {
                                    $montajeLinea = 0.00;
                                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LA LÍNEA' . $codLinea . ' NO TIENE ESTABLECIDO EL MONTAJE.</p>';
                                    scrollUpdate();
                                    @ob_flush();
                                    flush();
                                }
                            }
                            $realizacion = $realizacion + $realizacionLinea;
                            $montaje = $montaje + $montajeLinea;
                            $precioLinea = $realizacionLinea + $montajeLinea;
                            // MARGEN DE GANANCIA
                            if ($margenGanancia) {
                                $margenGananciaValueLinea = ($precioLinea * $margenGanancia) / 100;
                                $margenGananciaValue = $margenGananciaValue + $margenGananciaValueLinea;
                                $precioLinea = $precioLinea + $margenGananciaValueLinea;
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">margenGananciaValueLinea: ' . $margenGananciaValueLinea . '</p>';
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                            }
                            // DESCUENTO OT
                            if ($descuentoOt) {
                                $descuentoOtEnLinea = ($precioLinea * $descuentoOt) / 100;
                                $descuentoOtEnLinea = number_format($descuentoOtEnLinea, 2, '.', '');
                                $totalDescOt = $totalDescOt + $descuentoOtEnLinea;
                                $precioLinea = $precioLinea - $descuentoOtEnLinea;
                                $precioLinea = number_format($precioLinea, 2, '.', '');
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">descuentoOtEnLinea: ' . $descuentoOtEnLinea . '</p>';
                                echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">precioLinea: ' . $precioLinea . '</p>';
                                scrollUpdate();
                                @ob_flush();
                                flush();
                            }
                            $totalPreciosLineas = $totalPreciosLineas + $precioLinea;
                            //LÍNEA DE A3 ERP
                            $lineaA3Erp = [];
                            $lineaA3Erp['CodigoArticulo'] = $codigoArticuloA3Erp;
                            $lineaA3Erp['Unidades'] = 1;
                            $lineaA3Erp['Param2'] = $realizacionLinea;
                            $lineaA3Erp['Param3'] = $montajeLinea;
                            $lineaA3Erp['Precio'] = $precioLinea;
                            $lineaA3Erp['Texto'] = $codLinea . ' - ' . $nombreDeLinea;
                            array_push($lineasA3Erp, $lineaA3Erp);
                            echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LÍNEA' . $codLinea . ' INCLUIDA CON EL PRECIO: ' . $precioLinea . '</p>';
                            scrollUpdate();
                            @ob_flush();
                            flush();
                        } else {
                            array_push($lineasNoIncluidas, $codLinea);
                            echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LÍNEA ' . $codLinea . ' NO ESTÁ INCLUIDA EN EL CÁLCULO...</p>';
                            scrollUpdate();
                            @ob_flush();
                            flush();
                        }
                    }
                    /* RESULTADOS */
                    echo '<h1 style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">RESULTADOS OT ' . $codOt . '</h1>';
                    if (count($lineasNoIncluidas)) {
                        echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LÍNEAS NO INCLUIDAS:</p>';
                        print_r($lineasNoIncluidas);
                    }
                    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">LÍNEAS INCLUIDAS:</p>';
                    print_r($lineasIncluidas);
                    /* TOTALES */
                    $totalRealizacion = $realizacion;
                    $totalMontaje = $montaje;
                    $totalSinImpuesto = $totalRealizacion + $totalMontaje + $margenGananciaValue;
                    //DESCUENTO MANUAL DE LA OT
                    $descuentoOtValue = ($totalSinImpuesto * $descuentoOt) / 100;
                    $totalSinImpuestoConDescuentoOt = $totalSinImpuesto - $descuentoOtValue;
                    //APLICACIÓN DEL IMPUESTO
                    $impuestoAplicado = 0;
                    $totalConImpuesto = 0;
                    if ($descuentoOt) {
                        $impuestoAplicado = ($totalSinImpuestoConDescuentoOt * $impuestoPorc) / 100;
                        $totalConImpuesto = $totalSinImpuestoConDescuentoOt + $impuestoAplicado;
                    } else {
                        $impuestoAplicado = ($totalSinImpuesto * $impuestoPorc) / 100;
                        $totalConImpuesto = $totalSinImpuesto + $impuestoAplicado;
                    }
                    /* ACTUALIZAR IMPORTE DE LA OT */
                    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ACTUALIZANDO EL IMPORTE DE LA OT EN EL CRM...</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                    $otVector = [];
                    $otVector['data'][0]['id'] = $idOt;
                    $descuentoOt ? $otVector['data'][0]['Amount'] = number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') : $otVector['data'][0]['Amount'] = number_format($totalSinImpuesto, 2, '.', '');
                    $otJson = json_encode($otVector);
                    $crm->actualizar("actualizarOt", $otJson);
                    if ($crm->estado) {
                        echo '<p style="color:pink;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">EL IMPORTE DE LA OT HA SIDO ACTUALIZADO EN EL CRM</p>';
                        scrollUpdate();
                        @ob_flush();
                        flush();
                    } else {
                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL ACTUALIZAR EL IMPORTE DE LA OT EN EL CRM</p>';
                        print_r($crm->respuestaError);
                        scrollUpdate();
                        @ob_flush();
                        flush();
                        die();
                    }
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalRealizacion: ' . number_format($totalRealizacion, 2, '.', '') . '€</p>';
                    $observacionesA3Erp .= 'Total realización: ' . number_format($totalRealizacion, 2, '.', '') . '€\n';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalMontaje: ' . number_format($totalMontaje, 2, '.', '') . '€</p>';
                    $observacionesA3Erp .= 'Total montaje: ' . number_format($totalMontaje, 2, '.', '') . '€\n';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€</p>';
                    $observacionesA3Erp .= 'Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€\n';
                    if ($margenGanancia) {
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Margen de ganancia: ' . $margenGanancia . '%</p>';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Valor del margen de ganancia: ' . $margenGananciaValue . '€</p>';
                    }
                    if ($descuentoOt) {
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general: ' . $descuentoOt . '%</p>';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Descuento general aplicado: ' . number_format($descuentoOtValue, 2, '.', '') . '€</p>';
                        echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€</p>';
                        $observacionesA3Erp .= 'Descuento general: ' . $descuentoOt . '%\nDescuento general aplicado: ' . number_format($descuentoOtValue, 2, '.', '') . '€\nTotal presupuesto sin impuesto: ' . number_format($totalSinImpuestoConDescuentoOt, 2, '.', '') . '€\n';
                    }
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Impuesto ' . $impuesto . '</p>';
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€</p>';
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">totalPreciosLineas: ' . number_format($totalPreciosLineas, 2, '.', '') . '€</p>';
                    //$observacionesA3Erp .= 'Toma de medidas: ' . number_format($tomaDeMedidas, 2, '.', '') . '€ - Total presupuesto sin impuesto: ' . number_format($totalSinImpuesto, 2, '.', '') . '€ - Impuesto ' . $impuesto . ' - Total presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€ - ';
                    $observacionesA3Erp .= 'Impuesto ' . $impuesto . '\nTotal presupuesto con impuesto: ' . number_format($totalConImpuesto, 2, '.', '') . '€\n';
                    $observacionesA3Erp = str_replace('\n', "\n", $observacionesA3Erp);
                    scrollUpdate();
                    @ob_flush();
                    flush();
                    echo '<p style="color:blue;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">' . $observacionesA3Erp . '</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                    //OGANIZAR LA INFORMACIÓN PARA A3ERP Y CREAR EL PRESUPUESTO EN A3ERP
                    $a3ErpData['Observaciones'] = $observacionesA3Erp;
                    $a3ErpData['Lineas'] = $lineasA3Erp;
                    echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">CREANDO EL PRESUPUESTO EN A3 ERP...</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                    $responseA3Erp = $a3Erp->post('pedidoVenta', $a3ErpData);
                    echo '<p style="color:orange;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">responseA3Erp: </p>';
                    var_dump($responseA3Erp);
                    scrollUpdate();
                    @ob_flush();
                    flush();
                    if ($responseA3Erp) {
                        echo '<p style="color:green;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">RESPUESTA DE A3 ERP RECIBIDA!!!</p>';
                        scrollUpdate();
                        @ob_flush();
                        flush();
                        if (isset($responseA3Erp['Error'])) {
                            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! HA HABIDO UN ERROR EN LA API DE A3 ERP A LA HORA DE CREAR EL PRESUPUESTO, EL PRESUPUESTO NO HA SIDO CREADO EN A3 ERP. VERIFICA POR FAVOR QUE LA OT O EL CENTRO DE COSTE EN A3 ERP EXISTA. SI EL ERROR PERSISTE POR FAVOR REVISA LA RESPUESTA DE A3 ERP QUE SE HA IMPRIMIDO EN PANTALLA, PARA VERIFICAR SI PUEDES ENTENDER EL ERROR ANTES DE CONTACTAR A IT.</p>';
                            scrollUpdate();
                            @ob_flush();
                            flush();
                        }
                    } else {
                        echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! AL CREAR EL PRESUPUESTO EN A3 ERP, NO SE HA RECIBIDO UNA RESPUESTA DE LA API DE A3 ERP QUE TENGA VALOR</p>';
                        print_r($a3Erp->error);
                        scrollUpdate();
                        @ob_flush();
                        flush();
                        die();
                    }
                } else {
                    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HAN OBTENIDO LÍNEAS DE LA OT DEL CRM</p>';
                    scrollUpdate();
                    @ob_flush();
                    flush();
                    die();
                }
            } else {
                echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! NO SE HA OBTENIDO EL CÓDIGO DE ARTÍCULO DE A3 ERP DESDE LA API DEL CRM</p>';
                scrollUpdate();
                @ob_flush();
                flush();
                die();
            }
        } catch (\Throwable $th) {
            echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EN EL SERVIDOR</p>';
            var_dump($th);
            scrollUpdate();
            @ob_flush();
            flush();
            die();
        }
    }
} catch (\Throwable $th) {
    echo '<p style="color:red;display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">ERROR!!! EN EL SERVIDOR</p>';
    var_dump($th);
    scrollUpdate();
    @ob_flush();
    flush();
    die();
}

// FUNCIÓN PARA HACER SCROLL Y MANTENER LA VISIBILIDAD DEL ÚLTIMO MENSAJE DEL SERVIDOR
function scrollUpdate()
{
    $uniqueId = 'response_' . time() . '_' . rand(1000, 9999);
    echo '<p id="' . $uniqueId . '" style="display:flex;flex-direction:column;justify-content:center;align-items:center;width:100%">...</p>';
    echo '<script>setTimeout(function() {let lastResponse = document.getElementById("' . $uniqueId . '");if (lastResponse) {lastResponse.scrollIntoView({ behavior: "smooth", block: "end" });}}, 500); // Pequeño retraso para asegurar que el DOM está actualizado</script>';
}

//FUNCIÓN PARA ORDENAR RESPUESTAS (ARRAY INDEXADOS) POR CAMPOS
function ordenarArrayPorCampo(array $array, string $campo, string $orden = 'asc'): array
{
    usort($array, function ($a, $b) use ($campo, $orden) {
        $valA = isset($a[$campo]) ? (int)$a[$campo] : 0;
        $valB = isset($b[$campo]) ? (int)$b[$campo] : 0;
        return $orden === 'asc' ? $valA <=> $valB : $valB <=> $valA;
    });
    return $array;
}

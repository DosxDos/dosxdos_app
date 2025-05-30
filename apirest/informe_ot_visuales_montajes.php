<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe visuales - montajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="http://localhost/dosxdos_app/img/logo-red.png" />
    <link rel="stylesheet" href="http://localhost/dosxdos_app/css/informe_ot_visuales_montajes.css?v=1" />
    <link rel="stylesheet" href="http://localhost/dosxdos_app/css/tailwindmain.css" />
    <script src="http://localhost/dosxdos_app/js/pdfmake.min.js"></script>
    <script src="http://localhost/dosxdos_app/js/vfs_fonts.js"></script>
    <script src="http://localhost/dosxdos_app/js/informe_ot_visuales_montajes.js?v=1"></script>
</head>

<body class="bg-gray-50 min-h-screen p-4 flex flex-col items-center justify-start">
    <div id="loader" class="displayOn">
        <div id="loader-text"></div>
        <div class="dots" id="loader-dots"><span>.</span><span>.</span><span>.</span></div>
    </div>

    <?php

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    @ob_flush();
    flush();

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('curl.cainfo', '/dev/null');
    set_time_limit(0);
    ini_set('default_socket_timeout', 28800);
    date_default_timezone_set('Atlantic/Canary');

    require_once 'middlewares/jwtMiddleware.php';
    require_once 'clases/crm_clase.php';
    require_once 'utils/funciones_data.php';

    if (isset($_GET['idOt']) && isset($_GET['codOt']) && isset($_GET['nombreOt']) && isset($_GET['tipoOt']) && isset($_GET['cliente']) && isset($_GET['tokenJwt'])) {

        $jwtMiddleware = new JwtMiddleware;
        $jwtMiddleware->verificar();

        $idOt = $_GET['idOt'];
        $codOt = $_GET['codOt'];
        $nombreOt = $_GET['nombreOt'];
        $tipoOt = $_GET['tipoOt'];
        $cliente = $_GET['cliente'];
        $firma = '';
        if (isset($_GET['firma'])) {
            $firma = $_GET['firma'];
        }

        if ($idOt && $codOt && $nombreOt && $tipoOt && $cliente) {

            if ($tipoOt != "VIS") {
    ?>
                <script>
                    document.getElementById('loader-text').textContent = 'Error! El tipo de OT no pertenece a VISUALES';
                    document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                    errorApp = true;
                </script>
                <?php
            } else {
                $crm = new Crm;
                $numLineas = 0;
                $lineas;

                /* LINEAS */
                $camposLineas = "Punto_de_venta,Fecha_entrada,Codigo_de_l_nea,Ubicaci_n,Material,Tipo_de_trabajo,Quitar,Poner,Ancho_medida,Alto_medida,Ancho_total,Alto_total";
                $query = "SELECT $camposLineas FROM Products WHERE OT_relacionada=$idOt";
                $crm->query($query);
                if ($crm->estado) {
                    $lineas = $crm->respuesta[1]['data'];
                    $lineas = ordenarArrayPorCampo($lineas, 'Codigo_de_l_nea');
                    $numLineas = count($lineas);
                    $pvs = [];
                    $validarPv = [];
                    $fechasEntrada = [];
                    // BUCLE DE ITERACIÓN POR CADA LÍNEA
                    foreach ($lineas as $linea) {
                        $fechaEntrada = $linea['Fecha_entrada'];
                        if ($fechaEntrada) {
                            array_push($fechasEntrada, $fechaEntrada);
                            $idPv = $linea['Punto_de_venta']['id'];
                            if (!isset($validarPv[$idPv])) {
                                $validarPv[$idPv] = true;
                                $camposPv = "Name,N_tel_fono,Direcci_n,rea,Zona";
                                $query = "SELECT $camposPv FROM Puntos_de_venta WHERE id=\"$idPv\"";
                                $crm->query($query);
                                if ($crm->estado) {
                                    $nombrePv = $crm->respuesta[1]['data'][0]['Name'];
                                    $telefonoPv = $crm->respuesta[1]['data'][0]['N_tel_fono'];
                                    $direccionPv = $crm->respuesta[1]['data'][0]['Direcci_n'];
                                    $areaPv = $crm->respuesta[1]['data'][0]['rea'];
                                    $zonaPv = $crm->respuesta[1]['data'][0]['Zona'];
                                    $pv = [];
                                    $pv['id'] = $idPv;
                                    $pv['nombre'] = $nombrePv;
                                    $pv['telefono'] = $telefonoPv;
                                    $pv['direccion'] = $direccionPv;
                                    $pv['area'] = $areaPv;
                                    $pv['zona'] = $zonaPv;
                                    $pv['lineas'] = [];
                                    $lineaVector = [];
                                    $lineaPv = $idPv;
                                    $lineaFechaEntrada = $fechaEntrada;
                                    $lineaCod = $linea['Codigo_de_l_nea'];
                                    $lineaUbicacion = $linea['Ubicaci_n'];
                                    $lineaMaterial = $linea['Material'];
                                    $lineaTipo = $linea['Tipo_de_trabajo'];
                                    $lineaQuitar = $linea['Quitar'];
                                    $lineaPoner = $linea['Poner'];
                                    $lineaAncho = 0;
                                    $lineaAlto = 0;
                                    if ($linea['Ancho_total'] && $linea['Alto_total']) {
                                        $lineaAncho = $linea['Ancho_total'];
                                        $lineaAlto = $linea['Alto_total'];
                                    } else {
                                        $lineaAncho = $linea['Ancho_medida'];
                                        $lineaAlto = $linea['Alto_medida'];
                                    }
                                    $lineaVector['pv'] = $lineaPv;
                                    $lineaVector['fechaEntrada'] = $lineaFechaEntrada;
                                    $lineaVector['codigo'] = $lineaCod;
                                    $lineaVector['ubicacion'] = $lineaUbicacion;
                                    $lineaVector['material'] = $lineaMaterial;
                                    $lineaVector['tipo'] = $lineaTipo;
                                    $lineaVector['quitar'] = $lineaQuitar;
                                    $lineaVector['poner'] = $lineaPoner;
                                    $lineaVector['ancho'] = $lineaAncho;
                                    $lineaVector['alto'] = $lineaAlto;
                                    array_push($pv['lineas'], $lineaVector);
                                    array_push($pvs, $pv);
                                } else {
                ?>
                                    <script>
                                        document.getElementById('loader-text').textContent = 'Error! Al consultar las líneas de la OT con fecha de entrada en el CRM';
                                        document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                                        errorApp = true;
                                    </script>
                        <?php
                                    break;
                                }
                            } else {
                                $i = 0;
                                foreach ($pvs as $pv) {
                                    if ($pv['id'] == $idPv) {
                                        $lineaVector = [];
                                        $lineaPv = $idPv;
                                        $lineaFechaEntrada = $fechaEntrada;
                                        $lineaCod = $linea['Codigo_de_l_nea'];
                                        $lineaUbicacion = $linea['Ubicaci_n'];
                                        $lineaMaterial = $linea['Material'];
                                        $lineaTipo = $linea['Tipo_de_trabajo'];
                                        $lineaQuitar = $linea['Quitar'];
                                        $lineaPoner = $linea['Poner'];
                                        $lineaAncho = 0;
                                        $lineaAlto = 0;
                                        if ($linea['Ancho_total'] && $linea['Alto_total']) {
                                            $lineaAncho = $linea['Ancho_total'];
                                            $lineaAlto = $linea['Alto_total'];
                                        } else {
                                            $lineaAncho = $linea['Ancho_medida'];
                                            $lineaAlto = $linea['Alto_medida'];
                                        }
                                        $lineaVector['pv'] = $lineaPv;
                                        $lineaVector['fechaEntrada'] = $lineaFechaEntrada;
                                        $lineaVector['codigo'] = $lineaCod;
                                        $lineaVector['ubicacion'] = $lineaUbicacion;
                                        $lineaVector['material'] = $lineaMaterial;
                                        $lineaVector['tipo'] = $lineaTipo;
                                        $lineaVector['quitar'] = $lineaQuitar;
                                        $lineaVector['poner'] = $lineaPoner;
                                        $lineaVector['ancho'] = $lineaAncho;
                                        $lineaVector['alto'] = $lineaAlto;
                                        array_push($pvs[$i]['lineas'], $lineaVector);
                                    }
                                    $i++;
                                }
                            }
                        }
                    }
                    if ($fechasEntrada) {
                        $fechasEntrada = array_unique($fechasEntrada);
                        rsort($fechasEntrada);
                        $otVector = [];
                        $otVector['ot'] = [];
                        $otVector['pvs'] = $pvs;
                        $otVector['ot']['id'] = $idOt;
                        $otVector['ot']['codOt'] = $codOt;
                        $otVector['ot']['nombreOt'] = $nombreOt;
                        $otVector['ot']['cliente'] = $cliente;
                        $otVector['ot']['firma'] = $firma;
                        $otVector['ot']['fechasEntrada'] = $fechasEntrada;
                        ?>
                        <script>
                            const data = <?php echo json_encode($otVector, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                            console.log(data);
                        </script>
                    <?php
                    } else {
                    ?>
                        <script>
                            document.getElementById('loader-text').textContent = 'Error! No existen líneas con fecha de entrada';
                            document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                            errorApp = true;
                        </script>
                    <?php
                    }
                } else {
                    ?>
                    <script>
                        document.getElementById('loader-text').textContent = 'Error! Al consultar las líneas de la OT con fecha de entrada en el CRM';
                        document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                        errorApp = true;
                    </script>
            <?php
                }
            }
        } else {
            ?>
            <script>
                document.getElementById('loader-text').textContent = 'Error! No se han recibido los datos necesarios';
                document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                errorApp = true;
            </script>
        <?php
        }
    } else {
        ?>
        <script>
            document.getElementById('loader-text').textContent = 'Error! No se han recibido los datos necesarios';
            document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
            errorApp = true;
        </script>
    <?php
    }
    ?>

    <h1 class="text-xl font-semibold text-gray-700 mb-4" id="tituloPrincipal">INFORME VISUALES - MONTAJES</h1>
    <h2 class="text-lg font-semibold text-gray-700 mb-4" id="tituloSecundario"></h2>

    <div class="w-full max-w-md bg-white shadow rounded p-4 space-y-4">
        <h3 class="font-semibold text-gray-700">Selecciona Fechas de entrada</h3>

        <div class="space-y-2">
            <label class="flex items-center space-x-2">
                <input type="checkbox" id="check-todas" class="form-checkbox text-red-600">
                <span class="text-gray-700 font-medium">Todas las fechas</span>
            </label>

            <div id="fechas-container" class="space-y-2">
                <!-- Fechas individuales -->
            </div>
        </div>

        <button
            id="btn-pdf"
            disabled
            class="w-full mt-4 bg-red-600 text-white py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
            Generar PDF
        </button>
    </div>


    <script src="http://localhost/dosxdos_app/js/pdf_informe_ot_visuales_montajes.js?v=1"></script>

</body>

</html>

<?php
@ob_flush();
flush();
?>
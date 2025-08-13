<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Ot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="http://localhost:8080/img/logo-red.png" />
    <link rel="stylesheet" href="http://localhost:8080/css/informe_ot.css?v=1" />
    <link rel="stylesheet" href="http://localhost:8080/css/tailwindmain.css" />
    <script src="http://localhost:8080/js/pdfmake.min.js"></script>
    <script src="http://localhost:8080/js/vfs_fonts.js"></script>
    <script src="http://localhost:8080/js/informe_ot.js?v=1"></script>
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
        $contacto = '';
        if (isset($_GET['contacto'])) {
            $contacto = $_GET['contacto'];
        }
        $creacion = '';
        if (isset($_GET['creacion'])) {
            $creacion = $_GET['creacion'];
        }
        $subtipo = '';
        if (isset($_GET['subtipo'])) {
            $subtipo = $_GET['subtipo'];
        }
        $creadaPor = '';
        if (isset($_GET['creadaPor'])) {
            $creadaPor = $_GET['creadaPor'];
        }


        if ($idOt && $codOt && $nombreOt && $tipoOt && $cliente) {

            $crm = new Crm;
            $numLineas = 0;
            $lineas;

            /* LINEAS */
            $camposLineas = "Punto_de_venta,Incluir,Codigo_de_l_nea,Fecha_de_Previsi_n_de_L_nea,Observaciones_internas,Quitar,Poner";
            $query = "SELECT $camposLineas FROM Products WHERE OT_relacionada=$idOt AND Fase!='Perdidas'";
            $crm->query($query);
            if ($crm->estado) {
                $lineas = $crm->respuesta[1]['data'];
                $lineas = ordenarArrayPorCampo($lineas, 'Codigo_de_l_nea');
                $numLineas = count($lineas);
                $lineasIncluidas = 0;
                $lineasOrdenadas = [];
                $pvs = [];
                // BUCLE DE ITERACIÓN POR CADA LÍNEA
                foreach ($lineas as $linea) {
                    $incluir = $linea['Incluir'];
                    if ($incluir) {
                        $lineasIncluidas++;
                        $codLinea = $linea['Codigo_de_l_nea'];
                        $previsionLinea = $linea['Fecha_de_Previsi_n_de_L_nea'];
                        $observacionesLinea = $linea['Observaciones_internas'];
                        $idPv = $linea['Punto_de_venta']['id'];
                        $quitar = $linea['Quitar'];
                        $poner = $linea['Poner'];
                        if (!isset($pvs[$idPv])) {
                            $pvs[$idPv] = [];
                            $camposPv = "Name,rea,Sector,Zona,Direcci_n";
                            $query = "SELECT $camposPv FROM Puntos_de_venta WHERE id=\"$idPv\"";
                            $crm->query($query);
                            if ($crm->estado) {
                                $nombrePv = $crm->respuesta[1]['data'][0]['Name'];
                                $areaPv = $crm->respuesta[1]['data'][0]['rea'];
                                $sectorPv = $crm->respuesta[1]['data'][0]['Sector'];
                                $zonaPv = $crm->respuesta[1]['data'][0]['Zona'];
                                $direccionPv = $crm->respuesta[1]['data'][0]['Direcci_n'];
                                $pvs[$idPv]['nombrePv'] = $nombrePv;
                                $pvs[$idPv]['areaPv'] = $areaPv;
                                $pvs[$idPv]['sectorPv'] = $sectorPv;
                                $pvs[$idPv]['zonaPv'] = $zonaPv;
                                $pvs[$idPv]['direccionPv'] = $direccionPv;
                                if (!isset($lineasOrdenadas[$areaPv])) {
                                    $lineasOrdenadas[$areaPv] = [];
                                }
                                if (!isset($lineasOrdenadas[$areaPv][$sectorPv])) {
                                    $lineasOrdenadas[$areaPv][$sectorPv] = [];
                                }
                                $lineaVector = [];
                                $lineaVector['pvId'] = $idPv;
                                $lineaVector['pv'] = $nombrePv;
                                $lineaVector['incluir'] = $incluir;
                                $lineaVector['codigo'] = $codLinea;
                                $lineaVector['prevision'] = $previsionLinea;
                                $lineaVector['direccion'] = $direccionPv;
                                $lineaVector['area'] = $areaPv;
                                $lineaVector['sector'] = $sectorPv;
                                $lineaVector['zona'] = $zonaPv;
                                $lineaVector['observaciones'] = $observacionesLinea;
                                $lineaVector['quitar'] = $quitar;
                                $lineaVector['poner'] = $poner;
                                array_push($lineasOrdenadas[$areaPv][$sectorPv], $lineaVector);
                            } else {
    ?>
                                <script>
                                    document.getElementById('loader-text').textContent = 'Error! Al consltar en la API del CRM los datos del punto de venta de la línea: '.$codLinea;
                                    document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                                    errorApp = true;
                                </script>
                    <?php
                                break;
                            }
                        } else {
                            $nombrePv = $pvs[$idPv]['nombrePv'];
                            $areaPv = $pvs[$idPv]['areaPv'];
                            $sectorPv = $pvs[$idPv]['sectorPv'];
                            $zonaPv = $pvs[$idPv]['zonaPv'];
                            if (!isset($lineasOrdenadas[$areaPv])) {
                                $lineasOrdenadas[$areaPv] = [];
                            }
                            if (!isset($lineasOrdenadas[$areaPv][$sectorPv])) {
                                $lineasOrdenadas[$areaPv][$sectorPv] = [];
                            }
                            $lineaVector = [];
                            $lineaVector['pvId'] = $idPv;
                            $lineaVector['pv'] = $nombrePv;
                            $lineaVector['incluir'] = $incluir;
                            $lineaVector['codigo'] = $codLinea;
                            $lineaVector['prevision'] = $previsionLinea;
                            $lineaVector['direccion'] = $direccionPv;
                            $lineaVector['area'] = $areaPv;
                            $lineaVector['sector'] = $sectorPv;
                            $lineaVector['zona'] = $zonaPv;
                            $lineaVector['observaciones'] = $observacionesLinea;
                            $lineaVector['quitar'] = $quitar;
                            $lineaVector['poner'] = $poner;
                            array_push($lineasOrdenadas[$areaPv][$sectorPv], $lineaVector);
                        }
                    }
                }
                if ($lineasIncluidas) {
                    $otVector = [];
                    $otVector['ot'] = [];
                    $otVector['lineas'] = $lineasOrdenadas;
                    $otVector['ot']['id'] = $idOt;
                    $otVector['ot']['codOt'] = $codOt;
                    $otVector['ot']['nombreOt'] = $nombreOt;
                    $otVector['ot']['cliente'] = $cliente;
                    $otVector['ot']['firma'] = $firma;
                    $otVector['ot']['tipo'] = $tipoOt;
                    $otVector['ot']['contacto'] = $contacto;
                    $otVector['ot']['creacion'] = $creacion;
                    $otVector['ot']['subtipo'] = $subtipo;
                    $otVector['ot']['creadaPor'] = $creadaPor;
                    ?>
                    <script>
                        const data = <?php echo json_encode($otVector, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                        console.log(data);
                    </script>
                <?php
                } else {
                ?>
                    <script>
                        document.getElementById('loader-text').textContent = 'Error! No existen líneas que estén seleccionadas como incluidas en la OT del CRM';
                        document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                        errorApp = true;
                    </script>
                <?php
                }
            } else {
                ?>
                <script>
                    document.getElementById('loader-text').textContent = 'Error! Al consultar las líneas de la OT que estan seleccionadas como incluidas en la OT del CRM';
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

    <script src="http://localhost:8080/js/pdf_informe_ot.js?v=1"></script>

</body>

</html>

<?php
@ob_flush();
flush();
?>
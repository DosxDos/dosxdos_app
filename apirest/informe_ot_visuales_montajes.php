<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe visuales - montajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="https://dosxdos.app.iidos.com/img/logo-red.png" />
    <link rel="stylesheet" href="https://dosxdos.app.iidos.com/css/informe_ot_visuales_montajes.css" />
    <script src="https://dosxdos.app.iidos.com/js/pdfmake.min.js"></script>
    <script src="https://dosxdos.app.iidos.com/js/vfs_fonts.js"></script>
    <script src="https://dosxdos.app.iidos.com/js/informe_ot_visuales_montajes.js"></script>
</head>

<body>
    <div id="loader" class="displayOn">
        <div id="loader-text"></div>
        <div class="dots" id="loader-dots"><span>.</span><span>.</span><span>.</span></div>
    </div>

    <?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('curl.cainfo', '/dev/null');
    set_time_limit(0);
    ini_set('default_socket_timeout', 28800);
    date_default_timezone_set('Atlantic/Canary');

    require_once 'middlewares/jwtMiddleware.php';
    require_once 'clases/crm_clase.php';
    require_once 'utils/funciones_data.php';

    if (isset($_GET['idOt']) && isset($_GET['codOt']) && isset($_GET['tipoOt']) && isset($_GET['cliente']) && isset($_GET['firma']) && isset($_GET['tokenJwt'])) {

        $jwtMiddleware = new JwtMiddleware;
        $jwtMiddleware->verificar();

        $idOt = $_GET['idOt'];
        $codOt = $_GET['codOt'];
        $tipoOt = $_GET['tipoOt'];
        $cliente = $_GET['cliente'];
        $firma = $_GET['firma'];

        if ($idOt && $codOt && $tipoOt && $cliente && $firma) {
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
                

    ?>
                <div>Número de líneas: <?php echo $numLineas ?></div>
                <div>Líneas: <?php print_r($lineas); ?></div>
                <script>
                    loaderOff();
                    scrollToTop();
                </script>
            <?php

            } else {
            ?>
                <script>
                    document.getElementById('loader-text').textContent = 'Error! Al consultar las líneas de la OT con fecha de entrada en el CRM';
                    document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
                </script>
            <?php
            }
        } else {
            ?>
            <script>
                document.getElementById('loader-text').textContent = 'Error! No se han recibido los datos necesarios';
                document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
            </script>
        <?php
        }
    } else {
        ?>
        <script>
            document.getElementById('loader-text').textContent = 'Error! No se han recibido los datos necesarios';
            document.getElementById('loader-dots').innerHTML = '<span class="error">✘</span>';
        </script>
    <?php
    }
    ?>

</body>

</html>
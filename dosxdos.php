<?php

require_once 'config.php';

$idUsuario;
$id;
$clase;
$usuario;
$cod;
$contrasena;
$correo;
$movil;
$nombre;
$apellido;
$imagen;
$activo;
$mensaje = '';

if (!isset($_COOKIE['login'])) {
    header("location: index.html");
}

if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
}

if (isset($_COOKIE['usuario'])) {
    $idUsuario = $_COOKIE['usuario'];
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if ($conexion && $idUsuario) {
    $query = "SELECT * FROM usuarios WHERE id = $idUsuario";
    $result = $conexion->datos($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $clase = $row['clase'];
        $usuario = $row['usuario'];
        $cod = $row['cod'];
        $contrasena = $row['contrasena'];
        $correo = $row['correo'];
        $movil = $row['movil'];
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $imagen = $row['imagen'];
    }
} else {
    $mensaje .= '--Error en la conexión con la base de datos o la obtención del login del usuario: ' . $conexion->error . '--';
}

?>

<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://dosxdos.app.iidos.com/css/cdn_data_tables.css">
    <link rel="stylesheet" href="https://dosxdos.app.iidos.com/css/tailwindmain.css" />
    <link rel="stylesheet" href="https://dosxdos.app.iidos.com/css/index.css" />
    <link rel="icon" type="image/png" href="https://dosxdos.app.iidos.com/img/logoPwa256.png">
    <script src="https://dosxdos.app.iidos.com/js/jquery.js"></script>
    <script src="https://dosxdos.app.iidos.com/js/data_tables.js"></script>
    <script src="https://dosxdos.app.iidos.com/js/cdn_data_tables.js"></script>
    <script src="https://dosxdos.app.iidos.com/js/index_db.js"></script>
    <script>
        let mensajePhp;
        <?php if ($mensaje) {
            echo 'mensajePhp = "' . $mensaje . '"';
        } ?>
    </script>
    <style>
        /* Desktop dropdown menu improvements */
        .desktop-dropdown {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 0.5rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            z-index: 50;
            min-width: 12rem;
            padding: 0.5rem 0;
            border: 1px solid rgba(229, 231, 235, 1);
        }

        .desktop-dropdown-visible {
            display: block;
        }

        .desktop-dropdown a {
            padding: 0.75rem 1rem;
            display: block;
            transition: all 0.2s;
        }

        /* Mobile submenu improvements */
        .mobile-submenu-header {
            position: relative;
            margin-bottom: 0.25rem;
        }

        .mobile-submenu-content {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 0.5rem;
            margin: 0.25rem 0 0.75rem 3.5rem;
        }

        .mobile-submenu-item {
            padding: 0.75rem 1rem;
            display: block;
            color: white;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .mobile-submenu-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body id="body" class="">

    <div id="loader" class="displayOn">
        <span class="loader"></span>
    </div>

    <section id="encabezado" class="w-full bg-white shadow-md fixed top-0 z-30">
        <!-- Main header -->
        <div class="flex items-center justify-between w-full px-6 py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <img src="https://dosxdos.app.iidos.com/img/logo300.png" class="h-16 hidden xl:block" alt="Logo completo" />
                <img src="https://dosxdos.app.iidos.com/img/Isotipo-38.png" class="h-16 xl:hidden" alt="Isotipo" />
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden xl:flex items-center space-x-8">
                <!-- OT Option -->
                <div id="archivos" class="displayOff">
                    <a href="https://dosxdos.app.iidos.com/ot.html" class="flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                        </svg>
                        <span>OT</span>
                    </a>
                </div>

                <!-- Lineas Ruta Option -->
                <div id="lineasIcono" class="displayOff">
                    <a href="https://dosxdos.app.iidos.com/lineas.html" class="flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                            <path d="M9 12h6" />
                            <path d="M9 16h6" />
                        </svg>
                        <span>Líneas Ruta</span>
                    </a>
                </div>

                <!-- Lineas OT Option -->
                <div id="icLineasOt" class="displayOff">
                    <a href="https://dosxdos.app.iidos.com/lineas_ot.html" class="flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12h6" />
                            <path d="M9 16h6" />
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                        </svg>
                        <span>Líneas OT</span>
                    </a>
                </div>

                <!-- Rutas Option -->
                <div id="rutasIcono" class="displayOff">
                    <a href="https://dosxdos.app.iidos.com/rutas.html" class="flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 5c8 0 8 14 16 14" />
                            <circle cx="4" cy="5" r="2" />
                            <circle cx="12" cy="12" r="2" />
                            <circle cx="20" cy="19" r="2" />
                        </svg>
                        <span>Rutas</span>
                    </a>
                </div>

                <!-- Usuarios Option -->
                <div id="usuarios" class="displayOff">
                    <a href="https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios" class="flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Usuarios</span>
                    </a>
                </div>

                <!-- Gastos Dropdown -->
                <div id="gastos" class="displayOff relative">
                    <button class="dropdown-toggle flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200" data-dropdown="gastosDropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <line x1="2" y1="10" x2="22" y2="10" />
                            <line x1="7" y1="15" x2="9" y2="15" />
                            <line x1="15" y1="15" x2="17" y2="15" />
                        </svg>
                        <span>Gastos</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="gastosDropdown" class="desktop-dropdown w-56">
                        <a href="https://dosxdos.app.iidos.com/gastos_rutas.html" class="text-sm text-gray-700 hover:bg-red-600 hover:text-white">
                            Gastos Rutas
                        </a>
                    </div>
                </div>

                <!-- Sincronización Dropdown -->
                <div id="sincronizacion" class="displayOff relative">
                    <button class="dropdown-toggle flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200" data-dropdown="sincronizacionDropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2v6h-6"></path>
                            <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
                            <path d="M3 22v-6h6"></path>
                            <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
                        </svg>
                        <span>Sincronización</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="sincronizacionDropdown" class="desktop-dropdown w-72">
                        <a href="#" class="text-sm text-gray-700 hover:bg-red-600 hover:text-white">
                            Sinc. OT Navision
                        </a>
                        <a href="https://dosxdos.app.iidos.com/apirest/sincronizador2.php" class="text-sm text-gray-700 hover:bg-red-600 hover:text-white">
                            Sinc. Total OT Navision
                        </a>
                    </div>
                </div>

                <!-- Clientes Dropdown -->
                <div id="clientes" class="displayOff relative">
                    <button class="dropdown-toggle flex items-center text-gray-700 hover:text-red-600 transition-colors duration-200" data-dropdown="clientesDropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Clientes</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="clientesDropdown" class="desktop-dropdown w-72">
                        <a href="https://dosxdos.app.iidos.com/clientes_dm.html" class="text-sm text-gray-700 hover:bg-red-600 hover:text-white">
                            Todos los Clientes
                        </a>
                        <a href="https://dosxdos.app.iidos.com/ots_clientes.html" class="text-sm text-gray-700 hover:bg-red-600 hover:text-white">
                            Restricciones Tipos de OT
                        </a>
                        <a href="https://dosxdos.app.iidos.com/firmas_clientes.html" class="text-sm text-gray-700 hover:bg-red-600 hover:text-white">
                            Restricciones Firmas
                        </a>
                    </div>
                </div>

                <!-- Desktop Menu Notifications Bell -->
                <a href="https://dosxdos.app.iidos.com/notificaciones.html" class="relative z-10" id="desktopBellContainer">
                    <img id="bellDesktop" src="https://dosxdos.app.iidos.com/img/bell2.png" class="w-7 text-gray-900 object-contain" />
                    <span id="desktopNotificationCount" class="absolute top-0 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center border hidden"></span>
                </a>

                <!-- Desktop User Menu -->
                <div class="relative group drop-shadow">
                    <button id="userMenuButton" class="group flex items-center gap-3 py-1 pl-1.5 pr-4 rounded-full bg-white border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200" aria-expanded="false">
                        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex items-center justify-center">
                            <img id="imagenUsuario" src="https://dosxdos.app.iidos.com/img/usuario.png" class="w-full h-full object-cover" alt="Profile" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                            <svg class="w-5 h-5 text-gray-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>

                        <span id="nombreUsuario" class="text-gray-700 font-medium text-lg"></span>

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <!-- Desktop Dropdown Menu -->
                    <div id="opcionesUsuario" class="hidden absolute right-0 mt-2 w-max bg-white rounded-lg shadow-lg p-4 z-50">
                        <button id="editarUsuario" class="flex items-center gap-2 w-full mb-4 text-left text-xl text-black hover:bg-red-600/20 rounded-lg transition-colors duration-200 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Editar Usuario
                        </button>
                        <button id="cerrarSesion" class="flex items-center gap-2 w-full text-left text-xl text-red-500 hover:bg-red-600/20 rounded-lg transition-colors duration-200 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Cerrar Sesión
                        </button>
                    </div>
                </div>
            </nav>

            <!-- Mobile Navigation -->
            <div class="xl:hidden flex items-center space-x-2 mx-2">
                <!-- Mobile Bell -->
                <a href="https://dosxdos.app.iidos.com/notificaciones.html" class="relative z-10" id="mobileBellContainer">
                    <img id="bellMobile" src="https://dosxdos.app.iidos.com/img/bell2.png" class="w-8 text-gray-900 object-contain mt-2" />
                    <span id="mobileNotificationCount" class="absolute top-2 right-0 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center border hidden"></span>
                </a>

                <!-- Mobile Menu Button -->
                <button id="menuButton" class="xl:hidden relative z-50 p-2 focus:outline-none">
                    <div class="relative w-8 h-8">
                        <span class="absolute h-1 w-8 bg-gray-900 transition-all duration-300 ease-in-out top-1" id="hamburgerTop"></span>
                        <span class="absolute h-1 w-8 bg-gray-900 transition-all duration-300 ease-in-out top-4" id="hamburgerMiddle"></span>
                        <span class="absolute h-1 w-8 bg-gray-900 transition-all duration-300 ease-in-out top-7" id="hamburgerBottom"></span>
                    </div>
                </button>
            </div>

            <div id="opcionesMenu" class="xl:hidden fixed inset-0 bg-gradient-to-r from-red-500 to-red-600 bg-opacity-95 transform translate-x-full transition-all duration-500 ease-in-out z-40 overflow-hidden backdrop-blur-sm flex flex-col">
                <div class="absolute inset-0 z-0" style="background-image: url('https://dosxdos.app.iidos.com/img/texture-red.svg'); background-size: contain; opacity: 0.7;"></div>
                <!-- User Profile Section -->
                <div class="px-8 py-4 mt-20 relative z-10">
                    <div class="relative flex items-center gap-3 py-1 pl-1.5 pr-4 bg-white shadow-lg rounded-full">
                        <!-- Profile image -->
                        <div class="absolute left-0 w-28 h-28 rounded-full overflow-hidden border-4 border-white bg-gradient-to-br from-red-50 to-white flex items-center justify-center shadow-xl" style="transform: translateX(-15%);">
                            <img id="imagenUsuarioMobile" src="https://dosxdos.app.iidos.com/img/usuario.png" class="w-full h-full object-cover" alt="Profile" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                            <svg class="w-12 h-12 text-gray-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="ml-28 flex items-center py-4">
                            <!-- User name -->
                            <span class="text-black font-medium text-xl">¡Hola,&nbsp;</span>
                            <span id="nombreUsuarioMobile" class="text-black font-medium text-xl"></span>
                            <span class="text-black font-medium text-xl">!</span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Navigation Links -->
                <nav class="px-8 py-6 space-y-2 relative z-10 flex-1 overflow-y-auto custom-scrollbar">
                    <!-- Dynamic menu items will be added here based on user role -->
                </nav>

                <!-- Mobile Menu Footer -->
                <div class="relative z-10">
                    <div class="website-divider-container-734167" style="height: 100px; overflow: visible">
                        <svg xmlns="http://www.w3.org/2000/svg" class="divider-img-734167" viewBox="0 0 1080 137" preserveAspectRatio="none" style="bottom: -20px">
                            <path d="M 0,137 V 59.03716 c 158.97703,52.21241 257.17659,0.48065 375.35967,2.17167 118.18308,1.69101 168.54911,29.1665 243.12679,30.10771 C 693.06415,92.25775 855.93515,29.278599 1080,73.61449 V 137 Z" style="fill: #ffffff"></path>
                            <path d="M 0,10.174557 C 83.419822,8.405668 117.65911,41.78116 204.11379,44.65308 290.56846,47.52499 396.02558,-7.4328 620.04248,94.40134 782.19141,29.627636 825.67279,15.823104 1080,98.55518 V 137 H 0 Z" style="fill: #ffffff; opacity: 0.5"></path>
                            <path d="M 0,45.10182 C 216.27861,-66.146913 327.90348,63.09813 416.42665,63.52904 504.94982,63.95995 530.42054,22.125806 615.37532,25.210412 700.33012,28.295019 790.77619,132.60682 1080,31.125744 V 137 H 0 Z" style="fill: #ffffff; opacity: 0.25"></path>
                        </svg>
                    </div>
                </div>

                <!-- User Actions -->
                <div class="relative z-10 mt-auto px-8 pb-6 bg-white shadow-lg flex justify-center space-x-6 pb-2 rounded-t-xl">
                    <!-- Edit Button -->
                    <button id="editarUsuarioMobile" class="flex items-center justify-center w-14 h-14 bg-white border-2 border-red-500 rounded-full hover:bg-red-100 transition-all duration-300">
                        <svg class="w-8 h-8 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </button>

                    <!-- Logout Button -->
                    <button id="cerrarSesionMobile" class="flex items-center justify-center w-14 h-14 bg-red-600 rounded-full transition-all duration-300">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- <div id="tituloVisible"></div> -->
        <!-- <h1 id="titulo" class="text-2xl sm:text-3xl font-bold mb-2 text-center mt-24 pt-4 displayOn"></h1> -->
    </section>

    <?php
    if (isset($_GET['modulo'])) {
        if ($_GET['modulo'] == 'crearUsuario') {
            require_once 'crear_usuario.php';
        } else if ($_GET['modulo'] == 'editarUsuario') {
            if ($id) {
                require_once 'editar_usuario.php';
            } else {
                $mensaje .= 'Es necesario especificar el id del usuario a editar en las variables de la url';
    ?>
                <meta http-equiv="refresh" content="0; url=dosxdos.php?mensaje=<?php echo $mensaje ?>">
            <?php
            }
        } else if ($_GET['modulo'] == 'usuarios') {
            require_once 'usuarios.php';
        } else if ($_GET['modulo'] == 'archivos') {
            ?>
            <meta http-equiv="refresh" content="0; url=ot.html">
    <?php
        } else {
            if ($clase == 'admon') {
                require_once 'usuarios.php';
            }
        }
    }
    ?>


</body>

<script>
    //ALERTAS AL ELIMINAR
    $(document).ready(function() {
        $(".eliminar").click(function(e) {
            e.preventDefault();
            var res = confirm("Confirma por favor la eliminación");
            if (res == true) {
                var link = $(this).attr("href");
                window.location = link;
            }
        });
    });

    function overfila(id) {
        window.location.href = `https://dosxdos.app.iidos.com/dosxdos.php?modulo=editarUsuario&id=${id}`;
    }

    /*REENVÍO DE FORMULARIOS */

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    /* CONSTANTES DEL DOM */
    const $opcionesMenu = document.getElementById('opcionesMenu'),
        $opcionesUsuario = document.getElementById('opcionesUsuario'),
        $casa = document.getElementById('casa'),
        $usuario = document.getElementById('usuario'),
        $rutas = document.getElementById('rutas'),
        $loader = document.getElementById('loader'),
        $body = document.getElementById('body'),
        buscador = document.getElementById('buscador'),
        $nombreUsuario = document.getElementById('nombreUsuario'),
        $imagenUsuario = document.getElementById('imagenUsuario'),
        $horarios = document.getElementById('horarios'),
        $archivos = document.getElementById('archivos'),
        $usuarios = document.getElementById('usuarios'),
        $rutasIcono = document.getElementById('rutasIcono'),
        $lineasIcono = document.getElementById('lineasIcono'),
        $pv = document.getElementById('pv'),
        $icLineasOt = document.getElementById('icLineasOt'),
        $icClientes = document.getElementById('icClientes'),
        $dm = document.getElementById('dm'),
        $reciclar = document.getElementById('reciclar'),
        $rutasMontador = document.getElementById('rutasMontador'),
        $lineasMontador = document.getElementById('lineasMontador');
    let usuario,
        enviar;

    /* VALIDACIÓN DE FORMULARIOS */

    const expresiones = {
            usuario: /^(?:[a-zA-Z]+|\d+|[a-zA-Z\d]+){4,8}$/,
            contrasena: /^(?:[a-zA-Z\d!@#$%^&*()-+=<>?/\|[\]{}:;,.]+){4,12}$/,
            correo: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
        },

        /* Campos */
        campos = {
            usuario: false,
            codigo: false,
            contrasena: false,
            contrasena2: false,
            correo: false,
            nombre: false,
        };

    const validarCampo = (expresion, input, campo) => {
        if (expresion.test(input)) {
            campos[campo] = true;
        } else {
            campos[campo] = false;
        }
    }

    function trim(cadena) {
        if (cadena != null) {
            return cadena.replace(/\s+/g, '');
        } else {
            return '';
        }
    }

    const validarVacio = (input) => {
        if (trim(input) != '') {
            return false;
        } else {
            return true;
        }
    }

    /* LISTENERS */
    document.addEventListener('DOMContentLoaded', () => {

        //SUBMITS

        if (document.getElementById('crearUsuarioFormulario')) {
            console.log('Formulario de crear usuario reconocido');
            if (document.getElementById('enviar')) {
                enviar = document.getElementById('enviar');
            }
            if (enviar) {
                enviar.addEventListener('click', () => {
                    const formulario = document.getElementById('crearUsuarioFormulario');
                    console.log('Ejecución del listener submit del formulario para crear usuario');
                    loaderOn();
                    let mensaje = '';
                    let formData = new FormData(formulario);
                    window.scrollTo(0, 0);
                    usuario = formData.get('usuario');
                    codigo = formData.get('cod');
                    contrasena = formData.get('contrasena');
                    contrasena2 = formData.get('contrasena2');
                    correo = formData.get('correo');
                    nombre = formData.get('nombre');
                    validarCampo(expresiones.usuario, usuario, 'usuario');
                    const vCodigo = validarVacio(codigo);
                    if (vCodigo) {
                        campos.codigo = false;
                    } else {
                        campos.codigo = true;
                    }
                    validarCampo(expresiones.contrasena, contrasena, 'contrasena');
                    if (contrasena === contrasena2) {
                        campos.contrasena2 = true;
                    }
                    const vCorreo = validarVacio(correo);
                    if (vCorreo) {
                        campos.correo = false;
                    } else {
                        validarCampo(expresiones.correo, correo, 'correo');
                    }
                    const vNombre = validarVacio(nombre);
                    if (vNombre) {
                        campos.nombre = false;
                    } else {
                        campos.nombre = true;
                    }
                    if (!campos.usuario || !campos.codigo || !campos.contrasena || !campos.contrasena2 || !campos.correo || !campos.nombre) {
                        if (!campos.usuario) {
                            mensaje += '-Formato de usuario no válido; en el usuario sólo se permite el uso de letras y dígitos (4 a 8 caracteres sin espacios)-'
                        }
                        if (!campos.codigo) {
                            mensaje += '-Si el usuario es montador o cliente es necesario ingresar el código de NAVISION que pertenece al montador o el cliente. Si el usuario es de tipo oficina o administración es necesario crear un código para comenzar a relacionarlo con NAVISION-'
                        }
                        if (!campos.contrasena) {
                            mensaje += '-Formato de contraseña no válido; en la contraseña sólo se permite el uso de letras, dígitos y caracteres especiales (4 a 12 caracteres sin espacios)-'
                        }
                        if (!campos.contrasena2) {
                            mensaje += '-Las contraseñas no coinciden-'
                        }
                        if (!campos.correo) {
                            mensaje += '-El formato de correo no es válido-'
                        }
                        if (!campos.nombre) {
                            mensaje += '-Es necesario ingresar el nombre del usuario para poder identificarlo en la interfaz de la aplicación-'
                        }
                        $textoMensaje.innerHTML = mensaje;
                        mensajeOn();
                        loaderOff();
                    } else {
                        formulario.submit();
                    }
                })
            }
        }

        if (document.getElementById('editarUsuarioFormulario')) {
            console.log('Formulario de editar usuario reconocido');
            if (document.getElementById('enviar')) {
                enviar = document.getElementById('enviar');
            }
            if (enviar) {
                enviar.addEventListener('click', () => {
                    const formulario = document.getElementById('editarUsuarioFormulario');
                    console.log('Ejecución del listener submit del formulario para editar usuario');
                    loaderOn();
                    let mensaje = '';
                    let formData = new FormData(formulario);
                    window.scrollTo(0, 0);
                    usuario = formData.get('usuario');
                    codigo = formData.get('cod');
                    contrasena = formData.get('contrasena');
                    contrasena2 = formData.get('contrasena2');
                    correo = formData.get('correo');
                    nombre = formData.get('nombre');
                    validarCampo(expresiones.usuario, usuario, 'usuario');
                    const vCodigo = validarVacio(codigo);
                    if (vCodigo) {
                        campos.codigo = false;
                    } else {
                        campos.codigo = true;
                    }
                    validarCampo(expresiones.contrasena, contrasena, 'contrasena');
                    if (contrasena === contrasena2) {
                        campos.contrasena2 = true;
                    }
                    const vCorreo = validarVacio(correo);
                    if (vCorreo) {
                        campos.correo = false;
                    } else {
                        validarCampo(expresiones.correo, correo, 'correo');
                    }
                    const vNombre = validarVacio(nombre);
                    if (vNombre) {
                        campos.nombre = false;
                    } else {
                        campos.nombre = true;
                    }
                    if (!campos.usuario || !campos.codigo || !campos.contrasena || !campos.contrasena2 || !campos.correo || !campos.nombre) {
                        if (!campos.usuario) {
                            mensaje += '-Formato de usuario no válido; en el usuario sólo se permite el uso de letras y dígitos (4 a 8 caracteres sin espacios)-'
                        }
                        if (!campos.codigo) {
                            mensaje += '-Si el usuario es montador o cliente es necesario ingresar el código de NAVISION que pertenece al montador o el cliente. Si el usuario es de tipo oficina o administración es necesario crear un código para comenzar a relacionarlo con NAVISION-'
                        }
                        if (!campos.contrasena) {
                            mensaje += '-Formato de contraseña no válido; en la contraseña sólo se permite el uso de letras, dígitos y caracteres especiales (4 a 12 caracteres sin espacios)-'
                        }
                        if (!campos.contrasena2) {
                            mensaje += '-Las contraseñas no coinciden-'
                        }
                        if (!campos.correo) {
                            mensaje += '-El formato de correo no es válido-'
                        }
                        if (!campos.nombre) {
                            mensaje += '-Es necesario ingresar el nombre del usuario para poder identificarlo en la interfaz de la aplicación-'
                        }
                        $textoMensaje.innerHTML = mensaje;
                        mensajeOn();
                        loaderOff();
                    } else {
                        formulario.submit();
                    }
                })
            }
        }
    });

    function toggleElemento(elementId) {
        const elemento = document.getElementById(elementId);
        elemento.classList.toggle('displayOn');
        elemento.classList.toggle('displayOff');
    }

    /* LOADER */

    function scrollToTop() {
        // Para navegadores modernos
        document.documentElement.scrollTop = 0;
        // Para navegadores antiguos
        document.body.scrollTop = 0;
    }

    /* LOADER */
    function loaderOn() {
        scrollToTop();
        $loader.classList.remove("displayOff");
        $loader.classList.add("displayOn");
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
    }

    function loaderOff() {
        setTimeout(() => {
            $loader.classList.remove("displayOn");
            $loader.classList.add("displayOff");
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
        }, 1000);
    }

    function alerta(mensaje) {
        // Remove existing alert if present
        const existingAlert = document.querySelector("#customAlert");
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create alert container
        const alertContainer = document.createElement("div");
        alertContainer.id = "customAlert";
        alertContainer.className =
            "fixed left-1/2 transform -translate-x-1/2 z-50 transition-all duration-300 ease-in-out w-[95%] md:w-[75%] lg:w-[65%]";
        alertContainer.style.top = "-100px";

        // Create alert content
        const alertContent = document.createElement("div");
        alertContent.className = `
    relative
    bg-white
    border-2 border-red-600
    rounded-lg
    shadow-lg
    p-4 md:p-5
    flex flex-col md:flex-row md:items-center gap-3 md:gap-4
  `;

        // Create close button
        const closeButton = document.createElement("button");
        closeButton.className =
            "absolute -right-3 -top-3 hover:bg-red-400 p-1 bg-red-600 rounded-full p-1.5 transition-colors duration-200";
        closeButton.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
  `;

        // Create main content container for mobile layout
        const contentContainer = document.createElement("div");
        contentContainer.className =
            "flex flex-col md:flex-row items-center gap-3 md:gap-4 flex-1";

        // Create icon with exclamation mark
        const iconContainer = document.createElement("div");
        iconContainer.className = "flex-shrink-0 text-red-600";
        iconContainer.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <circle cx="12" cy="12" r="10" />
  <path d="M12 7v6" />
  <path d="M12 16h0.01" />
</svg>
`;
        // Create message text
        const messageText = document.createElement("p");
        messageText.className = "text-gray-900 text-base md:text-lg flex-1";
        messageText.textContent = mensaje;

        // Assemble the content container
        contentContainer.appendChild(iconContainer);
        contentContainer.appendChild(messageText);

        // Assemble the alert
        alertContent.appendChild(closeButton);
        alertContent.appendChild(contentContainer);
        alertContainer.appendChild(alertContent);
        document.body.appendChild(alertContainer);

        // Slide down animation
        requestAnimationFrame(() => {
            alertContainer.style.top = "56px";
        });

        // Add event listeners
        closeButton.addEventListener("click", () => {
            hideAlert(alertContainer);
        });

        // Hide loader if it exists
        loaderOff();
    }

    function hideAlert(alertContainer) {
        alertContainer.style.top = "-100px";

        setTimeout(() => {
            alertContainer.remove();
        }, 300);
    }

    if (mensajePhp) {
        alerta(mensajePhp);
        scrollToTop();
    }

    /* CERRAR SESIÓN */

    function eliminarCookie(nombre) {
        const tiempoExpiracion = 1; // Segundo 1 de la Época Unix
        document.cookie = `${nombre}=; expires=${new Date(tiempoExpiracion * 1000).toUTCString()}; path=/;`;
    }

    function convertirFecha(fecha) {
        // Separar la fecha y la hora
        const [fechaParte, horaParte] = fecha.split(' ');
        // Separar la fecha en año, mes y día
        const [anio, mes, dia] = fechaParte.split('-');
        // Formatear la fecha al estilo deseado: DD/MM/AAAA HH:MM
        const fechaFormateada = `${dia}/${mes}/${anio}`;
        // Devolver la fecha formateada junto con la hora original
        return `${fechaFormateada} ${horaParte}`;
    }

    const idsGenerados = new Set(); // Utilizamos un conjunto para evitar IDs duplicados

    function generarId(min, max) {
        // Genera un número aleatorio en el rango [min, max]
        const id = Math.floor(Math.random() * (max - min + 1)) + min;

        // Verifica si el ID ya ha sido generado
        if (idsGenerados.has(id)) {
            // Si ya existe, vuelve a generar el ID de forma recursiva
            return generarId(min, max);
        }

        // Agrega el ID generado al conjunto
        idsGenerados.add(id);

        return id;
    }

    function obtenerConfirmacion() {
        return new Promise((resolve, reject) => {
            const respuesta = confirm('¿Estás segur@ que deseas eliminar el archivo del servidor?');
            if (respuesta) {
                resolve(true);
            } else {
                resolve(false);
            }
        });
    }

    /* LOGIN */
    function vLogin() {
        return new Promise((resolve, reject) => {
            const login = localStorage.getItem('login');
            if (login && login !== null) {
                leerDatos('dosxdos', 'usuario')
                    .then(res => {
                        usuario = res[0];
                        resolve(true);
                    })
                    .catch(err => {
                        resolve(false);
                    })
            } else {
                resolve(false);
            }
        })
    }

    async function appOnline() {
        try {
            const login = await vLogin();
            if (!login) {
                window.location.href = "https://dosxdos.app.iidos.com/index.html";
            } else {
                const Arrayusuario = await leerDatos('dosxdos', 'usuario');
                usuario = Arrayusuario[0];
                $nombreUsuario.innerHTML = usuario.nombre;
                if (usuario.imagen != '0') {
                    $imagenUsuario.src = usuario.imagen;
                }

                // Get references to menu items
                const menuItems = {
                    notificaciones: document.getElementById('notificaciones'),
                    horarios: document.getElementById('horarios'),
                    archivos: document.getElementById('archivos'),
                    pv: document.getElementById('pv'),
                    rutasIcono: document.getElementById('rutasIcono'),
                    lineasIcono: document.getElementById('lineasIcono'),
                    icLineasOt: document.getElementById('icLineasOt'),
                    usuarios: document.getElementById('usuarios'),
                    usuariosOficina: document.getElementById('usuariosOficina'),
                    rutasMontador: document.getElementById('rutasMontador'),
                    lineasMontador: document.getElementById('lineasMontador'),
                    historial: document.getElementById('historial'),
                    dm: document.getElementById('dm'),
                    reciclar: document.getElementById('reciclar')
                };

                // Reset all menu items to displayOff
                Object.values(menuItems).forEach(item => {
                    if (item) item.classList.replace('displayOn', 'displayOff');
                });

                // Show menu items based on user role
                if (usuario.clase == 'admon') {
                    ['notificaciones', 'archivos', 'lineasIcono', 'icLineasOt', 'rutasIcono', 'usuarios', 'gastos', 'sincronizacion', 'clientes']
                    .forEach(key => {
                        const item = document.getElementById(key);
                        if (item) item.classList.replace('displayOff', 'displayOn');
                    });
                }

                if (usuario.clase == 'oficina' || usuario.clase == 'diseno' || usuario.clase == 'estudio') {
                    ['notificaciones', 'pv', 'archivos', 'rutasIcono', 'lineasIcono', 'icLineasOt']
                    .forEach(key => {
                        if (menuItems[key]) menuItems[key].classList.replace('displayOff', 'displayOn');

                        // Special handling for users office
                        if (key === 'usuarios') {
                            const usuariosOficina = document.getElementById('usuariosOficina');
                            if (usuariosOficina) usuariosOficina.classList.replace('displayOff', 'displayOn');
                        }
                    });
                }

                if (usuario.clase == 'montador') {
                    ['notificaciones', 'rutasMontador', 'lineasMontador', 'historial']
                    .forEach(key => {
                        if (menuItems[key]) menuItems[key].classList.replace('displayOff', 'displayOn');
                    });
                }

                // Edit user button logic
                const editarUsuario = document.getElementById('editarUsuario');
                const editarUsuarioMobile = document.getElementById('editarUsuarioMobile');

                if (editarUsuario) {
                    editarUsuario.addEventListener('click', e => {
                        if (navigator.onLine) {
                            window.location.href = "https://dosxdos.app.iidos.com/dosxdos.php?modulo=editarUsuario&id=" + usuario.id;
                        } else {
                            alerta('No es posible acceder a las opciones de edición de usuario sin conexión a internet');
                            scrollToTop();
                        }
                    });
                }

                if (editarUsuarioMobile) {
                    editarUsuarioMobile.addEventListener('click', e => {
                        if (navigator.onLine) {
                            window.location.href = "https://dosxdos.app.iidos.com/dosxdos.php?modulo=editarUsuario&id=" + usuario.id;
                        } else {
                            alerta('No es posible acceder a las opciones de edición de usuario sin conexión a internet');
                            scrollToTop();
                        }
                    });
                }

                // Sync notifications
                const sincronizacionDeNotificaciones = await sincronizarNotificaciones();
                if (sincronizacionDeNotificaciones) {
                    await notificar();
                }

                loaderOff();
            }
        } catch (error) {
            mensaje = 'ERROR: ' + error.message;
            console.error(error);
            alerta(mensaje);
            scrollToTop();
            loaderOff();
        }
    }

    appOnline();

    window.addEventListener('pageshow', (e) => {
        console.log('Carga completa de los elementos de la página no fetch');
        let login = localStorage.getItem('login');
        if (!login || login === null) {
            window.location.href = "https://dosxdos.app.iidos.com/index.html";
            loaderOff();
        }
        let mensaje = localStorage.getItem('mensaje');
        if (mensaje && mensaje !== null) {
            alerta(mensaje)
            localStorage.removeItem('mensaje');
        }
    })

    /* TAREAS EN LA INTERMITENCIA DE LA CONEXIÓN */

    window.addEventListener('online', () => {
        console.log('La conexión a Internet se ha recuperado.');
        window.location.reload();
    });

    window.addEventListener('offline', () => {
        console.log('La conexión a Internet se ha perdido.');
    });

    function setupGlobalClickHandler() {
        setupGlobalMenuClosing();
    }

    function setupMenuInteractions() {
        // Desktop User Menu
        const userMenuButton = document.getElementById("userMenuButton");
        const userDropdownMenu = document.getElementById("opcionesUsuario");

        if (userMenuButton && userDropdownMenu) {
            // Remove existing listeners for user menu
            const newUserMenuButton = userMenuButton.cloneNode(true);
            userMenuButton.parentNode.replaceChild(newUserMenuButton, userMenuButton);

            newUserMenuButton.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (userDropdownMenu.classList.contains("hidden")) {
                    userDropdownMenu.classList.remove("hidden");
                } else {
                    userDropdownMenu.classList.add("hidden");
                }
                const isExpanded = !userDropdownMenu.classList.contains("hidden");
                newUserMenuButton.setAttribute("aria-expanded", isExpanded.toString());
            });
        }

        // Mobile Menu Toggle
        const menuButton = document.getElementById("menuButton");
        const mobileMenu = document.getElementById("opcionesMenu");

        if (menuButton && mobileMenu) {
            // Remove existing listeners for mobile menu
            const newMenuButton = menuButton.cloneNode(true);
            menuButton.parentNode.replaceChild(newMenuButton, menuButton);

            // Get fresh references after DOM changes
            const freshMenuButton = document.getElementById("menuButton");
            // Get hamburger icon elements
            const hamburgerTop = document.getElementById("hamburgerTop");
            const hamburgerMiddle = document.getElementById("hamburgerMiddle");
            const hamburgerBottom = document.getElementById("hamburgerBottom");

            freshMenuButton.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();

                // First, remove the displayOff class if it exists
                if (mobileMenu.classList.contains("displayOff")) {
                    mobileMenu.classList.remove("displayOff");
                }

                // Then toggle the transform class for the slide animation
                mobileMenu.classList.toggle("translate-x-full");

                // Toggle body overflow
                document.body.classList.toggle("overflow-hidden");

                // Hamburger animation
                const isOpen = !mobileMenu.classList.contains("translate-x-full");

                if (isOpen) {
                    // Menu is opening - change hamburger to X
                    document.getElementById("hamburgerTop").style.transform = "rotate(45deg) translate(10px, 7px)";
                    document.getElementById("hamburgerMiddle").style.opacity = "0";
                    document.getElementById("hamburgerBottom").style.transform = "rotate(-45deg) translate(10px, -7px)";

                    document.getElementById("hamburgerTop").classList.remove("bg-gray-900");
                    document.getElementById("hamburgerMiddle").classList.remove("bg-gray-900");
                    document.getElementById("hamburgerBottom").classList.remove("bg-gray-900");

                    document.getElementById("hamburgerTop").classList.add("bg-white");
                    document.getElementById("hamburgerMiddle").classList.add("bg-white");
                    document.getElementById("hamburgerBottom").classList.add("bg-white");
                } else {
                    // Menu is closing - change X back to hamburger
                    document.getElementById("hamburgerTop").style.transform = "";
                    document.getElementById("hamburgerMiddle").style.opacity = "1";
                    document.getElementById("hamburgerBottom").style.transform = "";

                    document.getElementById("hamburgerTop").classList.remove("bg-white");
                    document.getElementById("hamburgerMiddle").classList.remove("bg-white");
                    document.getElementById("hamburgerBottom").classList.remove("bg-white");

                    document.getElementById("hamburgerTop").classList.add("bg-gray-900");
                    document.getElementById("hamburgerMiddle").classList.add("bg-gray-900");
                    document.getElementById("hamburgerBottom").classList.add("bg-gray-900");
                }
            });
        }

        // Mobile Menu Edit and Logout Buttons
        const editUserMobile = document.getElementById("editarUsuarioMobile");
        const logoutMobile = document.getElementById("cerrarSesionMobile");

        if (editUserMobile) {
            // Remove existing listeners
            const newEditUserMobile = editUserMobile.cloneNode(true);
            editUserMobile.parentNode.replaceChild(newEditUserMobile, editUserMobile);

            newEditUserMobile.addEventListener("click", () => {
                if (navigator.onLine && usuario) {
                    window.location.href = `https://dosxdos.app.iidos.com/dosxdos.php?modulo=editarUsuario&id=${usuario.id}`;
                } else {
                    alerta('No es posible acceder a las opciones de edición de usuario sin conexión a internet');
                }
            });
        }

        if (logoutMobile) {
            // Remove existing listeners
            const newLogoutMobile = logoutMobile.cloneNode(true);
            logoutMobile.parentNode.replaceChild(newLogoutMobile, logoutMobile);

            newLogoutMobile.addEventListener("click", () => {
                if (typeof cerrarSesion === 'function') {
                    cerrarSesion();
                } else if (typeof cerrarSesions === 'function') {
                    cerrarSesions();
                }
            });
        }
    }

    function setupGlobalMenuClosing() {
        document.addEventListener("click", (e) => {
            const userMenuButton = document.getElementById("userMenuButton");
            const userDropdownMenu = document.getElementById("opcionesUsuario");
            const menuButton = document.getElementById("menuButton");
            const mobileMenu = document.getElementById("opcionesMenu");

            // Close desktop dropdown if clicking outside
            if (userDropdownMenu && userMenuButton &&
                !userMenuButton.contains(e.target) &&
                !userDropdownMenu.contains(e.target) &&
                !userDropdownMenu.classList.contains("hidden")) {
                userDropdownMenu.classList.add("hidden");
                userMenuButton.setAttribute("aria-expanded", "false");
            }

            // Close mobile menu if clicking outside
            if (mobileMenu && menuButton &&
                !mobileMenu.contains(e.target) &&
                !menuButton.contains(e.target) &&
                !mobileMenu.classList.contains("translate-x-full")) {
                menuButton.click();
            }
        });

        // Handle escape key
        document.addEventListener("keydown", (e) => {
            const userDropdownMenu = document.getElementById("opcionesUsuario");
            const userMenuButton = document.getElementById("userMenuButton");
            const mobileMenu = document.getElementById("opcionesMenu");
            const menuButton = document.getElementById("menuButton");

            if (e.key === "Escape") {
                // Close desktop dropdown
                if (userDropdownMenu && !userDropdownMenu.classList.contains("hidden")) {
                    userDropdownMenu.classList.add("hidden");
                    userMenuButton?.setAttribute("aria-expanded", "false");
                }

                // Close mobile menu
                if (mobileMenu && !mobileMenu.classList.contains("translate-x-full")) {
                    menuButton.click();
                }
            }
        });
    }

    // POPULATE MOBILE MENU WITH NAVIGATION ITEMS
    function populateMobileMenu() {
        const mobileMenuNav = document.querySelector("#opcionesMenu nav");

        if (!mobileMenuNav) return;

        // Clear existing content
        mobileMenuNav.innerHTML = '';

        // Helper function to create mobile menu items
        function createMobileMenuItem(id, href, iconPath, text) {
            // Special case for notifications - always show it for all user types
            const forceShow = (id === "notificaciones" && usuario && usuario.clase !== 'cliente');

            // For notifications, don't check for DOM element existence
            const element = forceShow ? {
                classList: {
                    contains: () => false
                }
            } : document.getElementById(id);

            if (forceShow || (element && !element.classList.contains('displayOff'))) {
                const link = document.createElement('a');
                link.href = href;
                link.className = "z-10 flex items-center px-6 py-2 text-white bg-red-600 bg-opacity-60 hover:bg-white/20 rounded-xl transition-all duration-300 shadow-xl group backdrop-blur-lg";
                link.innerHTML = `
        <div class="flex items-center justify-center w-14 h-14 bg-white/20 rounded-xl mr-4 group-hover:bg-white/30 transition-all">
          ${iconPath}
        </div>
        <span class="text-lg font-semibold tracking-wide">${text}</span>
      `;
                mobileMenuNav.appendChild(link);
            }
        }

        // Helper function to create mobile submenu items
        function createMobileSubmenu(id, text, iconPath, items) {
            if (usuario && usuario.clase === 'admon') {
                const submenuContainer = document.createElement('div');
                submenuContainer.className = "mb-4";

                // Create submenu header
                const submenuHeader = document.createElement('div');
                submenuHeader.className = "z-10 flex items-center px-6 py-2 text-white bg-red-600 bg-opacity-60 hover:bg-red-500/60 rounded-xl transition-all duration-300 shadow-xl mobile-submenu-header cursor-pointer";
                submenuHeader.innerHTML = `
            <div class="flex items-center justify-center w-14 h-14 bg-white/20 rounded-xl mr-4 transition-all">
                ${iconPath}
            </div>
            <span class="text-lg font-semibold tracking-wide flex-1">${text}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white transition-transform duration-300 submenu-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        `;

                // Create submenu content
                const submenuContent = document.createElement('div');
                submenuContent.className = "hidden mobile-submenu-content";

                // Add submenu items
                items.forEach(item => {
                    const submenuItem = document.createElement('a');
                    submenuItem.href = item.url;
                    submenuItem.className = "mobile-submenu-item";
                    submenuItem.textContent = item.text;
                    submenuContent.appendChild(submenuItem);
                });

                // Assemble submenu
                submenuContainer.appendChild(submenuHeader);
                submenuContainer.appendChild(submenuContent);
                mobileMenuNav.appendChild(submenuContainer);

                // Add toggle functionality
                submenuHeader.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const toggle = this.querySelector('.submenu-toggle');
                    const content = this.nextElementSibling;

                    // Toggle this submenu
                    content.classList.toggle('hidden');
                    toggle.classList.toggle('rotate-180');

                    // Close other submenus
                    document.querySelectorAll('.mobile-submenu-header').forEach(header => {
                        if (header !== this) {
                            header.querySelector('.submenu-toggle').classList.remove('rotate-180');
                            header.nextElementSibling.classList.add('hidden');
                        }
                    });
                });
            }
        }

        // Add menu items based on user role
        if (usuario && usuario.clase !== 'cliente') {
            createMobileMenuItem(
                "notificaciones",
                "https://dosxdos.app.iidos.com/notificaciones.html",
                `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 8a6 6 0 0 0-12 0v5a6 6 0 0 1-2 4h16a6 6 0 0 1-2-4V8"></path>
                                <line x1="12" y1="22" x2="12" y2="22"></line>
                                <!-- Small bell clapper -->
                            </svg>`,
                "Notificaciones"
            );
        }

        createMobileMenuItem(
            "archivos",
            "https://dosxdos.app.iidos.com/ot.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
      <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
    </svg>`,
            "OT"
        );

        createMobileMenuItem(
            "pv",
            "https://dosxdos.app.iidos.com/pv.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
      <polyline points="9 22 9 12 15 12 15 22"></polyline>
    </svg>`,
            "PV"
        );

        createMobileMenuItem(
            "rutasIcono",
            "https://dosxdos.app.iidos.com/rutas.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M4 5c8 0 8 14 16 14"/>
      <circle cx="4" cy="5" r="2"/>
      <circle cx="12" cy="12" r="2"/>
      <circle cx="20" cy="19" r="2"/>
    </svg>`,
            "Rutas"
        );

        createMobileMenuItem(
            "lineasIcono",
            "https://dosxdos.app.iidos.com/lineas.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
      <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
      <path d="M9 12h6"/>
      <path d="M9 16h6"/>
    </svg>`,
            "Líneas"
        );

        createMobileMenuItem(
            "icLineasOt",
            "https://dosxdos.app.iidos.com/lineas_ot.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M9 12h6"/>
      <path d="M9 16h6"/>
      <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
      <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
    </svg>`,
            "Líneas OT"
        );

        createMobileMenuItem(
            "usuarios",
            "https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
      <circle cx="9" cy="7" r="4"></circle>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
    </svg>`,
            "Usuarios"
        );

        createMobileMenuItem(
            "usuariosOficina",
            "https://dosxdos.app.iidos.com/usuarios_oficina.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
      <circle cx="9" cy="7" r="4"></circle>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
    </svg>`,
            "Usuarios Oficina"
        );

        createMobileMenuItem(
            "rutasMontador",
            "https://dosxdos.app.iidos.com/rutas_montador.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M4 5c8 0 8 14 16 14"/>
      <circle cx="4" cy="5" r="2"/>
      <circle cx="12" cy="12" r="2"/>
      <circle cx="20" cy="19" r="2"/>
    </svg>`,
            "Rutas"
        );

        createMobileMenuItem(
            "lineasMontador",
            "https://dosxdos.app.iidos.com/ruta_montador.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
      <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
      <path d="M9 12h6"/>
      <path d="M9 16h6"/>
    </svg>`,
            "Líneas"
        );

        createMobileMenuItem(
            "dm",
            "https://dosxdos.app.iidos.com/dm.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
      <line x1="3" y1="9" x2="21" y2="9"/>
      <line x1="9" y1="21" x2="9" y2="9"/>
    </svg>`,
            "DM"
        );

        createMobileMenuItem(
            "reciclar",
            "https://dosxdos.app.iidos.com/reciclar.html",
            `<svg class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="3 6 5 6 21 6"></polyline>
      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
      <line x1="10" y1="11" x2="10" y2="17"></line>
      <line x1="14" y1="11" x2="14" y2="17"></line>
    </svg>`,
            "Reciclar"
        );

        createMobileMenuItem(
            "historial",
            "https://dosxdos.app.iidos.com/historial_montador.html",
            `       <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>`,
            "Historial"
        );

        if (usuario && usuario.clase === 'admon') {
            createMobileSubmenu(
                "gastos-mobile",
                "Gastos",
                `<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white group-hover:text-gray-900 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="5" width="20" height="14" rx="2" />
            <line x1="2" y1="10" x2="22" y2="10" />
            <line x1="7" y1="15" x2="9" y2="15" />
            <line x1="15" y1="15" x2="17" y2="15" />
        </svg>`,
                [{
                    text: "Gastos Rutas",
                    url: "https://dosxdos.app.iidos.com/gastos_rutas.html"
                }]
            );

            // Add Sincronización Submenu for admin
            createMobileSubmenu(
                "sync-mobile",
                "Sincronización",
                `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 2v6h-6"></path>
    <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
    <path d="M3 22v-6h6"></path>
    <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
</svg>`,
                [{
                        text: "Sinc. OT Navision",
                        url: "#"
                    },
                    {
                        text: "Sinc. Total OT Navision",
                        url: "https://dosxdos.app.iidos.com/apirest/sincronizador2.php"
                    }
                ]
            );

            // Add Clientes Submenu for admin
            createMobileSubmenu(
                "clientes-mobile",
                "Clientes",
                `<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
  <circle cx="9" cy="7" r="4"></circle>
  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
</svg>`,
                [{
                        text: "Todos los Clientes",
                        url: "https://dosxdos.app.iidos.com/clientes_dm.html"
                    },
                    {
                        text: "Restricciones Tipos de OT",
                        url: "https://dosxdos.app.iidos.com/ots_clientes.html"
                    },
                    {
                        text: "Restricciones Firmas",
                        url: "https://dosxdos.app.iidos.com/firmas_clientes.html"
                    }
                ]
            );
        }
    }


    // INITIALIZE MENU SYSTEM
    function initializeMenuSystem() {
        // Setup the menu interactions
        setupMenuInteractions();

        // Setup global click handler
        setupGlobalClickHandler();

        // Populate mobile menu
        populateMobileMenu();

        // Update mobile username
        const nombreUsuarioMobile = document.getElementById('nombreUsuarioMobile');
        if (nombreUsuarioMobile && usuario && usuario.nombre) {
            nombreUsuarioMobile.textContent = usuario.nombre;
        }

        // Update mobile user image
        const imagenUsuarioMobile = document.getElementById('imagenUsuarioMobile');
        if (imagenUsuarioMobile && usuario && usuario.imagen && usuario.imagen !== '0') {
            imagenUsuarioMobile.src = usuario.imagen;
        }
    }

    // Run after appOnline has initialized usuario
    document.addEventListener('DOMContentLoaded', function() {
        const checkUserInterval = setInterval(function() {
            if (typeof usuario !== 'undefined' && usuario) {
                initializeMenuSystem();
                clearInterval(checkUserInterval);
            }
        }, 100);

        // Safety timeout after 5 seconds
        setTimeout(function() {
            clearInterval(checkUserInterval);
        }, 5000);
    });

    // Function to toggle menu with transform instead of display
    function toggleElementoModern(elementId) {
        const elemento = document.getElementById(elementId);
        if (elemento) {
            if (elementId === 'opcionesMenu') {
                elemento.classList.toggle('translate-x-full');
                document.body.classList.toggle('overflow-hidden');
            } else if (elementId === 'opcionesUsuario') {
                elemento.classList.toggle('hidden');
            }
        }
    }

    // Override the old toggle function to use the new one
    const originalToggleElemento = toggleElemento;
    window.toggleElemento = function(elementId) {
        if (elementId === 'opcionesMenu' || elementId === 'opcionesUsuario') {
            toggleElementoModern(elementId);
        } else {
            originalToggleElemento(elementId);
        }
    };

    // Make sure the menu system initializes properly
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM fully loaded - initializing menu system");

        // First try to setup immediately if user data is available
        if (typeof usuario !== 'undefined' && usuario) {
            initializeMenuSystem();
        }

        // Fall back to interval check
        const checkUserInterval = setInterval(function() {
            if (typeof usuario !== 'undefined' && usuario) {
                initializeMenuSystem();
                clearInterval(checkUserInterval);
                console.log("Menu system initialized through interval check");
            }
        }, 100);

        // Safety timeout after 5 seconds
        setTimeout(function() {
            clearInterval(checkUserInterval);

            // As a last resort, try one more time
            if (typeof usuario !== 'undefined' && usuario) {
                initializeMenuSystem();
                console.log("Menu system initialized through timeout check");
            } else {
                console.warn("Failed to initialize menu system - user data not available");
                // Try to initialize anyway
                setupMenuInteractions();
                setupGlobalMenuClosing();
                populateMobileMenu();
            }
        }, 5000);
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Direct reference to logout buttons
        const logoutDesktop = document.getElementById('cerrarSesion');
        const logoutMobile = document.getElementById('cerrarSesionMobile');

        // Check if elements exist to avoid null reference errors
        if (logoutDesktop) {
            logoutDesktop.addEventListener('click', function() {
                if (typeof cerrarSesion === 'function') {
                    cerrarSesion();
                } else if (typeof cerrarSesions === 'function') {
                    cerrarSesions();
                } else {
                    // Fallback if neither function is available
                    handleLogout();
                }
            });
        }

        if (logoutMobile) {
            logoutMobile.addEventListener('click', function() {
                if (typeof cerrarSesion === 'function') {
                    cerrarSesion();
                } else if (typeof cerrarSesions === 'function') {
                    cerrarSesions();
                } else {
                    // Fallback if neither function is available
                    handleLogout();
                }
            });
        }

        // Fallback logout function
        function handleLogout() {
            loaderOn();
            try {
                // Basic logout functionality
                localStorage.removeItem('login');
                localStorage.removeItem('usuario');

                // Delete cookies
                document.cookie = "login=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

                // Redirect to login page
                window.location.href = "https://dosxdos.app.iidos.com/index.html";
            } catch (error) {
                console.error("Error during logout:", error);
                loaderOff();
                alerta("Error al cerrar sesión: " + error.message);
            }
        }
    });

    // Setup dropdown toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Set up desktop dropdown toggles
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const dropdownId = this.getAttribute('data-dropdown');
                const dropdown = document.getElementById(dropdownId);

                // Close all other dropdowns
                document.querySelectorAll('.desktop-dropdown').forEach(dd => {
                    if (dd.id !== dropdownId) {
                        dd.classList.remove('desktop-dropdown-visible');
                    }
                });

                // Toggle arrow rotation
                document.querySelectorAll('.dropdown-toggle svg:last-child').forEach(arrow => {
                    if (arrow.parentElement === this) {
                        arrow.classList.toggle('rotate-180');
                    } else {
                        arrow.classList.remove('rotate-180');
                    }
                });

                // Toggle current dropdown
                dropdown.classList.toggle('desktop-dropdown-visible');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-toggle') && !e.target.closest('.desktop-dropdown')) {
                document.querySelectorAll('.desktop-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('desktop-dropdown-visible');
                });

                document.querySelectorAll('.dropdown-toggle svg:last-child').forEach(arrow => {
                    arrow.classList.remove('rotate-180');
                });
            }
        });
    });
</script>
<script src="https://dosxdos.app.iidos.com/js/notificaciones.js"></script>
<script src="https://dosxdos.app.iidos.com/js/loadFirebase.js"></script>

</html>
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
                <!-- Navigation items will be dynamically created by createDesktopNavigation() -->

                <!-- Desktop Menu Notifications Bell -->
                <a href="https://dosxdos.app.iidos.com/notificaciones.html" class="relative z-10" id="desktopBellContainer">
                    <img id="bellDesktop" src="" class="text-gray-900 object-contain" />
                    <span id="desktopNotificationCount"
                        class="absolute top-0 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center border hidden"></span>
                </a>

                <!-- Desktop User Menu -->
                <div class="relative group drop-shadow">
                    <button id="userMenuButton"
                        class="group flex items-center gap-3 py-1 pl-1.5 pr-4 rounded-full bg-white border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200"
                        aria-expanded="false">
                        <div
                            class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex items-center justify-center">
                            <img id="imagenUsuarioDesktop" src="https://dosxdos.app.iidos.com/img/usuario.png"
                                class="w-full h-full object-cover" alt="Profile"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                            <svg class="w-5 h-5 text-gray-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>

                        <span id="nombreUsuarioDesktop" class="text-gray-700 font-medium text-lg"></span>

                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:rotate-180"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <!-- Desktop Dropdown Menu -->
                    <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-max bg-white rounded-lg shadow-lg p-4 z-50">
                        <button id="editarUsuarioDesktop"
                            class="flex items-center gap-2 w-full mb-4 text-left text-xl text-black hover:bg-red-600/20 rounded-lg transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Editar Usuario
                        </button>
                        <button id="cerrarSesionDesktop"
                            class="flex items-center gap-2 w-full text-left text-xl text-red-500 hover:bg-red-600/20 rounded-lg transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                    <img id="bellMobile" src="https://dosxdos.app.iidos.com/img/bell2.png"
                        class="w-8 text-gray-900 object-contain mt-2" />
                    <span id="mobileNotificationCount"
                        class="absolute top-2 right-0 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center border hidden"></span>
                </a>

                <!-- Mobile Menu Button -->
                <button id="menuButton" class="xl:hidden relative z-50 p-2 focus:outline-none">
                    <div class="relative w-8 h-8">
                        <span class="absolute h-1 w-8 bg-gray-900 transition-all duration-300 ease-in-out top-1"
                            id="hamburgerTop"></span>
                        <span class="absolute h-1 w-8 bg-gray-900 transition-all duration-300 ease-in-out top-4"
                            id="hamburgerMiddle"></span>
                        <span class="absolute h-1 w-8 bg-gray-900 transition-all duration-300 ease-in-out top-7"
                            id="hamburgerBottom"></span>
                    </div>
                </button>
            </div>

        </div>

        <div id="opcionesMenu"
            class="xl:hidden fixed inset-0 bg-gradient-to-r from-red-500 to-red-600 bg-opacity-95 transform translate-x-full transition-all duration-500 ease-in-out z-40 overflow-hidden backdrop-blur-sm flex flex-col">
            <div class="absolute inset-0 z-0"
                style="background-image: url('https://dosxdos.app.iidos.com/img/texture-red.svg'); background-size: contain; opacity: 0.7;">
            </div>
            <!-- User Profile Section -->
            <div class="px-8 py-4 mt-16 relative z-10">
                <div class="relative flex items-center gap-3 py-1 pl-1.5 pr-4 bg-white shadow-lg rounded-full">
                    <!-- Profile image -->
                    <div
                        class="absolute left-0 w-28 h-28 rounded-full overflow-hidden border-4 border-white bg-gradient-to-br from-red-50 to-white flex items-center justify-center shadow-xl"
                        style="transform: translateX(-15%);">
                        <img id="imagenUsuarioMobile" src="https://dosxdos.app.iidos.com/img/usuario.png"
                            class="w-full h-full object-cover" alt="Profile"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                        <svg class="w-12 h-12 text-gray-400 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
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
            <nav class="px-8 pt-3 space-y-2 relative z-10 flex-1 overflow-y-auto custom-scrollbar mt-2">
                <!-- Dynamic menu items will be added here based on user role -->
            </nav>

            <!-- Mobile Menu Footer -->
            <div class="relative z-10">
                <div class="website-divider-container-734167" style="height: 100px; overflow: visible">
                    <svg xmlns="http://www.w3.org/2000/svg" class="divider-img-734167" viewBox="0 0 1080 137"
                        preserveAspectRatio="none" style="bottom: -20px">
                        <path
                            d="M 0,137 V 59.03716 c 158.97703,52.21241 257.17659,0.48065 375.35967,2.17167 118.18308,1.69101 168.54911,29.1665 243.12679,30.10771 C 693.06415,92.25775 855.93515,29.278599 1080,73.61449 V 137 Z"
                            style="fill: #ffffff"></path>
                        <path
                            d="M 0,10.174557 C 83.419822,8.405668 117.65911,41.78116 204.11379,44.65308 290.56846,47.52499 396.02558,-7.4328 620.04248,94.40134 782.19141,29.627636 825.67279,15.823104 1080,98.55518 V 137 H 0 Z"
                            style="fill: #ffffff; opacity: 0.5"></path>
                        <path
                            d="M 0,45.10182 C 216.27861,-66.146913 327.90348,63.09813 416.42665,63.52904 504.94982,63.95995 530.42054,22.125806 615.37532,25.210412 700.33012,28.295019 790.77619,132.60682 1080,31.125744 V 137 H 0 Z"
                            style="fill: #ffffff; opacity: 0.25"></path>
                    </svg>
                </div>
            </div>

            <!-- User Actions -->
            <div class="relative z-10 mt-auto px-8 pb-6 bg-white shadow-lg flex justify-center space-x-6 pb-2 rounded-t-xl">
                <!-- Edit Button -->
                <button id="editarUsuarioMobile"
                    class="flex items-center justify-center w-14 h-14 bg-white border-2 border-red-500 rounded-full hover:bg-red-100 transition-all duration-300">
                    <svg class="w-8 h-8 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </button>

                <!-- Logout Button -->
                <button id="cerrarSesionMobile"
                    class="flex items-center justify-center w-14 h-14 bg-red-600 rounded-full transition-all duration-300">
                    <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </button>
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
            e.preventDefault(); // Prevent default behavior

            let link = $(this).attr("href");

            // Open custom modal instead of default confirm()
            $("#customModal").addClass("open");

            // Handle confirm button inside the custom modal
            $("#confirmDelete").off("click").on("click", function() {
                window.location.href = link; // Redirect to deletion URL
            });

            // Handle cancel button
            $("#cancelDelete").off("click").on("click", function() {
                $("#customModal").removeClass("open");
            });
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
        $pv = document.getElementById('pv'),
        $icLineasOt = document.getElementById('icLineasOt'),
        $icClientes = document.getElementById('icClientes'),
        $dm = document.getElementById('dm'),
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
                        alerta(mensaje)
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
                        alerta(mensaje)
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

                // Dynamically create navigation based on user role
                createDesktopNavigation(usuario.clase);
                // Setup desktop and mobile navigation
                setupMenu(usuario.clase);

                // Setup menu interactions
                setupMenuInteractions();
                setupUserActions();
                setupGlobalMenuClosing();

                updateUserDisplay();

                // Update the user information in the new UI elements
                const nombreUsuarioDesktop = document.getElementById(
                    "nombreUsuarioDesktop"
                );
                const nombreUsuarioMobile = document.getElementById(
                    "nombreUsuarioMobile"
                );
                const imagenUsuarioDesktop = document.getElementById(
                    "imagenUsuarioDesktop"
                );
                const imagenUsuarioMobile = document.getElementById(
                    "imagenUsuarioMobile"
                );

                if (nombreUsuarioDesktop)
                    nombreUsuarioDesktop.textContent = usuario.nombre;
                if (nombreUsuarioMobile)
                    nombreUsuarioMobile.textContent = usuario.nombre;

                if (usuario.imagen !== "0") {
                    if (imagenUsuarioDesktop) imagenUsuarioDesktop.src = usuario.imagen;
                    if (imagenUsuarioMobile) imagenUsuarioMobile.src = usuario.imagen;
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
</script>

<script src="https://dosxdos.app.iidos.com/js/notificaciones.js"></script>
<script src="https://dosxdos.app.iidos.com/js/loadFirebase.js"></script>
<script src="https://dosxdos.app.iidos.com/js/navigation.js"></script>

</html>
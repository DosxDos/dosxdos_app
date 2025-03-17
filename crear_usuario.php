<?php

if (!isset($_COOKIE['login'])) {
    header("location: index.html");
}

if ((isset($_REQUEST["crearUsuario"]))) {
    require_once "config.php";
    if (!$conexion) {
        header("location: dosxdos.php?modulo=crearUsuario&mensaje=Error de conexión a la base de datos: $conexion->error");
    }
    $usuario = $conexion->sanitizar($_REQUEST["usuario"]);
    $clase = $conexion->sanitizar($_REQUEST["clase"]);
    $cod = $conexion->sanitizar($_REQUEST["cod"]);
    /*$clave = md5($clave);*/
    $contrasena = $conexion->sanitizar($_REQUEST["contrasena"]);
    $correo = $conexion->sanitizar($_REQUEST["correo"]);
    $movil = $conexion->sanitizar($_REQUEST["movil"]);
    $nombre = $conexion->sanitizar($_REQUEST["nombre"]);
    $apellido = $conexion->sanitizar($_REQUEST["apellido"]);
    $nombreImagen;
    if ($_FILES["imagen"]["name"][0]) {
        $tipo = explode('/', $conexion->sanitizar($_FILES["imagen"]["type"][0]))[0];
        $extension = explode('/', $conexion->sanitizar($_FILES["imagen"]["type"][0]))[1];
    }
    ($_FILES["imagen"]["name"][0]) ? $imagen = uniqid() . '_' . $tipo . '_' . $extension . '_' . $conexion->sanitizar($_FILES["imagen"]["name"][0]) : $nombreImagen = 0;
    if ($_FILES["imagen"]["name"][0]) {
        $nombreImagen = 'img_usuarios/' . $imagen;
    }
    $query = "INSERT INTO usuarios (usuario, cod, contrasena, clase, correo, movil, nombre, apellido, imagen) VALUES (\"$usuario\", \"$cod\", \"$contrasena\", \"$clase\", \"$correo\", \"$movil\", \"$nombre\", \"$apellido\", \"$nombreImagen\")";
    $result = $conexion->datosPost($query);
    if ($result) {
        if ($imagen) {
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"][0], "img_usuarios/" . $imagen)) {
                header("location: dosxdos.php?modulo=crearUsuario&mensaje=El usuario se ha creado exitosamente");
            } else {
                header("location: dosxdos.php?modulo=crearUsuario&mensaje=El usuario se ha creado exitosamente, pero no ha sido posible guardar la imagen");
            }
        } else {
            header("location: dosxdos.php?modulo=crearUsuario&mensaje=El usuario se ha creado exitosamente");
        }
    } else {
        $error = $conexion->error;
        header("location: dosxdos.php?modulo=crearUsuario&mensaje=Error de base de datos, el usuario no ha sido creado. Por favor verifica si el usuario ya existe - $error");
    }
}

?>

<section id="contenido" class="displayOn flex justify-center items-start pt-32 pb-12 bg-gray-100 min-h-screen">
    <div class="w-full max-w-6xl px-4">
        <!-- Page Header -->
        <div class="rounded-xl shadow-lg overflow-hidden relative mb-6">
            <div class="absolute inset-0" style="background-image: url('https://dosxdos.app.iidos.com/img/texture-red.svg'); background-size: contain;"></div>
            <div class="relative p-10 flex gap-4 items-center text-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8">
                    <circle cx="9" cy="7" r="4" fill="none" stroke="white" stroke-width="2" />
                    <path d="M3 19c0-3.314 2.686-6 6-6 3.314 0 6 2.686 6 6" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" />
                    <line x1="19" y1="8" x2="19" y2="16" stroke="white" stroke-width="2" stroke-linecap="round" />
                    <line x1="15" y1="12" x2="23" y2="12" stroke="white" stroke-width="2" stroke-linecap="round" />
                </svg>
                <h1 class="text-2xl font-bold mb-2 md:mb-0">Crear Usuario</h1>
            </div>
        </div>

        <!-- Improved Volver Button -->
        <div class="mb-6">
            <a href="https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios"
                class="inline-flex items-center gap-2 text-red-600 hover:text-red-700 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">Volver</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 mb-8">
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Información del Usuario</h2>
                    <p class="text-gray-600 mt-1">Complete todos los campos requeridos (marcados con *)</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="relative h-24 w-24 mx-auto sm:mx-0 bg-gray-100 rounded-full overflow-hidden border-4 border-white shadow-md">
                        <img src="https://dosxdos.app.iidos.com/img/usuario.png" id="imagenPerfil" class="w-full h-full object-cover" alt="Foto de perfil">
                    </div>
                </div>
            </div>

            <form action="crear_usuario.php" method="post" id="crearUsuarioFormulario" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Left Column -->
                <div class="space-y-6 lg:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre<span class="text-red-600">*</span></label>
                            <input type="text" name="nombre" id="nombre"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                        </div>

                        <!-- Apellido -->
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                            <input type="text" name="apellido" id="apellido"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Usuario -->
                        <div>
                            <label for="usuario" class="block text-sm font-medium text-gray-700 mb-1">Usuario<span class="text-red-600">*</span></label>
                            <input type="text" name="usuario" id="usuario" maxlength="8"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                            <p class="text-xs text-gray-500 mt-1">Máximo 8 caracteres</p>
                        </div>

                        <!-- Código -->
                        <div>
                            <label for="cod" class="block text-sm font-medium text-gray-700 mb-1">Código<span class="text-red-600">*</span></label>
                            <input type="text" name="cod" id="cod"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Contraseña -->
                        <div>
                            <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1">Contraseña<span class="text-red-600">*</span></label>
                            <input type="password" name="contrasena" id="contrasena" maxlength="12"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                            <p class="text-xs text-gray-500 mt-1">Máximo 12 caracteres</p>
                        </div>

                        <!-- Repetir Contraseña -->
                        <div>
                            <label for="contrasena2" class="block text-sm font-medium text-gray-700 mb-1">Repetir Contraseña<span class="text-red-600">*</span></label>
                            <input type="password" name="contrasena2" id="contrasena2" maxlength="12"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Correo -->
                        <div>
                            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico<span class="text-red-600">*</span></label>
                            <input type="email" name="correo" id="correo"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                        </div>

                        <!-- Móvil -->
                        <div>
                            <label for="movil" class="block text-sm font-medium text-gray-700 mb-1">Teléfono Móvil</label>
                            <input type="number" name="movil" id="movil"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                        </div>
                    </div>
                </div>

                <!-- Right Column - Fixed for better spacing -->
                <div class="space-y-6 flex flex-col h-full">
                    <!-- Clase de Usuario -->
                    <div>
                        <label for="clase" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Usuario<span class="text-red-600">*</span></label>
                        <select id="clase" name="clase"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300">
                            <option value="montador" selected>Montador</option>
                            <option value="oficina">Oficina</option>
                            <option value="cliente">Cliente</option>
                            <option value="admon">Administrador</option>
                            <option value="diseno">Diseño</option>
                            <option value="estudio">Estudio</option>
                        </select>
                    </div>

                    <!-- Imagen de Perfil - Added flex-grow -->
                    <div class="flex-grow">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen de Perfil</label>
                        <div class="mt-1 flex flex-col items-center h-full">
                            <label class="w-full cursor-pointer">
                                <div class="bg-white px-4 py-2.5 border border-gray-300 rounded-lg text-center hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300 text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Seleccionar imagen</span>
                                    <input type="file" name="imagen[]" accept="image/*" id="imagen" class="hidden">
                                </div>
                            </label>
                            <p class="text-xs text-gray-500 mt-2 text-center">Formatos recomendados: JPG, PNG</p>
                        </div>
                    </div>

                    <!-- Form Notes - Added mt-auto to stick to bottom -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-auto">
                        <h4 class="font-medium text-gray-800 mb-2">Información Importante</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <span>Los campos marcados con <span class="text-red-600">*</span> son obligatorios.</span>
                            </li>
                            <li class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <span>El nombre de usuario debe ser único en el sistema.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <input type="hidden" name="crearUsuario" value="1">
                <!-- Form Actions - Span full width on smaller screens -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8 
                col-span-1 md:col-span-2 lg:col-span-3 
                xl:col-span-3">
                    <a href="https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios"
                        id="cancelar"
                        class="w-full sm:w-1/2">
                        <button type="button"
                            class="w-full py-3 px-4 bg-gray-300 hover:bg-gray-400 
                       text-gray-800 rounded-lg font-medium 
                       transition-colors duration-300 
                       flex justify-center items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            CANCELAR
                        </button>
                    </a>
                    <button type="button"
                        id="enviar"
                        class="w-full sm:w-1/2 py-3 px-4 bg-red-600 hover:bg-red-700 
                       text-white rounded-lg font-medium 
                       transition-colors duration-300 
                       flex justify-center items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        CREAR USUARIO
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="https://dosxdos.app.iidos.com/js/navigation.js"></script>
<script>
    titulo1 = <?php echo ("'" . $nombre . '_' . 'CREAR USUARIO' . "'") ?>;
    titulo2 = 'CREAR USUARIO';
    /* CAMBIO DE IMAGEN */
    const $imagen = document.getElementById('imagen'),
        $imagenPerfil = document.getElementById('imagenPerfil');
    const cambioImagen = file => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
    $imagen.addEventListener('change', (e) => {
        archivo = e.target.files[0];
        if (archivo) {
            cambioImagen(archivo)
                .then(file => {
                    $imagenPerfil.src = file;
                })
                .catch(error => {
                    console.log(error);
                    $imagenPerfil.src = "";
                    $imagenPerfil.setAttribute('alt', 'Error')
                })
        }
    });
</script>
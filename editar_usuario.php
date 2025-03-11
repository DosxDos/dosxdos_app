<?php

if (!isset($_COOKIE['login'])) {
    header("location: index.html");
}

$usuarioEditado = false;
$mensajeUsuarioEditado = '';

if ((isset($_REQUEST["editarUsuario"]))) {
    require_once "config.php";
    if (!$conexion) {
        header("location: dosxdos.php?modulo=editarUsuario&mensaje=Error de conexión a la base de datos: $conexion->error&id=$id");
    }
    $idUsuario = $_COOKIE['usuario'];
    $id = $conexion->sanitizar($_REQUEST["id"]);
    $usuarioActual = $conexion->sanitizar($_REQUEST["usuarioActual"]);
    $usuario = $conexion->sanitizar($_REQUEST["usuario"]);
    $clase = $conexion->sanitizar($_REQUEST["clase"]);
    $cod = $conexion->sanitizar($_REQUEST["cod"]);
    /*$clave = md5($clave);*/
    $contrasena = $conexion->sanitizar($_REQUEST["contrasena"]);
    $correo = $conexion->sanitizar($_REQUEST["correo"]);
    $movil = $conexion->sanitizar($_REQUEST["movil"]);
    $nombre = $conexion->sanitizar($_REQUEST["nombre"]);
    $apellido = $conexion->sanitizar($_REQUEST["apellido"]);
    $imagenActual = $conexion->sanitizar($_REQUEST["imagenActual"]);
    $activo = $conexion->sanitizar($_REQUEST["activo"]);
    $nombreImagen;
    ($_FILES["imagen"]["name"][0]) ? $imagen = uniqid() . '_' . $tipo . '_' . $extension . '_' . $conexion->sanitizar($_FILES["imagen"]["name"][0]) : $nombreImagen = $imagenActual;
    if ($_FILES["imagen"]["name"][0]) {
        $tipo = explode('/', $conexion->sanitizar($_FILES["imagen"]["type"][0]))[0];
        $extension = explode('/', $conexion->sanitizar($_FILES["imagen"]["type"][0]))[1];
    }
    if ($_FILES["imagen"]["name"][0]) {
        $nombreImagen = 'img_usuarios/' . $imagen;
    }
    if ($usuario == $usuarioActual) {
        $query = "UPDATE usuarios SET cod=\"$cod\", contrasena=\"$contrasena\", clase=\"$clase\", correo=\"$correo\", movil=\"$movil\", nombre=\"$nombre\", apellido=\"$apellido\", imagen=\"$nombreImagen\", activo=\"$activo\" WHERE id=\"$id\"";
    } else {
        $query = "UPDATE usuarios SET usuario=\"$usuario\", cod=\"$cod\", contrasena=\"$contrasena\", clase=\"$clase\", correo=\"$correo\", movil=\"$movil\", nombre=\"$nombre\", apellido=\"$apellido\", imagen=\"$nombreImagen\", activo=\"$activo\" WHERE id=\"$id\"";
    }
    $result = $conexion->datos($query);
    if ($result) {
        if ($imagen) {
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"][0], "img_usuarios/" . $imagen)) {
                unlink($imagenActual);
                if ($idUsuario == $id) {
                    $usuarioEditado = true;
                    $mensajeUsuarioEditado = 'Tu usuario ha sido editado exitosamente';
                } else {
                    header("location: dosxdos.php?modulo=editarUsuario&mensaje=El usuario ha sido editado exitosamente&id=$id");
                }
            } else {
                if ($idUsuario == $id) {
                    $usuarioEditado = true;
                    $mensajeUsuarioEditado = 'Tu usuario ha sido editado exitosamente, pero no ha sido posible guardar la imagen';
                } else {
                    header("location: dosxdos.php?modulo=editarUsuario&mensaje=El usuario se ha editado exitosamente, pero no ha sido posible guardar la imagen&id=$id");
                }
            }
        } else {
            if ($idUsuario == $id) {
                $usuarioEditado = true;
                $mensajeUsuarioEditado = 'Tu usuario ha sido editado exitosamente';
            } else {
                header("location: dosxdos.php?modulo=editarUsuario&mensaje=El usuario se ha editado exitosamente&id=$id");
            }
        }
    } else {
        $error = $conexion->error;
        header("location: dosxdos.php?modulo=editarUsuario&mensaje=Error de base de datos, el usuario no ha sido editado. Por favor verifica si el usuario ya existe - $error&id=$id");
    }
}

if ((isset($_REQUEST["eliminar"]))) {
    require_once "config.php";
    if (!$conexion) {
        header("location: dosxdos.php?modulo=editarUsuario&mensaje=Error de conexión a la base de datos: $conexion->error&id=$id");
    }
    $id = $conexion->sanitizar($_REQUEST["id"]);
    $accion = 1;
    $query = "UPDATE usuarios SET eliminado=\"$accion\" WHERE id=\"$id\"";
    $result = $conexion->datos($query);
    if ($result) {
        header("location: dosxdos.php?modulo=usuarios&mensaje=El usuario ha sido eliminado");
    } else {
        $error = $conexion->error;
        header("location: dosxdos.php?modulo=usuarios&mensaje=Error de base de datos, el usuario no ha sido eliminado - $error");
    }
}

$query = "SELECT * FROM usuarios WHERE id = $id";
$result = $conexion->datos($query);
if ($result) {
    $row = $result->fetch_assoc();
    $usuarioE = $row['usuario'];
    $codE = $row['cod'];
    $contrasenaE = $row['contrasena'];
    $claseE = $row['clase'];
    $correoE = $row['correo'];
    $movilE = $row['movil'];
    $nombreE = $row['nombre'];
    $apellidoE = $row['apellido'];
    $imagenE = $row['imagen'];
    $activoE = $row['activo'];
} else {
    $mensaje .= '--Error en la conexión con la base de datos para la obtención de la información del usuario: ' . $conexion->error . '--';
    header("location: dosxdos.php?modulo=editarUsuario&mensaje=$mensaje");
}

if ($idUsuario == $id && $usuarioEditado) {
?>
    <script>
        mensaje = <?php echo ('"' . $mensajeUsuarioEditado . '"') ?>;
        nuevoUsuario = {
            id: <?php if ($idUsuario) {
                    echo ('"' . $idUsuario . '"');
                } else {
                    echo ('"' . '' . '"');
                } ?>,
            usuario: <?php if ($usuario) {
                            echo ('"' . $usuario . '"');
                        } else {
                            echo ('"' . '' . '"');
                        } ?>,
            cod: <?php if ($cod) {
                        echo ('"' . $cod . '"');
                    } else {
                        echo ('"' . '' . '"');
                    } ?>,
            contrasena: <?php if ($contrasena) {
                            echo ('"' . $contrasena . '"');
                        } else {
                            echo ('"' . '' . '"');
                        } ?>,
            clase: <?php if ($clase) {
                        echo ('"' . $clase . '"');
                    } else {
                        echo ('"' . '' . '"');
                    } ?>,
            correo: <?php if ($correo) {
                        echo ('"' . $correo . '"');
                    } else {
                        echo ('"' . '' . '"');
                    } ?>,
            movil: <?php if ($movil) {
                        echo ('"' . $movil . '"');
                    } else {
                        echo ('"' . '' . '"');
                    } ?>,
            nombre: <?php if ($nombre) {
                        echo ('"' . $nombre . '"');
                    } else {
                        echo ('"' . '' . '"');
                    } ?>,
            apellido: <?php if ($apellido) {
                            echo ('"' . $apellido . '"');
                        } else {
                            echo ('"' . '' . '"');
                        } ?>,
            imagen: <?php
                    echo ('"' . $nombreImagen . '"');
                    ?>,
            activo: <?php if ($activo) {
                        echo ('"' . $activo . '"');
                    } else {
                        echo ('"' . '' . '"');
                    } ?>,
        }

        function agregarDato(database, store, data) {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open(database);
                request.onsuccess = (event) => {
                    const db = event.target.result;
                    const transaction = db.transaction(store, 'readwrite');
                    const datosStore = transaction.objectStore(store);
                    const clearRequest = datosStore.clear();
                    clearRequest.onsuccess = (clearEvent) => {
                        const requestAgregar = datosStore.add(data);
                        requestAgregar.onsuccess = (event) => {
                            resolve(true);
                        };
                        requestAgregar.onerror = (event) => {
                            console.error(`Error en la función agregarDato al agregar los datos al almacén ${store}: ${event.target.error}`);
                            reject(event.target.error);
                        };
                    };
                    clearRequest.onerror = (event) => {
                        console.error(`Error en la función agregarDato al limpiar el almacén ${store} para ingresar los nuevos datos: ${event.target.error}`);
                        reject(event.target.error);
                    };
                };
                request.onerror = (event) => {
                    console.error(`Error en la función agregarDato al abrir la base de datos ${database} para ingresar los nuevos datos: ${event.target.error}`);
                    reject(event.target.error);
                }
            })
        }

        async function procesarUsuario() {
            try {
                const actualizado = await agregarDato('dosxdos', 'usuario', nuevoUsuario);
                if (actualizado) {
                    const mensajeActualizado = mensaje;
                    localStorage.setItem('mensaje', mensajeActualizado);
                    if (nuevoUsuario.clase == 'montador') {
                        window.location.href = "https://dosxdos.app.iidos.com/rutas_montador.html";
                    } else {
                        window.location.href = "https://dosxdos.app.iidos.com/ot.html";
                    }
                }
            } catch (error) {
                const mensajeActualizado = 'El usuario ha sido editado exitosamente, pero no ha sido actualizado en la base de datos local, es necesario que cierres la sesión y vuelvas a realizar login para efectuar los cambios: ' + error.message;
                localStorage.setItem('mensaje', mensajeActualizado);
                if (nuevoUsuario.clase == 'montador') {
                    window.location.href = "https://dosxdos.app.iidos.com/rutas_montador.html";
                } else {
                    window.location.href = "https://dosxdos.app.iidos.com/ot.html";
                }
            }
        }

        procesarUsuario();
    </script>
<?php
    die();
}

?>
<div class=" px-4 sm:px-6 pb-12 pt-32 bg-gray-100">
    <div class="container mx-auto">
        <!-- Header Section -->
        <section class="mb-8">
            <div class="rounded-xl shadow-lg overflow-hidden relative">
                <div class="absolute inset-0"
                    style="background-image: url('https://dosxdos.app.iidos.com/img/texture-red.svg'); background-size: contain;">
                </div>
                <div class="relative p-6 sm:p-8 text-white z-10">
                    <h1 id="titulo" class="text-2xl sm:text-3xl font-bold mb-2">Editar Usuario</h1>
                    <p id="subtitulo" class="text-sm sm:text-base opacity-90"><?php echo $nombreE . ' ' . $apellidoE; ?></p>
                </div>
            </div>
        </section>

        <!-- Quick Actions Row -->
        <section class="mb-8">
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Back Button -->
                <?php if ($clase == 'admon') { ?>
                    <a href="https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios" class="flex items-center gap-2 text-red-600 mb-3 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Volver</span>
                    </a>
                <?php } else if ($clase == 'montador') { ?>
                    <a href="https://dosxdos.app.iidos.com/rutas_montador.html" class="flex items-center gap-2 text-red-600 mb-3 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Volver</span>
                    </a>
                <?php } else { ?>
                    <a href="https://dosxdos.app.iidos.com/ot.html" class="flex items-center gap-2 text-red-600 mb-3 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Volver</span>
                    </a>
                <?php } ?>
            </div>
        </section>

        <!-- Main Form Container -->
        <section class="bg-white rounded-xl shadow-md">
            <div class="p-6">
                <form action="editar_usuario.php" method="post" id="editarUsuarioFormulario" enctype="multipart/form-data" class="space-y-6" novalidate>

                    <!-- User profile image preview -->
                    <div class="flex justify-center mb-8 -mt-12">
                        <div class="w-48 h-48 rounded-full overflow-hidden border-2 border-red-600 bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center shadow-2xl transform hover:scale-105 transition-all duration-300">
                            <img src="<?php if ($imagenE) {
                                            echo ('https://dosxdos.app.iidos.com/' . $imagenE);
                                        } else {
                                            echo 'https://dosxdos.app.iidos.com/img/usuario.png';
                                        } ?>"
                                id="imagenPerfil" alt="Perfil" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Username and Role -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User input -->
                        <div class="space-y-2">
                            <label for="usuario" class="block text-sm font-medium text-gray-700">USUARIO: <span class="text-red-600">*</span></label>
                            <input type="text" name="usuario" id="usuario" value="<?php echo $usuarioE; ?>" maxlength="8"
                                class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                        </div>

                        <!-- Role/Class Selection -->
                        <?php if ($clase == 'admon') { ?>
                            <div class="space-y-2">
                                <label for="clase" class="block text-sm font-medium text-gray-700">CLASE: <span class="text-red-600">*</span></label>
                                <div class="relative">
                                    <select name="clase" id="clase"
                                        class="block w-full p-2.5 pr-12 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent cursor-pointer appearance-none">
                                        <option value="montador" <?php if ($claseE == 'montador') echo 'selected'; ?>>Montador</option>
                                        <option value="oficina" <?php if ($claseE == 'oficina') echo 'selected'; ?>>Oficina</option>
                                        <option value="cliente" <?php if ($claseE == 'cliente') echo 'selected'; ?>>Cliente</option>
                                        <option value="diseno" <?php if ($claseE == 'diseno') echo 'selected'; ?>>Diseño</option>
                                        <option value="estudio" <?php if ($claseE == 'estudio') echo 'selected'; ?>>Estudio</option>
                                        <option value="admon" <?php if ($claseE == 'admon') echo 'selected'; ?>>Administrador</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="clase" value="<?php echo $claseE; ?>">
                        <?php } ?>
                    </div>

                    <!-- Code and other inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Code Input (Admin only) -->
                        <?php if ($clase == 'admon') { ?>
                            <div class="space-y-2">
                                <label for="cod" class="block text-sm font-medium text-gray-700">CÓDIGO: <span class="text-red-600">*</span></label>
                                <input type="text" name="cod" id="cod" value="<?php echo $codE; ?>"
                                    class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="cod" value="<?php echo $codE; ?>">
                        <?php } ?>

                        <!-- Status (Admin only) -->
                        <?php if ($clase == 'admon') { ?>
                            <div class="space-y-2">
                                <label for="activo" class="block text-sm font-medium text-gray-700">ESTADO:</label>
                                <div class="relative">
                                    <select name="activo" id="activo"
                                        class="block w-full p-2.5 pr-12 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent cursor-pointer appearance-none">
                                        <option value="1" <?php if ($activoE == 1) echo 'selected'; ?>>Activo</option>
                                        <option value="0" <?php if ($activoE == 0) echo 'selected'; ?>>Inactivo</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="activo" value="<?php echo $activoE; ?>">
                        <?php } ?>
                    </div>

                    <!-- Password fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="contrasena" class="block text-sm font-medium text-gray-700">CONTRASEÑA: <span class="text-red-600">*</span></label>
                            <input type="password" name="contrasena" id="contrasena" value="<?php echo $contrasenaE; ?>" maxlength="12"
                                class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                        </div>

                        <div class="space-y-2">
                            <label for="contrasena2" class="block text-sm font-medium text-gray-700">REPETIR CONTRASEÑA: <span class="text-red-600">*</span></label>
                            <input type="password" name="contrasena2" id="contrasena2" value="<?php echo $contrasenaE; ?>" maxlength="12"
                                class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                        </div>
                    </div>

                    <!-- Contact information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">NOMBRE: <span class="text-red-600">*</span></label>
                            <input type="text" name="nombre" id="nombre" value="<?php echo $nombreE; ?>"
                                class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                        </div>

                        <div class="space-y-2">
                            <label for="apellido" class="block text-sm font-medium text-gray-700">APELLIDO:</label>
                            <input type="text" name="apellido" id="apellido" value="<?php echo $apellidoE; ?>"
                                class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="correo" class="block text-sm font-medium text-gray-700">CORREO: <span class="text-red-600">*</span></label>
                            <input type="email" name="correo" id="correo" value="<?php echo $correoE; ?>"
                                class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                        </div>

                        <div class="space-y-2">
                            <label for="movil" class="block text-sm font-medium text-gray-700">MÓVIL:</label>
                            <input type="number" name="movil" id="movil" value="<?php echo $movilE; ?>"
                                class="block w-full p-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent" />
                        </div>
                    </div>

                    <!-- Profile image upload -->
                    <div class="space-y-2 mt-4">
                        <label for="imagen" class="block text-sm font-medium text-gray-700">IMAGEN DE PERFIL:</label>
                        <label class="flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-300 rounded-lg py-3 px-4 shadow-sm transition-all cursor-pointer w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Seleccionar imagen</span>
                            <input type="file" name="imagen[]" id="imagen" accept="image/*" class="hidden" />
                        </label>
                    </div>

                    <input type="hidden" name="editarUsuario" value="1">
                    <input type="hidden" name="usuarioActual" value="<?php echo $usuarioE; ?>">
                    <input type="hidden" name="imagenActual" value="<?php echo $imagenE ?>" id="imagenActual">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex flex-col-reverse md:flex-row gap-3 pt-4 border-t border-gray-200">
                        <?php if ($clase == 'admon') { ?>
                            <a href="https://dosxdos.app.iidos.com/dosxdos.php?modulo=usuarios" id="cancelar" class="w-full">
                                <button type="button" class="w-full flex items-center justify-center gap-2 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 px-6 rounded-lg shadow-md transition-all text-base font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    CANCELAR
                                </button>
                            </a>
                        <?php } else if ($clase == 'montador') { ?>
                            <a href="https://dosxdos.app.iidos.com/rutas_montador.html" id="cancelar" class="w-full">
                                <button type="button" class="w-full flex items-center justify-center gap-2 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 px-6 rounded-lg shadow-md transition-all text-base font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    CANCELAR
                                </button>
                            </a>
                        <?php } else { ?>
                            <a href="https://dosxdos.app.iidos.com/ot.html" id="cancelar" class="w-full">
                                <button type="button" class="w-full flex items-center justify-center gap-2 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 px-6 rounded-lg shadow-md transition-all text-base font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    CANCELAR
                                </button>
                            </a>
                        <?php } ?>

                        <?php if ($clase == 'admon') { ?>
                            <a href="https://dosxdos.app.iidos.com/editar_usuario.php?eliminar=1&id=<?php echo $id ?>" class="eliminar w-full">
                                <button type="button" id="eliminarUsuario" class="w-full flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-900 text-white py-3 px-6 rounded-lg shadow-md transition-all text-base font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    ELIMINAR USUARIO
                                </button>
                            </a>
                        <?php } ?>

                        <button type="button" id="enviar" class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg shadow-md transition-all text-base font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            GUARDAR CAMBIOS
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- User Deletion Confirmation Modal -->
        <div id="deleteUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">¿Estás seguro?</h2>
                    <p class="text-gray-600 mb-6">Esta acción eliminará permanentemente el usuario. No podrás deshacer esta operación.</p>

                    <div class="flex justify-center space-x-4">
                        <button id="cancelDeleteBtn" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <a href="#" id="confirmDeleteBtn" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Eliminar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <script>
            actualizarUsuario = false;
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
            })

            document.addEventListener("DOMContentLoaded", function() {
                const deleteButton = document.querySelector('.eliminar');
                const deleteModal = document.getElementById('deleteUserModal');
                const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

                if (deleteButton) {
                    deleteButton.addEventListener("click", function(event) {
                        event.preventDefault(); // Evita que se redirija automáticamente
                        deleteModal.classList.remove('hidden'); // Muestra el modal
                    });
                }

                if (cancelDeleteBtn) {
                    cancelDeleteBtn.addEventListener("click", function() {
                        deleteModal.classList.add('hidden'); // Oculta el modal al cancelar
                    });
                }

                if (confirmDeleteBtn) {
                    confirmDeleteBtn.addEventListener("click", function(event) {
                        event.preventDefault();
                        window.location.href = deleteButton.getAttribute("href"); // Redirige a la URL de eliminación
                    });
                }

                // Cerrar el modal si se hace clic fuera del contenido
                deleteModal.addEventListener("click", function(event) {
                    if (event.target === deleteModal) {
                        deleteModal.classList.add('hidden');
                    }
                });
            });
        </script>
        <script src="https://dosxdos.app.iidos.com/js/notificaciones.js"></script>
        <script src="https://dosxdos.app.iidos.com/js/loadFirebase.js"></script>
<?php
require_once __DIR__ . "/conexion_clase.php";
require_once __DIR__ . "/respuestas_clase.php";

class usuarios extends conexion
{
    private $table = "usuarios";
    public $usuarioId = "";
    public $usuario = "";
    public $cod = "";
    public $contrasena = "";
    public $contrasenaActual = "";
    public $clase = "";
    public $correo = "";
    public $movil = "";
    public $nombre = "";
    public $apellido = "";
    public $imagen = "";
    public $activo = 1; // por defecto activo
    public $eliminado = 0;

    public function usuarios()
    {
        $_respuestas = new Respuestas;
        $query = "SELECT id, cod, clase, correo, movil, nombre, apellido, imagen, activo, eliminado FROM $this->table";
        $result = parent::datos($query);
        if ($result) {
            $datos = [];
            while ($row = $result->fetch_assoc()) {
                $datos[] = $row;
            }
            return $_respuestas->ok(parent::utf8($datos));
        } else {
            return $_respuestas->error_500("Error en la Api intermedia - Error interno en el servidor al conectar o consultar la base de datos");
        }
    }

    public function usuario($id)
    {
        $_respuestas = new Respuestas;
        $id = (int)$id;
        $query = "SELECT id, cod, clase, correo, movil, nombre, apellido, imagen, activo, eliminado FROM $this->table WHERE id = $id";
        $result = parent::datos($query);
        if ($result) {
            $datos = [];
            if ($result->num_rows) {
                $datos = $result->fetch_all(MYSQLI_ASSOC);
            }
            return $_respuestas->ok(parent::utf8($datos));
        } else {
            return $_respuestas->error_500("Error en la Api intermedia - Error interno en el servidor al conectar o consultar la base de datos");
        }
    }

    public function clientes()
    {
        $_respuestas = new Respuestas;
        $query = "SELECT * FROM $this->table WHERE clase = 'cliente' AND activo = 1 AND eliminado = 0";
        $result = parent::datos($query);
        if (!$result) {
            return $_respuestas->error_500("Error en la Api intermedia - Error interno en el servidor al conectar o consultar la base de datos para seleccionar los usuarios clientes");
        }

        $datos = [];
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $item = [
                    'id'       => $row['id'],
                    'usuario'  => $row['usuario'],
                    'cod'      => $row['cod'],
                    'clase'    => $row['clase'],
                    'correo'   => $row['correo'],
                    'movil'    => $row['movil'],
                    'nombre'   => $row['nombre'],
                    'apellido' => $row['apellido'],
                    'imagen'   => $row['imagen'],
                ];

                $cod = (int)$row['cod'];
                $q2  = "SELECT nombre FROM clientes WHERE cod = $cod";
                $r2  = parent::datos($q2);
                if ($r2 && $r2->num_rows) {
                    $row2 = $r2->fetch_assoc();
                    $item['nombreCliente'] = $row2['nombre'];
                } else {
                    $item['nombreCliente'] = '';
                }

                $datos[] = $item;
            }
            return $_respuestas->ok($datos);
        }
        return $_respuestas->okF($datos);
    }

    public function cliente($id)
    {
        $_respuestas = new Respuestas;
        $id = (int)$id;
        $query = "SELECT * FROM $this->table WHERE id = $id";
        $result = parent::datos($query);
        if (!$result) {
            return $_respuestas->error_500("Error en la Api intermedia - Error interno en el servidor al conectar o consultar la base de datos para seleccionar los usuarios clientes");
        }

        $datos = [];
        if ($result->num_rows) {
            $row = $result->fetch_assoc();
            if ($row['clase'] === 'cliente') {
                $datos = [
                    'id'       => $row['id'],
                    'usuario'  => $row['usuario'],
                    'cod'      => $row['cod'],
                    'clase'    => $row['clase'],
                    'correo'   => $row['correo'],
                    'movil'    => $row['movil'],
                    'nombre'   => $row['nombre'],
                    'apellido' => $row['apellido'],
                    'imagen'   => $row['imagen'],
                    'activo'   => $row['activo'],
                    'eliminado'=> $row['eliminado'],
                ];
                $cod = (int)$row['cod'];
                $q2  = "SELECT nombre FROM clientes WHERE cod = $cod";
                $r2  = parent::datos($q2);
                if ($r2 && $r2->num_rows) {
                    $row2 = $r2->fetch_assoc();
                    $datos['nombreCliente'] = $row2['nombre'];
                } else {
                    $datos['nombreCliente'] = '';
                }
                return $_respuestas->ok($datos);
            }
        }
        return $_respuestas->okF($datos);
    }

    public function post($json)
    {
        $_respuestas = new Respuestas;
        $datos = json_decode($json, true);

        // Si el body no es JSON válido, responde 400
        if ($datos === null && json_last_error() !== JSON_ERROR_NONE) {
            return $_respuestas->error_400("JSON inválido");
        }

        // --- LOGIN ---
        if (isset($datos['login']) || (isset($datos['usuario'], $datos['contrasena']) && count($datos) <= 3)) {
            if (!isset($datos['usuario']) || !isset($datos['contrasena'])) {
                return $_respuestas->error_400("Faltan credenciales");
            }
            $usuario    = parent::sanitizar($datos['usuario']);
            $contrasena = parent::sanitizar($datos['contrasena']);
            $query = "SELECT * FROM $this->table WHERE usuario = '$usuario' LIMIT 1";
            $result = parent::datos($query);
            if (!$result) {
                return $_respuestas->error_500("Error en la consulta SQL de la api intermedia");
            }
            if ($result->num_rows === 0) {
                return $_respuestas->error_401("No autorizado en la api intermedia, es probable que el usuario ingresado no exista");
            }

            $row = $result->fetch_assoc();
            $contrasenaR = $row['contrasena'];
            if ($contrasenaR == $contrasena) {
                $datosR = [
                    'id'       => $row['id'],
                    'usuario'  => $row['usuario'],
                    'cod'      => $row['cod'],
                    'clase'    => $row['clase'],
                    'correo'   => $row['correo'],
                    'movil'    => $row['movil'],
                    'nombre'   => $row['nombre'],
                    'apellido' => $row['apellido'],
                    'imagen'   => $row['imagen'],
                    'activo'   => $row['activo'],
                    'eliminado'=> $row['eliminado'],
                ];
                return $_respuestas->ok(parent::utf8($datosR));
            }
            return $_respuestas->error_401();
        }

        // --- CREACIÓN ---
        $requeridos = ['usuario','cod','contrasena','clase','correo','nombre'];
        foreach ($requeridos as $k) {
            if (!isset($datos[$k])) {
                return $_respuestas->error_400("Error en el formato de los datos que has enviado - Falta '$k'");
            }
        }

        $this->usuario    = $datos['usuario'];
        $this->cod        = $datos['cod'];
        $this->contrasena = $datos['contrasena'];
        $this->clase      = $datos['clase'];
        $this->correo     = $datos['correo'];
        $this->nombre     = $datos['nombre'];
        if (isset($datos['movil']))    $this->movil    = $datos['movil'];
        if (isset($datos['apellido'])) $this->apellido = $datos['apellido'];
        if (isset($datos['imagen']))   $this->imagen   = $datos['imagen'];
        if (isset($datos['activo']))   $this->activo   = (int)$datos['activo'];

        $result = $this->postUsuario();
        if ($result) {
            return $_respuestas->ok("El usuario con id $result ha sido creado exitosamente");
        }
        return $_respuestas->error_500("Error en la base de datos al intentar generar el nuevo registro - Verifica si el usuario ya existe");
    }

    private function postUsuario()
    {
        $query = "INSERT INTO $this->table (usuario, cod, contrasena, clase, correo, movil, nombre, apellido, imagen, activo)
                  VALUES ('$this->usuario', '$this->cod', '$this->contrasena', '$this->clase', '$this->correo', '$this->movil', '$this->nombre', '$this->apellido', '$this->imagen', '$this->activo')";
        $result = parent::datosPost($query);
        return $result ? $result : 0;
    }

    public function put($json)
    {
        $_respuestas = new Respuestas;
        $datos = json_decode($json, true);
        if (!isset($datos['usuarioId']) || !isset($datos['contrasenaActual'])) {
            return $_respuestas->error_400("Formato incorrecto de los datos o no se especificó un dato obligatorio");
        }

        $this->usuarioId        = (int)parent::sanitizar($datos['usuarioId']);
        $this->contrasenaActual = $datos['contrasenaActual'];

        $query = "SELECT * FROM $this->table WHERE id = $this->usuarioId";
        $result = parent::datos($query);
        if (!$result) {
            return $_respuestas->error_500("Error en la Api intermedia - No se ha podido consultar los datos originales del usuario");
        }
        if ($row = $result->fetch_assoc()) {
            $this->usuario   = $row['usuario'];
            $this->cod       = $row['cod'];
            $this->contrasena= $row['contrasena'];
            $this->clase     = $row['clase'];
            $this->correo    = $row['correo'];
            $this->movil     = $row['movil'];
            $this->nombre    = $row['nombre'];
            $this->apellido  = $row['apellido'];
            $this->imagen    = $row['imagen'];
            $this->activo    = $row['activo'];
        }

        if ($this->contrasena != $this->contrasenaActual) {
            return $_respuestas->error_401("Error: La contraseña actual no coincide con la contraseña guardada en nuestra base de datos");
        }

        // aplicar cambios
        foreach (['usuario','cod','contrasena','clase','correo','movil','nombre','apellido','imagen','activo'] as $k) {
            if (isset($datos[$k])) $this->$k = $datos[$k];
        }

        $result = $this->putUsuario();
        if (!$result) {
            return $_respuestas->error_500("Error en la base de datos al intentar actualizar el registro");
        }

        // manejo de imagen base64 si llega
        if (isset($datos['imagen']) && is_string($datos['imagen']) && str_contains($datos['imagen'], ';base64,')) {
            try {
                $imagenAnterior = __DIR__ . "/../../" . $this->imagen;
                $this->imagen   = $datos['imagen'];
                $partes     = explode(";base64,", $this->imagen);
                $mime       = explode(':', $partes[0])[1] ?? 'image/png';
                $extension  = explode('/', $mime)[1] ?? 'png';
                $file_base64= base64_decode($partes[1]);
                $nombre     = "img_usuarios/" . uniqid('', true) . "." . $extension;
                $filePath   = __DIR__ . "/../../";
                file_put_contents($filePath . $nombre, $file_base64);
                @unlink($imagenAnterior);
                $q = "UPDATE $this->table SET imagen=\"$nombre\" WHERE id=\"$this->usuarioId\"";
                parent::datosPost($q);
            } catch (\Throwable $th) {
                return $_respuestas->error_500("Error al realizar el cambio del archivo de imagen del usuario");
            }
        }
        return $_respuestas->ok("El usuario con id $this->usuarioId ha sido actualizado exitosamente");
    }

    private function putUsuario()
    {
        $query = "UPDATE $this->table SET 
                    usuario = '$this->usuario',
                    cod = '$this->cod',
                    contrasena = '$this->contrasena',
                    clase = '$this->clase',
                    correo = '$this->correo',
                    movil = '$this->movil',
                    nombre = '$this->nombre',
                    apellido = '$this->apellido',
                    imagen = '$this->imagen',
                    activo = '$this->activo'
                  WHERE id = $this->usuarioId";
        $result = parent::datosPost($query);
        return $result ? $result : 0;
    }

    public function delete($json)
    {
        $_respuestas = new Respuestas;
        $datos = json_decode($json, true);
        if (!isset($datos['usuarioId'])) {
            return $_respuestas->error_400("No se especificó el id del usuario que se debe eliminar");
        }
        $this->usuarioId = (int)$datos['usuarioId'];
        $result = $this->deleteUsuario();
        if ($result) {
            return $_respuestas->ok("El usuario con id $this->usuarioId ha sido eliminado exitosamente");
        }
        return $_respuestas->error_500("Error en la base de datos al intentar eliminar el registro");
    }

    private function deleteUsuario()
    {
        $query = "DELETE FROM $this->table WHERE id = $this->usuarioId";
        $result = parent::datosPost($query);
        return $result ? $result : 0;
    }
}

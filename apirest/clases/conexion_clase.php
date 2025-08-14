<?php

class Conexion
{
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    /** @var mysqli|null */
    public $conexion = null;
    public $errno = 0;
    public $error = '';

    function __construct()
    {
        // Lee configuración del JSON (soporta {"conexion":{...}} o [{...}])
        $cfg = $this->datosConexion();
        if (isset($cfg['conexion']) && is_array($cfg['conexion'])) {
            $cfg = $cfg['conexion'];
        } elseif (isset($cfg[0]) && is_array($cfg[0])) {
            $cfg = $cfg[0];
        }

        $this->server   = $cfg['server']   ?? 'host.docker.internal';
        $this->user     = $cfg['user']     ?? 'root';
        $this->password = $cfg['password'] ?? '';
        $this->database = $cfg['database'] ?? '';
        $this->port     = (int)($cfg['port'] ?? 3306);

        // En Docker, "localhost" apunta al contenedor. Forzamos TCP al host:
        if (strtolower($this->server) === 'localhost') {
            $this->server = 'host.docker.internal';
        }

        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = @new mysqli($this->server, $this->user, $this->password, $this->database, $this->port);

        if ($conn && !$conn->connect_errno) {
            $conn->set_charset('utf8mb4');
            $this->conexion = $conn;
        } else {
            $this->errno = $conn ? $conn->connect_errno : 2002;
            $this->error = $conn ? $conn->connect_error : 'No se pudo crear la conexión';
            $this->conexion = null; // Evita usar una conexión rota
        }
    }

    private function datosConexion()
    {
        $jsonPath = __DIR__ . '/conexion.json';
        if (!is_file($jsonPath)) {
            return [];
        }
        $jsondata = file_get_contents($jsonPath);
        $cfg = json_decode($jsondata, true);
        return is_array($cfg) ? $cfg : [];
    }

    public function datos($query)
    {
        if (!$this->conexion) {
            $this->errno = 2002;
            $this->error = 'No DB connection';
            return 0;
        }
        $result = $this->conexion->query($query);
        if ($this->conexion->error) {
            $this->error = $this->conexion->error;
            return 0;
        }
        return $result;
    }

    public function datosPost($query)
    {
        if (!$this->conexion) {
            $this->errno = 2002;
            $this->error = 'No DB connection';
            return 0;
        }
        $ok = $this->conexion->query($query);
        if ($this->conexion->error) {
            $this->error = $this->conexion->error;
            return 0;
        }
        // Si hay insert_id lo devolvemos; si no, 1 para UPDATE/DELETE correctos
        return $this->conexion->insert_id ?: ($ok ? 1 : 0);
    }

    public function utf8($array)
    {
        array_walk_recursive($array, function (&$item) {
            if (is_string($item) && !mb_detect_encoding($item, 'UTF-8', true)) {
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    public function sanitizar($datos)
    {
        $s = htmlspecialchars(trim(strip_tags($datos ?? "")), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        if ($this->conexion) {
            return $this->conexion->real_escape_string($s);
        }
        // Sin conexión: evita fatal y escapa de forma básica
        return addslashes($s);
    }
}

<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

require_once __DIR__ . '/conexion_clase.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class Notificaciones
{
    private $conn;
    private $table_name = "notificaciones_push";

    private $id;
    private $usuario_id;
    private $titulo;
    private $mensaje;
    private $fecha_envio;
    private $tipo_usuario;
    private $fecha_visto;

    public function __construct($id = null, $usuario_id = null, $titulo = null, $mensaje = null, $fecha_envio = null, $tipo_usuario = null, $fecha_visto = false)
    {
        $db = new Conexion();
        $this->conn = $db->conexion;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function getFechaEnvio()
    {
        return $this->fecha_envio;
    }

    public function setFechaEnvio($fecha_envio)
    {
        $this->fecha_envio = $fecha_envio;
    }

    public function getTipoUsuario()
    {
        return $this->tipo_usuario;
    }

    public function setTipoUsuario($tipo_usuario)
    {
        $this->tipo_usuario = $tipo_usuario;
    }

    public function getFechaVisto()
    {
        return $this->fecha_visto;
    }

    public function setFechaVisto($fecha_visto)
    {
        $this->fecha_visto = $fecha_visto;
    }

    public function obtenerNotificaciones($pageIndex = 1, $pageSize = 200)
    {
        try {
            // Calcular el offset según la página solicitada
            $offset = ($pageIndex - 1) * $pageSize;

            // Consulta SQL para obtener las notificaciones
            $query = "SELECT * FROM " . $this->table_name . " LIMIT ?, ?";
            $stmt = $this->conn->prepare($query);

            // Vincular parámetros para la consulta
            $stmt->bind_param('ii', $offset, $pageSize);
            $stmt->execute();

            // Obtener el resultado
            $result = $stmt->get_result();

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
                $notificaciones = [];

                // Recoger los resultados en un arreglo
                while ($row = $result->fetch_assoc()) {
                    $notificaciones[] = $row;
                }

                // Devolver las notificaciones
                return $notificaciones;
            } else {
                return []; // Si no hay resultados, devolver un arreglo vacío
            }

            // Cerrar la declaración
            $stmt->close();
        } catch (Exception $e) {
            // Registrar cualquier error
            error_log("Error en obtenerNotificaciones: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerNotificacionesPorId($pageIndex = 1, $pageSize = 200, $usuario_id)
    {
        try {
            // Calcular el offset según la página solicitada
            $offset = ($pageIndex - 1) * $pageSize;

            // Consulta SQL para obtener las notificaciones
            $query = "SELECT * FROM " . $this->table_name . " where usuario_id = ? LIMIT ?, ?";
            $stmt = $this->conn->prepare($query);

            // Vincular parámetros para la consulta
            $stmt->bind_param('iii', $usuario_id, $offset, $pageSize);
            $stmt->execute();

            // Obtener el resultado
            $result = $stmt->get_result();

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
                $notificaciones = [];

                // Recoger los resultados en un arreglo
                while ($row = $result->fetch_assoc()) {
                    $notificaciones[] = $row;
                }

                // Devolver las notificaciones
                return $notificaciones;
            } else {
                return []; // Si no hay resultados, devolver un arreglo vacío
            }

            // Cerrar la declaración
            $stmt->close();
        } catch (Exception $e) {
            // Registrar cualquier error
            error_log("Error en obtenerNotificaciones: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerNotificacionesActivasPorId($pageIndex = 1, $pageSize = 200, $usuario_id, $activa)
    {
        try {
            // Calcular el offset según la página solicitada
            $offset = ($pageIndex - 1) * $pageSize;

            // Consulta SQL para obtener las notificaciones
            $query = "SELECT * FROM " . $this->table_name . " where usuario_id = ? AND visto = ? LIMIT ?, ?";
            $stmt = $this->conn->prepare($query);

            // Vincular parámetros para la consulta
            $stmt->bind_param('iiii', $usuario_id, $activa, $offset, $pageSize);
            $stmt->execute();

            // Obtener el resultado
            $result = $stmt->get_result();

            // Verificar si hay resultados
            if ($result->num_rows > 0) {
                $notificaciones = [];

                // Recoger los resultados en un arreglo
                while ($row = $result->fetch_assoc()) {
                    $notificaciones[] = $row;
                }

                // Devolver las notificaciones
                return $notificaciones;
            } else {
                return []; // Si no hay resultados, devolver un arreglo vacío
            }

            // Cerrar la declaración
            $stmt->close();
        } catch (Exception $e) {
            // Registrar cualquier error
            error_log("Error en obtenerNotificaciones: " . $e->getMessage());
            return false;
        }
    }


    public function update()
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET usuario_id = ?, titulo = ?, mensaje = ?, tipo_usuario = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('isssi', $this->usuario_id, $this->titulo, $this->mensaje, $this->tipo_usuario, $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }

    //Este método elimina una notificación
    public function delete()
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }

    //Este método marca una notificación como vista
    public function actualizarVisto($id, $visto)
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET fecha_visto = NOW(), visto = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ii', $visto, $id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en markAsSeen: " . $e->getMessage());
            return false;
        }
    }

    //Este método crea una notificación
    public function crearNotificacion($usuario_id, $titulo, $mensaje, $tipo_usuario)
    {
        try {
            $query = "INSERT INTO " . $this->table_name . " (usuario_id, titulo, mensaje, fecha_envio, tipo_usuario, fecha_visto) VALUES (?, ?, ?, NOW(), ?, false)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('isss', $usuario_id, $titulo, $mensaje, $tipo_usuario);

            if ($stmt->execute()) {
                $this->id = $stmt->insert_id;
                return [
                    'id' => $this->id,
                    'usuario_id' => $usuario_id,
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'fecha_envio' => date('Y-m-d H:i:s'),
                    'tipo_usuario' => $tipo_usuario,
                    'fecha_visto' => false
                ];
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en crearNotificacion: " . $e->getMessage());
            return false;
        }
    }

    public function enviarNotification($usuario_id, $titulo, $mensaje, $tipo_usuario)
    {

        $tokensDestino = [];
        try {
            $sql = "SELECT * FROM tokens WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return [
                    'status'  => 'error',
                    'message' => 'No se encontró token para ese usuario.'
                ];
            }

            while ($row = $result->fetch_assoc()) {
                $tokensDestino[] = $row['token'];
            }
            $stmt->close();
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }

        try {
            $this->setUsuarioId($usuario_id);
            $this->setTitulo($titulo);
            $this->setMensaje($mensaje);
            $this->setTipoUsuario($tipo_usuario);
            $this->crearNotificacion($usuario_id, $titulo, $mensaje, $tipo_usuario);
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }

        $serviceAccountPath = __DIR__ . '/credenciales.json';
        $factory  = (new Factory)->withServiceAccount($serviceAccountPath);
        $messaging = $factory->createMessaging();

        $tokensNotificados = [];
        foreach ($tokensDestino as $token) {
            $message = CloudMessage::new()
                ->withData([
                    'title' => $titulo,
                    'body' => $mensaje,
                    'icon' => 'http://localhost:8080/img/dosxdoslogoNuevoRojo.png',
                    'click_action' => 'http://localhost:8080/notificaciones.html',
                    'url' => 'http://localhost:8080/notificaciones.html'
                ])
                ->withChangedTarget('token', $token);


            try {
                $messaging->send($message);
                $tokensNotificados[] = $token;
            } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                $sqlDel = "DELETE FROM tokens WHERE token = ?";
                $stmtDel = $this->conn->prepare($sqlDel);
                $stmtDel->bind_param("s", $token);
                $stmtDel->execute();
                $stmtDel->close();
            }
        }

        return [
            'status'  => 'success',
            'message' => count($tokensNotificados) . ' notificaciones enviadas exitosamente',
            'tokensNotificados' => $tokensNotificados
        ];
    }
    //Este método elimina un token de notificación
    public function eliminarNotificacionToken($token)
    {
        try {
            $query = "DELETE FROM tokens WHERE BINARY token = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('s', $token);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

<?php
// Importar clases y librerías necesarias
require_once __DIR__ . '/../clases/notificaciones_clase.php';

class NotificacionesControlador {

    // Atributos
    private $notificaciones;
    //Constructor
    public function __construct() {
        // Importar el modelo de las notificaciones
        $this->notificaciones = new Notificaciones();
    }
    
    // Método para obtener todas las notificaciones
    public function obtenerNotificaciones($pageIndex, $pageSize) {
        // Lógica para obtener todas las notificaciones
        $resultado = $this->notificaciones->obtenerNotificaciones($pageIndex, $pageSize);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para obtener una notificación por ID
    public function obtenerNotificacionPorId($pageIndex, $pageSize, $id) {
        // Lógica para obtener una notificación por ID
        $resultado = $this->notificaciones->obtenerNotificacionesPorId($pageIndex, $pageSize, $id);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para obtener una notificación por ID
    public function obtenerNotificacionActivaPorId($pageIndex, $pageSize, $id, $activa) {
        // Lógica para obtener una notificación por ID
        $resultado = $this->notificaciones->obtenerNotificacionesActivasPorId($pageIndex, $pageSize, $id, $activa);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para crear una nueva notificación
    public function crearNotificacion($datos) {
        // Lógica para crear una nueva notificación
        $resultado = $this->notificaciones->crearNotificacion($datos['usuario_id'], $datos['titulo'], $datos['mensaje'], $datos['tipo_usuario']);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para crear una nueva notificación
    public function enviarNotificacion($datos) {
        // Lógica para enviar una nueva notificación
        $enviarNotificacion = $this->notificaciones->enviarNotification($datos['usuario_id'], $datos['titulo'], $datos['mensaje'], $datos['tipo_usuario']);
        return $enviarNotificacion;
    }

    // Método para actualizar una notificación existente
    public function actualizarNotificacion($id, $datos) {
        // Lógica para actualizar una notificación existente
    }

    // Método para eliminar una notificación
    public function eliminarNotificacion($id) {
        // Lógica para eliminar una notificación
    }
}
?>
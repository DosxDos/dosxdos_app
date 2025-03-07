<?php
// Importar clases y librerías necesarias
require_once __DIR__ . '/../clases/notificaciones_clase.php';
require_once __DIR__ . '/../clases/crm_clase.php';
require_once __DIR__ . '/../clases/zoho_clase.php';

class NotificacionesControlador
{

    // Atributos
    private $notificaciones;

    private $crm;
    //Constructor
    public function __construct()
    {
        // Importar el modelo de las notificaciones
        $this->notificaciones = new Notificaciones();
        $this->crm = new CRM();
    }

    // Método para obtener todas las notificaciones
    public function obtenerNotificaciones($pageIndex, $pageSize)
    {
        // Lógica para obtener todas las notificaciones
        $resultado = $this->notificaciones->obtenerNotificaciones($pageIndex, $pageSize);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para obtener una notificación por ID
    public function obtenerNotificacionPorId($pageIndex, $pageSize, $id)
    {
        // Lógica para obtener una notificación por ID
        $resultado = $this->notificaciones->obtenerNotificacionesPorId($pageIndex, $pageSize, $id);
        //ordenar notificaciones por fecha
        $resultado = $this->ordenarNotificacionesPorFecha($resultado);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para obtener una notificación por ID
    public function obtenerNotificacionActivaPorId($pageIndex, $pageSize, $id, $activa)
    {
        // Lógica para obtener una notificación por ID
        $resultado = $this->notificaciones->obtenerNotificacionesActivasPorId($pageIndex, $pageSize, $id, $activa);
        //ordenar notificaciones por fecha
        $resultado = $this->ordenarNotificacionesPorFecha($resultado);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para crear una nueva notificación
    public function crearNotificacion($datos)
    {
        // Lógica para crear una nueva notificación
        $resultado = $this->notificaciones->crearNotificacion($datos['usuario_id'], $datos['titulo'], $datos['mensaje'], $datos['tipo_usuario']);
        // Retornar el resultado en formato JSON
        return $resultado;
    }

    // Método para crear una nueva notificación
    public function enviarNotificacion($datos)
    {
        // Verificar si usuario_id es un array de enteros
        if (is_array($datos['usuario_id']) && array_filter($datos['usuario_id'], 'is_int') === $datos['usuario_id']) {
            // Llamar a enviarNotificacionesMultiples si usuario_id es un array
            return $this->enviarNotificacionesMultiples($datos);
        } else {
            // Llamar a enviarNotification si usuario_id no es un array
            $enviarNotificacion = $this->notificaciones->enviarNotification($datos['usuario_id'], $datos['titulo'], $datos['mensaje'], $datos['tipo_usuario']);
            return $enviarNotificacion;
        }
    }

    // Método para enviar notificaciones a múltiples usuarios
    public function enviarNotificacionesMultiples($datos)
    {
        // Verificar si usuario_id es un array de enteros
        if (is_array($datos['usuario_id']) && array_filter($datos['usuario_id'], 'is_int') === $datos['usuario_id']) {
            $resultados = [];
            foreach ($datos['usuario_id'] as $usuario_id) {
                // Enviar notificación a cada usuario
                $resultado = $this->notificaciones->enviarNotification($usuario_id, $datos['titulo'], $datos['mensaje'], $datos['tipo_usuario']);
                if ($resultado['status'] == 'success') {
                    $resultados['tokensNotificados'] = array_merge($resultados['tokensNotificados'] ?? [], $resultado['tokensNotificados']);
                } else {
                    $resultados['tokensNotificados'] = array_merge($resultados['tokensNotificados'] ?? [], array_map(function ($token) {
                        return 'error ' . $token;
                    }, $resultado['tokensNotificados']));
                }
            }
            if (isset($resultados['tokensNotificados'])) {
                $resultados['status'] = 'success';
                $resultados['message'] = 'Notificaciones enviadas a los usuarios';
            } else {
                $resultados['status'] = 'error';
                $resultados['message'] = 'No se pudo enviar las notificaciones a ninguno de los usuarios';
            }
            // Retornar los resultados en formato JSON
            return $resultados;
        } else {
            return ['status' => 'error', 'message' => 'usuario_id debe ser un array de enteros'];
        }
    }

    // Método que sirve para saber si un cliente a visualizado una notificación
    public function actualizarVisto($id, $visto)
    {
        // Lógica para actualizar una visualización de una notificación
        $resultado = $this->notificaciones->actualizarVisto($id, $visto);
        // Retornar el resultado en formato JSON
        if ($resultado) {
            return ['status' => 'success', 'message' => 'Se actualizó el estado de la notificación ' . $id];
        } else {
            return ['status' => 'error', 'message' => 'No se pudo actualizar el estado de la notificación'];
        }
    }

    // Método para eliminar una notificación
    public function eliminarNotificacionToken($token)
    {
        // Lógica para eliminar el token de una notificación
        $resultado = $this->notificaciones->eliminarNotificacionToken($token);
        // Retornar el resultado en formato JSON
        if ($resultado) {
            return ['status' => 'success', 'message' => 'Se eliminó el token de la notificación ' . $token];
        } else {
            return ['status' => 'error', 'message' => 'No se pudo eliminar el token de la notificación'];
        }
    }

    // Método para eliminar una notificación 
    public function enviarNotificacionLineaNueva($idLinea, $faseRuta)
    {
        //===================Primera consulta Zoho para obtener datos de la ruta===================

        $crmDatos = $this->obtenerDatosRuta($faseRuta);
        if (isset($crmDatos['respuestaError'])) {
            return ['status' => 'error', 'message' => 'No se pudo obtener los datos de la ruta'];
        }
        $idRuta = json_encode($crmDatos['respuesta'][1]['data'][0]['id']);

        $confirmada = true;
        //===================Se requiere verificar la linea confirmada y se recogen los campos de la línea para mandarlos al montador===================

        list($confirmada, $camposLinea) = $this->obtenerConfirmada($idLinea, $camposLinea);

        if ($confirmada === false) {
            return ['status' => 'error', 'message' => 'No se pudo obtener el estado de la línea'];
        }
        //===================Segunda consulta Zoho cambiar datos de la línea para eso primero recoger los datos===================

        $actualizarLinea = $this->actualizarLinea($idLinea, $confirmada);

        // Si no se actualizan los datos correctamente, retornar un mensaje de error
        if (!$actualizarLinea) {
            return ["status" => "error", "message" => "Error al actualizar el registro."];
        }

        //===================Obtener los montadores de una ruta=======================================
        $datosAsociadosRuta = $this->obtenerDatosMontadoreAsociadosRuta($idRuta);

        if(isset($datosAsociadosRuta['status']) && $datosAsociadosRuta['status'] === 'error'){
            return $datosAsociadosRuta;
        }

        //===================Recoger el idApp de los montadores a través de la linea==================

        $idAppMontadores = $this->obtenerIdAppMontadoresDeIdLinea($datosAsociadosRuta);

        if(isset($idAppMontadores['status']) && $idAppMontadores['status'] === 'error'){
            return $idAppMontadores;
        }

        //===================Enviar notificación a los montadores==================
 
        $datos = [
            'usuario_id' => $idAppMontadores,
            'titulo' => 'Nueva línea agregada en la ruta ' . $faseRuta . ' con el código: ' . $camposLinea['Codigo_de_l_nea'],
            'mensaje' => 'Datos de línea: '.$camposLinea['Product_Name'],
            'tipo_usuario' => 'montador'
        ];

        $enviarNotificaciones = $this->enviarNotificacion($datos);

        if ($enviarNotificaciones['status'] === 'error') {
            return ["status" => "error", "message" => "Error al enviar las notificaciones a los montadores."];
        }

        $datosEncapsulados = [
            'idLinea' => $idLinea,
            'idRuta' =>  json_decode($idRuta),
            'confirmada' => $confirmada,
            'actualizarLinea' => $actualizarLinea,
            'datosAsociadosRuta' => $datosAsociadosRuta,
            'idAppMontadores' => $idAppMontadores,
            'enviarNotificaciones' => $enviarNotificaciones,
            'datos_enviados_montador' => $datos,
            'status' => 'success'
        ];
        
        return $datosEncapsulados;
    }

    // Método para eliminar una notificación 
    public function enviarNotificacionLineaUrgencia($idLinea, $faseRuta)
    {
        //===================Primera consulta Zoho para obtener datos de la ruta===================

        $crmDatos = $this->obtenerDatosRuta($faseRuta);
        if (isset($crmDatos['respuestaError'])) {
            return ['status' => 'error', 'message' => 'No se pudo obtener los datos de la ruta'];
        }
        $idRuta = json_encode($crmDatos['respuesta'][1]['data'][0]['id']);

        $confirmada = true;
        //===================Se requiere verificar la linea confirmada y se recogen los campos de la línea para mandarlos al montador===================

        list($confirmada, $camposLinea) = $this->obtenerConfirmadaUrgente($idLinea, $camposLinea);

        if ($confirmada === false) {
            return ['status' => 'error', 'message' => 'No se pudo obtener el estado de la línea'];
        }
        //===================Segunda consulta Zoho cambiar datos de la línea para eso primero recoger los datos===================

        /*
        $actualizarLinea = $this->actualizarLineaUrgente($idLinea, $confirmada);

        // Si no se actualizan los datos correctamente, retornar un mensaje de error
        if (!$actualizarLinea) {
            return ["status" => "error", "message" => "Error al actualizar el registro."];
        }
        */

        //===================Obtener los montadores de una ruta=======================================
        $datosAsociadosRuta = $this->obtenerDatosMontadoreAsociadosRuta($idRuta);

        if(isset($datosAsociadosRuta['status']) && $datosAsociadosRuta['status'] === 'error'){
            return $datosAsociadosRuta;
        }

        //===================Recoger el idApp de los montadores a través de la linea==================

        $idAppMontadores = $this->obtenerIdAppMontadoresDeIdLinea($datosAsociadosRuta);

        if(isset($idAppMontadores['status']) && $idAppMontadores['status'] === 'error'){
            return $idAppMontadores;
        }

        //===================Enviar notificación a los montadores==================
 
        $datos = [
            'usuario_id' => $idAppMontadores,
            'titulo' => '"LÍNEA URGENTE" en la ruta ' . $faseRuta . ' con el código: ' . $camposLinea['Codigo_de_l_nea'],
            'mensaje' => 'Datos de línea: '.$camposLinea['Product_Name'],
            'tipo_usuario' => 'montador'
        ];

        $enviarNotificaciones = $this->enviarNotificacion($datos);

        if ($enviarNotificaciones['status'] === 'error') {
            return ["status" => "error", "message" => "Error al enviar las notificaciones a los montadores."];
        }

        $datosEncapsulados = [
            'idLinea' => $idLinea,
            'idRuta' =>  json_decode($idRuta),
            'confirmada' => $confirmada,
            'datosAsociadosRuta' => $datosAsociadosRuta,
            'idAppMontadores' => $idAppMontadores,
            'enviarNotificaciones' => $enviarNotificaciones,
            'datos_enviados_montador' => $datos,
            'status' => 'success'
        ];
        
        return $datosEncapsulados;
    }

    // Método para enviar una notificación cada vez que una ruta se a abierto
    public function enviarNotificacionRutaAbierta($faseRuta)
    {
        //===================Primera consulta Zoho para obtener datos de la ruta===================

        $crmDatos = $this->obtenerDatosRutaAbierta($faseRuta);
        if (isset($crmDatos['respuestaError'])) {
            return ['status' => 'error', 'message' => 'No se pudo obtener los datos de la ruta'];
        }
        $idRuta = json_encode($crmDatos['respuesta'][1]['data'][0]['id']);

        $confirmada = true;
        //===================Obtener los montadores de una ruta=======================================
        $datosAsociadosRuta = $this->obtenerDatosMontadoreAsociadosRuta($idRuta);

        if(isset($datosAsociadosRuta['status']) && $datosAsociadosRuta['status'] === 'error'){
            return $datosAsociadosRuta;
        }

        //===================Recoger el idApp de los montadores a través de la linea==================

        $idAppMontadores = $this->obtenerIdAppMontadoresDeIdLinea($datosAsociadosRuta);

        if(isset($idAppMontadores['status']) && $idAppMontadores['status'] === 'error'){
            return $idAppMontadores;
        }

        //===================Enviar notificación a los montadores==================
 
        $datos = [
            'usuario_id' => $idAppMontadores,
            'titulo' => 'Se ha abierto la ruta ' . $faseRuta,
            'mensaje' => 'La ruta está abierta y se van a ir añadiendo líneas en la ruta: ' . $faseRuta,
            'tipo_usuario' => 'montador'
        ];

        $enviarNotificaciones = $this->enviarNotificacion($datos);

        if ($enviarNotificaciones['status'] === 'error') {
            return ["status" => "error", "message" => "Error al enviar las notificaciones a los montadores."];
        }

        $datosEncapsulados = [
            'idRuta' =>  json_decode($idRuta),
            'datosAsociadosRuta' => $datosAsociadosRuta,
            'idAppMontadores' => $idAppMontadores,
            'enviarNotificaciones' => $enviarNotificaciones,
            'datos_enviados_montador' => $datos,
            'status' => 'success'
        ];
        
        return $datosEncapsulados;
    }

    // Método para eliminar una notificación 
    public function enviarNotificacionRutaCerrada($faseRuta)
    {
        //===================Primera consulta Zoho para obtener datos de la ruta===================

        $crmDatos = $this->obtenerDatosRuta($faseRuta);
        if (isset($crmDatos['respuestaError'])) {
            return ['status' => 'error', 'message' => 'No se pudo obtener los datos de la ruta'];
        }
        $idRuta = json_encode($crmDatos['respuesta'][1]['data'][0]['id']);

        $confirmada = true;
        //===================Obtener los montadores de una ruta=======================================
        $datosAsociadosRuta = $this->obtenerDatosMontadoreAsociadosRuta($idRuta);

        if(isset($datosAsociadosRuta['status']) && $datosAsociadosRuta['status'] === 'error'){
            return $datosAsociadosRuta;
        }

        //===================Recoger el idApp de los montadores a través de la linea==================

        $idAppMontadores = $this->obtenerIdAppMontadoresDeIdLinea($datosAsociadosRuta);

        if(isset($idAppMontadores['status']) && $idAppMontadores['status'] === 'error'){
            return $idAppMontadores;
        }

        //===================Enviar notificación a los montadores==================
 
        $datos = [
            'usuario_id' => $idAppMontadores,
            'titulo' => 'Se ha cerrado la ruta ' . $faseRuta,
            'mensaje' => 'La ruta está cerrada, las líneas que se añadan a la ruta tendrán sólo la categoría de urgente o nueva: ' . $faseRuta,
            'tipo_usuario' => 'montador'
        ];

        $enviarNotificaciones = $this->enviarNotificacion($datos);

        if ($enviarNotificaciones['status'] === 'error') {
            return ["status" => "error", "message" => "Error al enviar las notificaciones a los montadores."];
        }

        $datosEncapsulados = [
            'idRuta' =>  json_decode($idRuta),
            'confirmada' => $confirmada,
            'datosAsociadosRuta' => $datosAsociadosRuta,
            'idAppMontadores' => $idAppMontadores,
            'enviarNotificaciones' => $enviarNotificaciones,
            'datos_enviados_montador' => $datos,
            'status' => 'success'
        ];
        
        return $datosEncapsulados;
    }

    public function obtenerIdAppMontadoresDeIdLinea($datosAsociadosRuta)
    {
        $idAppMontadores = [];
        $camposRutas = "Montadores_relacionados";
        $crmDatos = new Zoho;
        //$query = "SELECT Name, Montadores_relacionados.id FROM Rutas WHERE Name = 'TENERIFE_(A)'"; //esto esta cerrada tiene que ser true
        // Enviar la solicitud POST
        $crmDatos->get("/crm/v2/Montadores?fields=idApp");
        //solucionar el error Cannot use object of type Crm as array
        $crmDatos = json_encode($crmDatos);
        $crmDatos = json_decode($crmDatos, true); // Decode JSON string into an array

        //Esta función filtra los montadores que se encuentran en la ruta
        $idMontadoresRelacionados = $this->obtenerAppIdMontadores($crmDatos, $datosAsociadosRuta);
        //solucionar el error Cannot use object of type Crm as array
        $idMontadoresRelacionados = json_encode($idMontadoresRelacionados);
        $idMontadoresRelacionados = json_decode($idMontadoresRelacionados, true); // Decode JSON string into an array

        return $idMontadoresRelacionados;
    }

    public function obtenerAppIdMontadores($crmDatos, $datosAsociadosRuta)
    {
        $idAppMontadores = [];
        foreach ($crmDatos['respuesta']['data'] as $montador) {
            for ($i=0; $i < count($datosAsociadosRuta); $i++) {
                if ($montador['id'] == $datosAsociadosRuta[$i]) {
                    $idAppMontadores[] = $montador['idApp'];
                }
            }
        }
        if (empty($idAppMontadores)) {
            return ['status' => 'error', 'message' => 'No se encontraron montadores asociados a la ruta'];
        }
        //echo json_encode($idMontadoresRelacionados);
        return $idAppMontadores;
    }

    public function actualizarLinea($idLinea, $confirmada)
    {
        $zohoDatos = new Zoho;
        //Crear la estructura básica de JSON con un array en "data"
        $data = [
            "data" => [
                [
                    "Linea_nueva_en_Ruta" => $confirmada,  // ID del Lead o Contacto
                ],
            ]
        ];

        $dataJson = json_encode($data);

        $zohoDatos = $zohoDatos->put("/crm/v6/Products/$idLinea", $dataJson);
        // Verificar que la respuesta es válida (no es un error)
        if (isset($zohoDatos[1]['data'][0]['code']) && $zohoDatos[1]['data'][0]['code'] === 'SUCCESS') {
            // Si 'data' es 'SUCCESS' entonces se actualizó correctamente
            return true;
        }
        return false;
    }

    public function actualizarLineaUrgente($idLinea, $confirmada)
    {
        $zohoDatos = new Zoho;
        //Crear la estructura básica de JSON con un array en "data"
        $data = [
            "data" => [
                [
                    "Urgente" => $confirmada,  // ID del Lead o Contacto
                ],
            ]
        ];

        $dataJson = json_encode($data);

        $zohoDatos = $zohoDatos->put("/crm/v6/Products/$idLinea", $dataJson);
        // Verificar que la respuesta es válida (no es un error)
        if (isset($zohoDatos[1]['data'][0]['code']) && $zohoDatos[1]['data'][0]['code'] === 'SUCCESS') {
            // Si 'data' es 'SUCCESS' entonces se actualizó correctamente
            return true;
        }
        return false;
    }

    // Función para obtener y modificar el campo "Confirmada" de un producto dado el ID
    public function obtenerConfirmada($idLinea, &$camposLinea)
    {
        $crmDatos = new Crm;
        // Inicializar la clase CRM
        //codigo de linea en el titulo
        //Nombre de la linea en el mensaje
        $camposLineas = "Linea_nueva_en_Ruta,Codigo_de_l_nea,Product_Name";

        // Consulta SQL-like para obtener datos
        $query = "SELECT $camposLineas FROM Products WHERE id = $idLinea";

        // Enviar la solicitud POST
        $crmDatos->query($query);

        // Obtener los datos en formato JSON
        $datosArray = json_encode($crmDatos);

        // Convertir los datos JSON a array PHP
        $datosArray = json_decode($datosArray, true);

        $camposLinea = $datosArray['respuesta'][1]['data'][0];

        // Verificar si el campo 'Confirmada' existe en la respuesta y modificarlo
        if (isset($datosArray['respuesta'][1]['data'])) {
            $confirmada = $datosArray['respuesta'][1]['data'][0]['Linea_nueva_en_Ruta'] = true; // Establecer confirmada a true
        }

        // Retornar el valor modificado de 'Confirmada' y los campos de la línea
        return [$confirmada ?? false, $camposLinea]; // Retorna el valor de Confirmada o false si no se encontró, y los campos de la línea
    }

    // Función para obtener y modificar el campo "Confirmada" de un producto dado el ID
    public function obtenerConfirmadaUrgente($idLinea, &$camposLinea)
    {
        $crmDatos = new Crm;
        // Inicializar la clase CRM
        //codigo de linea en el titulo
        //Nombre de la linea en el mensaje
        $camposLineas = "Urgente,Codigo_de_l_nea,Product_Name";

        // Consulta SQL-like para obtener datos
        $query = "SELECT $camposLineas FROM Products WHERE id = $idLinea";

        // Enviar la solicitud POST
        $crmDatos->query($query);

        // Obtener los datos en formato JSON
        $datosArray = json_encode($crmDatos);

        // Convertir los datos JSON a array PHP
        $datosArray = json_decode($datosArray, true);

        $camposLinea = $datosArray['respuesta'][1]['data'][0];

        // Verificar si el campo 'Confirmada' existe en la respuesta y modificarlo
        if (isset($datosArray['respuesta'][1]['data'])) {
            $confirmada = $datosArray['respuesta'][1]['data'][0]['Urgente'] = true; // Establecer confirmada a true
        }

        // Retornar el valor modificado de 'Confirmada' y los campos de la línea
        return [$confirmada ?? false, $camposLinea]; // Retorna el valor de Confirmada o false si no se encontró, y los campos de la línea
    }
    //hacer consulta a Zoho para obtener datos de la ruta
    public function obtenerDatosRuta($faseRuta)
    {
        $camposRutas = "Cerrada, Email, Secondary_Email, Created_By, Tag, Fecha_cerrada, Record_Image, Modified_By, Email_Opt_Out, Name, Owner";
        $crmDatos = new Crm;
        $query = "SELECT $camposRutas FROM Rutas where Name = $faseRuta AND Cerrada = true"; //esto esta cerrada tiene que ser true
        // Enviar la solicitud POST
        $crmDatos->query($query);
        //solucionar el error Cannot use object of type Crm as array
        $crmDatos = json_encode($crmDatos);
        $crmDatos = json_decode($crmDatos, true); // Decode JSON string into an array

        return $crmDatos;
    }

    //hacer consulta a Zoho para obtener datos de la ruta
    public function obtenerDatosRutaAbierta($faseRuta)
    {
        $camposRutas = "Cerrada, Email, Secondary_Email, Created_By, Tag, Fecha_cerrada, Record_Image, Modified_By, Email_Opt_Out, Name, Owner";
        $crmDatos = new Crm;
        $query = "SELECT $camposRutas FROM Rutas where Name = $faseRuta AND Cerrada = false"; //esto esta cerrada tiene que ser true
        // Enviar la solicitud POST
        $crmDatos->query($query);
        //solucionar el error Cannot use object of type Crm as array
        $crmDatos = json_encode($crmDatos);
        $crmDatos = json_decode($crmDatos, true); // Decode JSON string into an array

        return $crmDatos;
    }

    //obtiene los montadores asociados a una ruta
    public function obtenerDatosMontadoreAsociadosRuta($idRuta)
    {
        $camposRutas = "Montadores_relacionados";
        $crmDatos = new Zoho;
        //$query = "SELECT Name, Montadores_relacionados.id FROM Rutas WHERE Name = 'TENERIFE_(A)'"; //esto esta cerrada tiene que ser true
        // Enviar la solicitud POST
        $crmDatos->get("/crm/v2/Montadores_Rutas?fields=Rutas,Montadores_relacionados");
        //solucionar el error Cannot use object of type Crm as array
        $crmDatos = json_encode($crmDatos);
        $crmDatos = json_decode($crmDatos, true); // Decode JSON string into an array

        //Esta función filtra los montadores que se encuentran en la ruta
        $idMontadoresRelacionados = $this->obtenerIdMontadoresRelacionados($crmDatos, $idRuta);
        //solucionar el error Cannot use object of type Crm as array
        $idMontadoresRelacionados = json_encode($idMontadoresRelacionados);
        $idMontadoresRelacionados = json_decode($idMontadoresRelacionados, true); // Decode JSON string into an array

        return $idMontadoresRelacionados;
    }

    public function obtenerIdMontadoresRelacionados($crmDatos, $idRuta)
    {
        $idMontadoresRelacionados = [];
        $cadena_limpia = trim($idRuta, '"'); // Elimina las comillas dobles
        $idRutaInt = intval($cadena_limpia);
        foreach ($crmDatos['respuesta']['data'] as $montador) {
            if ($montador['Rutas']['id'] == $idRutaInt) {
                $idMontadoresRelacionados[] = $montador['Montadores_relacionados']['id'];
            }
        }
        if (empty($idMontadoresRelacionados)) {
            return ['status' => 'error', 'message' => 'No se encontraron montadores asociados a la ruta'];
        }
        //echo json_encode($idMontadoresRelacionados);
        return $idMontadoresRelacionados;
    }



    //=============Estos Métodos son genéricos para todas las clases controladoras================

    //Método de burbuja ordena de menor fecha a mayor fecha
    public function ordenarNotificacionesPorFecha($notificaciones)
{
    $n = count($notificaciones);
    // Ordenamiento burbuja
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            // Si la fecha en $j es más reciente que la fecha en $j+1, intercambiamos
            if (strtotime($notificaciones[$j]['fecha_envio']) > strtotime($notificaciones[$j + 1]['fecha_envio'])) {
                // Intercambiar las notificaciones
                $temp = $notificaciones[$j];
                $notificaciones[$j] = $notificaciones[$j + 1];
                $notificaciones[$j + 1] = $temp;
            }
        }
    }
    return $notificaciones;
}   

}

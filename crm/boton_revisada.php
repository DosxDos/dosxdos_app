<?php
// Incluye el archivo que contiene la clase Crm. __DIR__ asegura que se use la ruta correcta relativa a este archivo.
require_once __DIR__ . '/crm.php';

// Indica que la respuesta será en formato JSON
header('Content-Type: application/json');

// Verifica que la petición sea de tipo POST. Si no lo es, responde con error y termina la ejecución.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['estado' => false, 'mensajeError' => 'Método no permitido']);
    exit;
}

// Captura el cuerpo de la solicitud en crudo (raw input)
$inputRaw = file_get_contents("php://input");

// Intenta decodificar el JSON recibido a un array asociativo
$input = json_decode($inputRaw, true);

// Verifica si el JSON es válido. Si no lo es, responde con error.
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['estado' => false, 'mensajeError' => 'JSON inválido']);
    exit;
}

// Extrae el ID de línea y el valor del checkbox (true o false)
$id = $input['id'] ?? null;
$revisado = $input['revisado'] ?? false;

// Si no se recibió un ID válido, responde con error
if (empty($id)) {
    echo json_encode(['estado' => false, 'mensajeError' => 'ID no recibido']);
    exit;
}

// Crea una nueva instancia del CRM
$crm = new Crm();

// Prepara los datos en formato JSON que el método `actualizar` espera
$json = json_encode([
    'data' => [[
        'id' => $id,
        'Revisado' => $revisado // Aquí se asume que el campo en la base de datos o API se llama "Revisado"
    ]]
]);

// Llama al método del CRM para actualizar la línea con los datos dados
$crm->actualizar('actualizarLinea', $json);

// Si la actualización fue exitosa, responde con estado true
if ($crm->estado) {
    echo json_encode(['estado' => true]);
} else {
    // Si hubo un error, responde con el mensaje y detalle del error
    echo json_encode([
        'estado' => false,
        'mensajeError' => $crm->mensajeError,
        'respuestaError' => $crm->respuestaError
    ]);
}

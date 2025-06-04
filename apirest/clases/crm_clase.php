<?php


require_once __DIR__ . '\respuestas_clase.php';
require_once __DIR__ . '\zoho_clase.php';

/*
require_once 'respuestas_clase.php';
require_once 'zoho_clase.php';
*/

class Crm extends Zoho
{
    private $respuestas;
    private $urlGet;
    private $urlQuery = "/crm/v5/coql";
    private $camposEmpresas = "Alias,C_d_vendedor,Rating,CIF_NIF1,Shipping_City,Billing_City,Contacto1,Contacto_relacionado,Correo_electr_nico,Created_By,C_d_forma_pago,C_digo_t_rminos_pago,Shipping_Code,Billing_Code,SIC_Code,C_digo_vendedor,Description,Shipping_Street,Billing_Street,Employees,Parent_Account,Shipping_State,Billing_State,Tag,Fax,Grupo_contable_cliente,Grupo_contable,Grupo_registro_IVA_neg,Record_Image,Annual_Revenue,Modified_By,Navision_nr,Account_Name,N1,Account_Number,Shipping_Country,Billing_Country,Ownership,Owner,Industry,Account_Site,Website,Ticker_Symbol,Phone,Phone_2,Tipo_de_empresa,Account_Type";
    private $camposOts = "Comentario,Completo,Created_By,C_digo,Departamentos_relacionados,Description,Empresa,Tag,Stage,Closing_Date,Fecha_de_previsi_n_de_OT,Firma,Fotos_de_la_OT,Lead_Source,Amount,Expected_Revenue,Associated_Products,Modified_By,Reason_For_Loss__s,Motivo_de_p_rdida2,Navision,Necesita_Presupuesto,Contact_Name,Account_Name,Deal_Name,Notion_ID,N_de_l_neas_relacionadas,N_mero_de_operaciones,Observaciones,PDV,PDV_relacionados,Prefijo,Related_quote,Subtipo_de_la_OT,Type,Tipo_de_cliente,Tipo_de_OT,Usuario_creador_Navision";
    private $camposMontajeImagenes = "Rango1,Rango2,Precio,idA3Erp";
    private $camposPreciosLogos = "Rango1,Rango2,Precio,idA3Erp";
    private $camposPreciosLogosMontaje = "Rango1,Rango2,Precio,idA3Erp";
    private $camposDescuentosLogosMontaje = "Isla,PDVS_minimo,Porcentaje";
    private $camposMaterialesServicios = "Precio,idA3Erp,Material,Abreviatura";
    private $camposAcabados = "Precio,idA3Erp,Acabado";
    private $camposRutas = "Name";
    private $camposMontadores = "Apellido_del_montador,C_digo_del_montador,idApp,Name,Tel_fono_del_montador";
    public $estado = true; // Determina si hubo éxito en la operación
    public $respuesta;  // Almacena la respuesta si todo sale bien
    public $respuestaError; // Almacena la respuesta de error si algo falla
    public $mensajeError; // Almacena el mensaje de error si algo falla

    // Si falla, cambia el estado a false y guarda los detalles del error
    function __construct()
    {
        try {
            $this->respuestas = new Respuestas;
        } catch (\Throwable $th) {
            $this->estado = false;
            $this->mensajeError = $th->getMessage();
            $this->respuestaError = [];
            $this->respuestaError[0] = false;
            $this->respuestaError[1] = "Error al crear el objeto Crm: " . $this->mensajeError;
            $this->respuestaError[2] = 500;
        }
    }
    //Getters y Setters para la Query
    public function getUrlQuery()
    {
        return $this->urlQuery;
    }

    public function setUrlQuery($urlQuery)
    {
        $this->urlQuery = $urlQuery;
    }
 // Ejecuta una consulta en Zoho CRM usando la API de CoQL
 // CoQL significa "Zoho CRM Query Language" y permite realizar consultas complejas en los datos de Zoho CRM.
    public function query($query)
    {
        try {
            $zoho = new Zoho; // Crea una instancia de la clase Zoho para manejar las solicitudes a la API de Zoho CRM
            $datosJson = []; // Inicializa un array para almacenar los datos de la consulta
            $datosJson['select_query'] = $query; // Asigna la consulta SQL al array de datos
            $json = json_encode($datosJson, JSON_FORCE_OBJECT); // Convierte el array a formato JSON, asegurando que se trate como un objeto JSON
            $datos = $zoho->post($this->urlQuery, $json); // Realiza una solicitud POST a la API de Zoho
            if ($datos[0]) {
                $this->respuesta = $datos;
            } else {
                $this->estado = false;
                $this->mensajeError = $datos[1];
                $this->respuestaError = $datos;
            }
        } catch (\Throwable $th) {
            $this->estado = false;
            $this->mensajeError = $th->getMessage();
            $this->respuestaError = [];
            $this->respuestaError[0] = false;
            $this->respuestaError[1] = "Error en el objeto CRM, en la función query: " . $this->mensajeError;
            $this->respuestaError[2] = 500;
        }
    }
    // Asigna un valor a la propiedad $urlGet según el tipo de variable solicitada
    // Luego, realiza una solicitud GET a la API de Zoho CRM para obtener datos específicos
    public function get($variable, $link = "")
    {
        try {
            $zoho = new Zoho;
            switch ($variable) {
                case 'modulos':
                    $this->urlGet = "/crm/v5/settings/modules";
                    break;

                case 'empresas':
                    $this->urlGet = "/crm/v5/Accounts?fields=" . $this->camposEmpresas;
                    break;

                case 'ots':
                    $this->urlGet = "/crm/v5/Deals?fields=" . $this->camposOts;
                    break;

                case 'preciosMontajeImagenes':
                    $this->urlGet = "/crm/v5/Precio_Montaje_Im_genes?fields=" . $this->camposMontajeImagenes;
                    break;

                case 'preciosLogos':
                    $this->urlGet = "/crm/v5/Precios_Logos?fields=" . $this->camposPreciosLogos;
                    break;

                case 'preciosLogosMontaje':
                    $this->urlGet = "/crm/v5/Precios_Montaje_Logos?fields=" . $this->camposPreciosLogosMontaje;
                    break;

                case 'descuentosLogosMontaje':
                    $this->urlGet = "/crm/v5/Descuentos_montaje_logos?fields=" . $this->camposDescuentosLogosMontaje;
                    break;

                case 'materialesServicios':
                    $this->urlGet = "/crm/v5/Precios_Materiales?fields=" . $this->camposMaterialesServicios;
                    break;

                case 'acabados':
                    $this->urlGet = "/crm/v5/Precios_Acabados?fields=" . $this->camposAcabados;
                    break;

                case 'rutas':
                    $this->urlGet = "/crm/v5/Rutas?fields=" . $this->camposRutas;
                    break;

                case 'montadores':
                    $this->urlGet = "/crm/v5/Montadores?fields=" . $this->camposMontadores;
                    break;

                default:
                    $this->urlGet = $link;
                    break;
            }
            $datos = $zoho->get($this->urlGet);
            if ($datos[0]) {
                $this->respuesta = $datos;
            } else {
                $this->estado = false;
                $this->mensajeError = $datos[1];
                $this->respuestaError = $datos;
            }
        } catch (\Throwable $th) {
            $this->estado = false;
            $this->mensajeError = $th->getMessage();
            $this->respuestaError = [];
            $this->respuestaError[0] = false;
            $this->respuestaError[1] = "Error en el objeto CRM, en la función modulos: " . $this->mensajeError;
            $this->respuestaError[2] = 500;
        }
    }
    // Asigna un endpoint variable y luego realiza una solicitud GET a la API de Zoho CRM para eliminar registros específicos
    public function eliminar($variable, $ids)
    {
        try {
            $zoho = new Zoho;
            switch ($variable) {
                case 'lineas':
                    $this->urlGet = "/crm/v6/Products?wf_trigger=true&ids=" . $ids;
                    break;

                case 'empresas':
                    $this->urlGet = "/crm/v5/Accounts?fields=" . $this->camposEmpresas;
                    break;

                case 'ots':
                    $this->urlGet = "/crm/v5/Deals?fields=" . $this->camposOts;
                    break;
            }
            $datos = $zoho->delete($this->urlGet);
            if ($datos[0]) {
                $this->respuesta = $datos;
            } else {
                $this->estado = false;
                $this->mensajeError = $datos[1];
                $this->respuestaError = $datos;
            }
        } catch (\Throwable $th) {
            $this->estado = false;
            $this->mensajeError = $th->getMessage();
            $this->respuestaError = [];
            $this->respuestaError[0] = false;
            $this->respuestaError[1] = "Error en el objeto CRM, en la función modulos: " . $this->mensajeError;
            $this->respuestaError[2] = 500;
        }
    }
    // Asigna un endpoint variable y luego realiza una solicitud PUT a la API de Zoho CRM para actualizar registros específicos
    public function actualizar($variable, $json)
    {
        try {
            $zoho = new Zoho;
            switch ($variable) {
                case 'actualizarOt':
                    $this->urlGet = "/crm/v6/Deals";
                    break;

                case 'actualizarLinea':
                    $this->urlGet = "/crm/v6/Products";
                    break;

                case 'actualizarPdvs':
                    $this->urlGet = "/crm/v5/Puntos_de_venta";
                    break;
            }
            $datos = $zoho->put($this->urlGet, $json);
            if (isset($datos[1]['data'][0]['status']) && isset($datos[1]['data'][0]['code']) && $datos[1]['data'][0]['status'] == 'error' && $datos[1]['data'][0]['code'] == 'RECORD_IN_BLUEPRINT') {
                $datos = $zoho->put($this->urlGet, $json);
            }
            if ($datos[0]) {
                if (isset($datos[1]['data'][0]['status']) && $datos[1]['data'][0]['status'] == 'error') {
                    $this->estado = false;
                    $this->mensajeError = $datos[1];
                    $this->respuestaError = $datos;
                } else {
                    $this->respuesta = $datos;
                }
            } else {
                $this->estado = false;
                $this->mensajeError = $datos[1];
                $this->respuestaError = $datos;
            }
        } catch (\Throwable $th) {
            $this->estado = false;
            $this->mensajeError = $th->getMessage();
            $this->respuestaError = [];
            $this->respuestaError[0] = false;
            $this->respuestaError[1] = "Error en el objeto CRM, en la función modulos: " . $this->mensajeError;
            $this->respuestaError[2] = 500;
        }
    }
    // Asigna un endpoint variable y luego realiza una solicitud PUT a la API de Zoho CRM para crear nuevos registros
    public function agregar($variable, $json)
    {
        try {
            $zoho = new Zoho;
            switch ($variable) {
                case 'actualizarOt':
                    $this->urlGet = "/crm/v6/settings/Deals";
                    break;

                case 'empresas':
                    $this->urlGet = "/crm/v5/Accounts?fields=" . $this->camposEmpresas;
                    break;

                case 'ots':
                    $this->urlGet = "/crm/v5/Deals?fields=" . $this->camposOts;
                    break;

                case 'bulkRead':
                    $this->urlGet = "/crm/bulk/v3/read";
                    break;

                default:
                    $this->urlGet = $variable;
            }
            $datos = $zoho->post($this->urlGet, $json);
            if (isset($datos[1]['data'][0]['status']) && isset($datos[1]['data'][0]['code']) && $datos[1]['data'][0]['status'] == 'error' && $datos[1]['data'][0]['code'] == 'RECORD_IN_BLUEPRINT') {
                $datos = $zoho->post($this->urlGet, $json);
            }
            if ($datos[0]) {
                if (isset($datos[1]['data'][0]['status']) && $datos[1]['data'][0]['status'] == 'error') {
                    $this->estado = false;
                    $this->mensajeError = $datos[1];
                    $this->respuestaError = $datos;
                } else {
                    $this->respuesta = $datos;
                }
            } else {
                $this->estado = false;
                $this->mensajeError = $datos[1];
                $this->respuestaError = $datos;
            }
        } catch (\Throwable $th) {
            $this->estado = false;
            $this->mensajeError = $th->getMessage();
            $this->respuestaError = [];
            $this->respuestaError[0] = false;
            $this->respuestaError[1] = "Error en el objeto CRM, en la función modulos: " . $this->mensajeError;
            $this->respuestaError[2] = 500;
        }
    }
    // Realiza una solicitud a la API de Zoho para descargas masivas de datos
    public function bulkFile($link) {
        $zoho = new Zoho;
        return $zoho->getBulkFile($link);
    }
    public function getRelacionados($modulo, $id, $modulorelacionado)
{
    try {
        $zoho = new Zoho;

        // Armamos la URL de la lista relacionada
        $url = "/crm/v5/{$modulo}/{$id}/{$modulorelacionado}";

        // Usamos el método get() de la clase base
        $datos = $zoho->get($url);

        // Verificamos si hubo error
        if (!$datos || isset($datos['code'])) {
            $this->estado = false;
            $this->respuestaError = $datos;
            $this->mensajeError = isset($datos['message']) ? $datos['message'] : "Error al consultar lista relacionada";
        } else {
            $this->estado = true;
            $this->respuesta = $datos;
        }
    } catch (\Throwable $th) {
        $this->estado = false;
        $this->respuestaError = [];
        $this->mensajeError = "Error interno: " . $th->getMessage();
    }
}

}
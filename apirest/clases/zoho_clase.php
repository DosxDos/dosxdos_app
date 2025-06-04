<?php

require_once 'respuestas_clase.php';
require_once "conexion_clase.php";

class Zoho extends conexion
{
    private $codigo;
    private $cliente;
    private $tokens;
    private $dominioApi;
    private $dominioCliente;
    private $masDatos = false; //Maneja la paginación si hay más datos disponibles
    public $datos = [];
    public $respuesta;
    private $respuestaFinal;

    function __construct()
    {
        try {
            $cliente = __DIR__ . '\cliente_zoho.json'; // Ruta al archivo cliente_zoho.json
            $this->cliente = file_get_contents($cliente); // Lee el contenido del archivo zoho
            $this->cliente = json_decode($this->cliente, true); // Decodifica el JSON a un array asociativo
            $token = __DIR__ . '\tokens_zoho.json';
            $this->tokens = file_get_contents($token);
            $this->tokens = json_decode($this->tokens, true);
            $codigo = __DIR__ . '\code_zoho.json';
            $this->codigo = file_get_contents($codigo);
            $this->codigo = json_decode($this->codigo, true);
            $this->dominioApi = $this->tokens['api_domain']; // Asigna URLs base de la API
            $this->dominioCliente = $this->codigo['accounts-server']; // Asigna URLs base del cliente
            $this->respuestaFinal = new Respuestas; // Instancia de la clase Respuestas para manejar las respuestas de la API
        } catch (\Throwable $th) {
            $this->respuesta = "Error al construir la clase de Zoho: " . $th->getMessage(); // En caso de error, asigna un mensaje de error a la propiedad respuesta
        }
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    private function renovarToken()
    {
        try {
            $urlNuevoToken = $this->dominioCliente . '/oauth/v2/token'; // URL para renovar el token de acceso
            // Se prepara un array con los campos necesarios para la solicitud de renovación del token
            $fields = array(
                'refresh_token' => $this->tokens['refresh_token'],
                'client_id' => $this->cliente['client_id'],
                'client_secret' => $this->cliente['client_secret'],
                'grant_type' => 'refresh_token'
            );
            // Se inicializa cURL para realizar la solicitud POST a la API de Zoho
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $urlNuevoToken); // Establece la URL para la solicitud cURL
            curl_setopt($curl, CURLOPT_POST, true); // Indica que se realizará una solicitud POST
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($fields)); // Convierte el array de campos a una cadena de consulta y la establece como los datos POST
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Indica que se debe devolver la respuesta como una cadena en lugar de imprimirla directamente
            $response = curl_exec($curl); // Ejecuta la solicitud cURL y almacena la respuesta
            curl_close($curl); // Cierra la sesión cURL para liberar recursos
            //print($response);
            // Si la respuesta es falsa, significa que hubo un error en la solicitud
            if (!$response) {
                $this->respuesta = 'Error de seguridad en la API de Zoho al renovar el token; ahora es necesario renovar la autorización de la API de Zoho';
                return false;
            } else {
                $this->respuesta = json_decode($response, true); // Si la respuesta es válida, decodifica el JSON a un array asociativo
                if (isset($this->respuesta['access_token'])) { //Si tiene access_token, actualiza los datos
                    $this->tokens['access_token'] = $this->respuesta['access_token']; // Actualiza el token de acceso
                    $this->tokens['scope'] = $this->respuesta['scope']; // Actualiza el scope del token
                    $this->tokens['api_domain'] = $this->respuesta['api_domain']; // Actualiza el dominio de la API
                    $this->tokens['token_type'] = $this->respuesta['token_type']; // Actualiza el tipo de token
                    $this->tokens['expires_in'] = $this->respuesta['expires_in']; // Actualiza el tiempo de expiración del token
                    $jsonTokens = json_encode($this->tokens); // Guarda los tokens actualizados en formato JSON
                    $tokensFile = __DIR__ . '\tokens_zoho.json'; //Crea la ruta al archivo tokens_zoho.json
                    if (file_put_contents($tokensFile, $jsonTokens) === false) {
                        $this->respuesta = "Error al escribir el token de acceso.";
                        return false;
                    } else {
                        $this->respuesta = "Token escrito correctamente";
                        return true;
                    }
                } else {
                    if (isset($this->respuesta['error'])) {
                        $this->respuesta = "Error en la api de Zoho al solicitar el nuevo token: " . $this->respuesta['error'];
                    } else {
                        $this->respuesta = "Error en la api de Zoho al solicitar el nuevo token";
                    }
                    return false;
                }
            }
        } catch (\Throwable $th) {
            $this->respuesta = "Error en la función renovarToken: " . $th->getMessage();
            return false;
        }
    }

    public function get($link)
    {
        try {
            $url = $this->dominioApi . $link; // Construye la URL completa para la solicitud GET
            $accessToken = $this->tokens['access_token']; // Obtiene el token de acceso del array de tokens
            $authorizationHeader = "Authorization: Zoho-oauthtoken $accessToken"; // Crea el encabezado de autorización con el token de acceso
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader)); // Establece el encabezado de autorización en la solicitud cURL
            $response = curl_exec($curl);
            curl_close($curl);
            $this->respuesta = json_decode($response, true); // Después de ejecutar la petición GET, decodifica la respuesta JSON a un array asociativo
            // Si el token está vencido intentamos renovarlo y volver a hacer la petición
            if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                $this->respuesta = "Token de acceso inválido en la api de Zoho";
                $renovar = $this->renovarToken();
                if ($renovar) {
                    return $this->get($link);
                } else {
                    $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                    return $respuestaFinal;
                }
            } else if ($this->respuesta === false) {
                $this->respuesta = "Error en la comunicación con la API de Zoho: " . curl_error($curl);
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else if (isset($this->respuesta['status']) && $this->respuesta['status'] == 'error') {
                $this->respuesta = "Error en la API de Zoho: " . $this->respuesta['code'] . " " . $this->respuesta['message'];
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else {
                if (isset($this->respuesta['info']['more_records'])) { // Verifica si hay más registros disponibles
                    if ($this->respuesta['info']['more_records']) { // Si hay más registros, maneja la paginación
                        $datos = $this->respuesta['data'];
                        $this->masDatos = true;
                        $page = 2;
                        while ($this->masDatos) { // Mientras haya más datos, sigue solicitando páginas adicionales
                            $pagina = '&page=' . $page;
                            $url = $this->dominioApi . $link . $pagina;
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_HTTPGET, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
                            $response = curl_exec($curl);
                            curl_close($curl);
                            $this->respuesta = json_decode($response, true);
                            if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                                $this->respuesta = "Token de acceso inválido en la api de Zoho";
                                $renovar = $this->renovarToken();
                                if ($renovar) {
                                    $curl = curl_init();
                                    curl_setopt($curl, CURLOPT_URL, $url);
                                    curl_setopt($curl, CURLOPT_HTTPGET, true);
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $this->respuesta = json_decode($response, true);
                                    if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                                        $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                                    }
                                } else {
                                    $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                                    return $respuestaFinal;
                                }
                            } else if ($this->respuesta === false) {
                                $this->respuesta = "Error en la comunicación con la API de Zoho: " . curl_error($curl);
                                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                                return $respuestaFinal;
                            } else if (isset($this->respuesta['status']) && $this->respuesta['status'] == 'error') {
                                $this->respuesta = "Error en la API de Zoho: " . $this->respuesta['code'] . " " . $this->respuesta['message'];
                                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                                return $respuestaFinal;
                            } else {
                                $datos = array_merge($datos, $this->respuesta['data']);
                                if ($this->respuesta['info']['more_records']) {
                                    $this->masDatos = true;
                                    $page++;
                                } else {
                                    $this->masDatos = false;
                                }
                            }
                        }
                        $respuestaFinal = $this->respuestaFinal->ok($datos); // Cuándo no hayan más páginas, responde con todos los datos obtenidos
                        return $respuestaFinal;
                    } else {
                        $respuestaFinal = $this->respuestaFinal->ok($this->respuesta);
                        return $respuestaFinal;
                    }
                } else {
                    $respuestaFinal = $this->respuestaFinal->ok($this->respuesta);
                    return $respuestaFinal;
                }
            }
        } catch (\Throwable $th) {
            $this->respuesta = "Error en la función get: " . $th->getMessage();
            $respuestaFinal = $this->respuestaFinal->error_500($this->respuesta);
            return $respuestaFinal;
        }
    }
// Función para descargar un archivo .zip de la API de Zoho
    public function getBulkFile($link)
    {
        try {
            $url = $this->dominioApi . $link;
            $accessToken = $this->tokens['access_token'];
            $authorizationHeader = "Authorization: Zoho-oauthtoken $accessToken";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
            $response = curl_exec($curl);
            curl_close($curl);
            if ($response === false) {
                $renovar = $this->renovarToken();
                if ($renovar) {
                    return $this->getBulkFile($link);
                } else {
                    return false;
                }
            } else {
                // Guardar el archivo .zip en una ubicación temporal
                $tempZipPath = 'temp_data.zip';
                if (file_put_contents($tempZipPath, $response)) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function put($link, $json)
    {
        try {
            $url = $this->dominioApi . $link; // Construye la URL completa para la solicitud PUT en función del módulo
            $accessToken = $this->tokens['access_token'];
            $authorizationHeader = "Authorization: Zoho-oauthtoken $accessToken";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                $authorizationHeader,
                "Content-Type: application/json",
            ));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json); // Establece los datos JSON a enviar en la solicitud PUT
            $response = curl_exec($curl);
            curl_close($curl);
            $this->respuesta = json_decode($response, true);
            if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                $this->respuesta = "Token de acceso inválido en la api de Zoho";
                $renovar = $this->renovarToken();
                if ($renovar) {
                    return $this->put($link, $json);
                } else {
                    $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                    return $respuestaFinal;
                }
            } else if ($this->respuesta === false) {
                $this->respuesta = "Error en la comunicación con la API de Zoho: " . curl_error($curl);
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else if (isset($this->respuesta['status']) && $this->respuesta['status'] == 'error') {
                $this->respuesta = "Error en la API de Zoho: " . $this->respuesta['code'] . " " . $this->respuesta['message'];
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else {
                $respuestaFinal = $this->respuestaFinal->ok($this->respuesta);
                return $respuestaFinal;
            }
        } catch (\Throwable $th) {
            $this->respuesta = "Error en la función put: " . $th->getMessage();
            $respuestaFinal = $this->respuestaFinal->error_500($this->respuesta);
            return $respuestaFinal;
        }
    }

    public function post($link, $json)
    {
        try {
            $url = $this->dominioApi . $link;
            $accessToken = $this->tokens['access_token'];
            $authorizationHeader = "Authorization: Zoho-oauthtoken $accessToken";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                $authorizationHeader,
                "Content-Type: application/json",
            ));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            $response = curl_exec($curl);
            curl_close($curl);
            $this->respuesta = json_decode($response, true);
            if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                $this->respuesta = "Token de acceso inválido en la api de Zoho";
                $renovar = $this->renovarToken();
                if ($renovar) {
                    return $this->post($link, $json);
                } else {
                    $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                    return $respuestaFinal;
                }
            } else if ($this->respuesta === false) {
                $this->respuesta = "Error en la comunicación con la API de Zoho: " . curl_error($curl);
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else if (isset($this->respuesta['status']) && $this->respuesta['status'] == 'error') {
                $this->respuesta = "Error en la API de Zoho: " . $this->respuesta['code'] . " " . $this->respuesta['message'];
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else {
                if (isset($this->respuesta['info']['more_records'])) {
                    if ($this->respuesta['info']['more_records']) {
                        $datos = $this->respuesta['data'];
                        $this->masDatos = true;
                        $page = 2;
                        while ($this->masDatos) {
                            $pagina = '&page=' . $page;
                            $url = $this->dominioApi . $link . $pagina;
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_HTTPGET, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
                            $response = curl_exec($curl);
                            curl_close($curl);
                            $this->respuesta = json_decode($response, true);
                            if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                                $this->respuesta = "Token de acceso inválido en la api de Zoho";
                                $renovar = $this->renovarToken();
                                if ($renovar) {
                                    $curl = curl_init();
                                    curl_setopt($curl, CURLOPT_URL, $url);
                                    curl_setopt($curl, CURLOPT_HTTPGET, true);
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $this->respuesta = json_decode($response, true);
                                    if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                                        $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                                    }
                                } else {
                                    $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                                    return $respuestaFinal;
                                }
                            } else if ($this->respuesta === false) {
                                $this->respuesta = "Error en la comunicación con la API de Zoho: " . curl_error($curl);
                                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                                return $respuestaFinal;
                            } else if (isset($this->respuesta['status']) && $this->respuesta['status'] == 'error') {
                                $this->respuesta = "Error en la API de Zoho: " . $this->respuesta['code'] . " " . $this->respuesta['message'];
                                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                                return $respuestaFinal;
                            } else {
                                $datos = array_merge($datos, $this->respuesta['data']);
                                if ($this->respuesta['info']['more_records']) {
                                    $this->masDatos = true;
                                    $page++;
                                } else {
                                    $this->masDatos = false;
                                }
                            }
                        }
                        $respuestaFinal = $this->respuestaFinal->ok($datos);
                        return $respuestaFinal;
                    } else {
                        $respuestaFinal = $this->respuestaFinal->ok($this->respuesta);
                        return $respuestaFinal;
                    }
                } else {
                    $respuestaFinal = $this->respuestaFinal->ok($this->respuesta);
                    return $respuestaFinal;
                }
            }
        } catch (\Throwable $th) {
            $this->respuesta = "Error en la función post: " . $th->getMessage();
            $respuestaFinal = $this->respuestaFinal->error_500($this->respuesta);
            return $respuestaFinal;
        }
    }

    public function delete($link)
    {
        try {
            $url = $this->dominioApi . $link;
            $accessToken = $this->tokens['access_token'];
            $authorizationHeader = "Authorization: Zoho-oauthtoken $accessToken";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
            $response = curl_exec($curl);
            curl_close($curl);
            $this->respuesta = json_decode($response, true);
            if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                $this->respuesta = "Token de acceso inválido en la api de Zoho";
                $renovar = $this->renovarToken();
                if ($renovar) {
                    return $this->get($link);
                } else {
                    $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                    return $respuestaFinal;
                }
            } else if ($this->respuesta === false) {
                $this->respuesta = "Error en la comunicación con la API de Zoho: " . curl_error($curl);
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else if (isset($this->respuesta['status']) && $this->respuesta['status'] == 'error') {
                $this->respuesta = "Error en la API de Zoho: " . $this->respuesta['code'] . " " . $this->respuesta['message'];
                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                return $respuestaFinal;
            } else {
                if (isset($this->respuesta['info']['more_records'])) {
                    if ($this->respuesta['info']['more_records']) {
                        $datos = $this->respuesta['data'];
                        $this->masDatos = true;
                        $page = 2;
                        while ($this->masDatos) {
                            $pagina = '&page=' . $page;
                            $url = $this->dominioApi . $link . $pagina;
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_HTTPGET, true);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
                            $response = curl_exec($curl);
                            curl_close($curl);
                            $this->respuesta = json_decode($response, true);
                            if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                                $this->respuesta = "Token de acceso inválido en la api de Zoho";
                                $renovar = $this->renovarToken();
                                if ($renovar) {
                                    $curl = curl_init();
                                    curl_setopt($curl, CURLOPT_URL, $url);
                                    curl_setopt($curl, CURLOPT_HTTPGET, true);
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorizationHeader));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $this->respuesta = json_decode($response, true);
                                    if (isset($this->respuesta['code']) && $this->respuesta['code'] == 'INVALID_TOKEN') {
                                        $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                                    }
                                } else {
                                    $respuestaFinal = $this->respuestaFinal->error_401($this->respuesta);
                                    return $respuestaFinal;
                                }
                            } else if ($this->respuesta === false) {
                                $this->respuesta = "Error en la comunicación con la API de Zoho: " . curl_error($curl);
                                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                                return $respuestaFinal;
                            } else if (isset($this->respuesta['status']) && $this->respuesta['status'] == 'error') {
                                $this->respuesta = "Error en la API de Zoho: " . $this->respuesta['code'] . " " . $this->respuesta['message'];
                                $respuestaFinal = $this->respuestaFinal->okF($this->respuesta);
                                return $respuestaFinal;
                            } else {
                                $datos = array_merge($datos, $this->respuesta['data']);
                                if ($this->respuesta['info']['more_records']) {
                                    $this->masDatos = true;
                                    $page++;
                                } else {
                                    $this->masDatos = false;
                                }
                            }
                        }
                        $respuestaFinal = $this->respuestaFinal->ok($datos);
                        return $respuestaFinal;
                    } else {
                        $respuestaFinal = $this->respuestaFinal->ok($this->respuesta);
                        return $respuestaFinal;
                    }
                } else {
                    $respuestaFinal = $this->respuestaFinal->ok($this->respuesta);
                    return $respuestaFinal;
                }
            }
        } catch (\Throwable $th) {
            $this->respuesta = "Error en la función get: " . $th->getMessage();
            $respuestaFinal = $this->respuestaFinal->error_500($this->respuesta);
            return $respuestaFinal;
        }
    }
}

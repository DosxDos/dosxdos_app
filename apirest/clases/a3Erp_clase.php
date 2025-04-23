<?php
class a3Erp
{
    private $baseUrl;
    private $apiKey;
    public $error = null;

    function __construct()
    {
        try {
            $listadatos = $this->datosConstructor();

            if (isset($listadatos['urlApi']) && isset($listadatos['apiKey'])) {
                $this->baseUrl = $listadatos['urlApi'];
                $this->apiKey = $listadatos['apiKey'];
            } else {
                throw new Exception("La clave 'urlApi' o la 'apiKey' no está presente en jwt.json");
            }
        } catch (Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
        }
    }

    private function datosConstructor()
    {
        try {
            $direccion = dirname(__FILE__);
            $jsondata = file_get_contents($direccion . "/a3Erp.json");
            $data = json_decode($jsondata, true);
            return $data;
        } catch (Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
        }
    }

    private function execute($method, $endpoint, $data = [], $extraHeaders = [])
    {

        try {
            $url = $this->baseUrl . ltrim($endpoint, '/');
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

            if (in_array(strtoupper($method), ['POST', 'PUT', 'DELETE']) && !empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $extraHeaders[] = 'Content-Type: application/json';
            }

            $headers = array_merge([
                'ApiKey: ' . $this->apiKey,
                'Accept: application/json;odata=verbose'
            ], $extraHeaders);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);

            curl_close($ch);

            $decoded = json_decode($response, true);

            return $decoded;
        } catch (Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
            return null;
        }
    }

    public function get($endpoint, $headers = [])
    {
        return $this->execute('GET', $endpoint, [], $headers);
    }

    public function post($endpoint, $data = [], $headers = [])
    {
        return $this->execute('POST', $endpoint, $data, $headers);
    }

    public function put($endpoint, $data = [], $headers = [])
    {
        return $this->execute('PUT', $endpoint, $data, $headers);
    }

    public function delete($endpoint, $data = [], $headers = [])
    {
        return $this->execute('DELETE', $endpoint, $data, $headers);
    }
}

/*
$a3Erp = new a3Erp();
if ($a3Erp->error) {
    var_dump($a3Erp->error);
} else {
    $response = $a3Erp->get('cliente?externalFields=true');
    var_dump($response);
}
*/

/*
$a3Erp = new a3Erp();
if ($a3Erp->error) {
    var_dump($a3Erp->error);
} else {
    $filter = "NIF eq 'A28050359'";
    $endpoint = "cliente?externalFields=true&" . urlencode('$filter') . "=" . urlencode($filter);
    $response = $a3Erp->get($endpoint);
    $cliente = $response['PageData'][0];
    print_r($cliente);
}
*/


/*
$a3Erp = new a3Erp();
$a3Erp = new a3Erp();
if ($a3Erp->error) {
    var_dump($a3Erp->error);
} else {
    $data = [
        "Fecha" => "2025-04-22T16:11:30",
        "CodigoCliente" => "8",
        "Lineas" => [
            [
                "CodigoArticulo" => "C000002",
                "Unidades" => "1"
            ],
            [
                "CodigoArticulo" => "C000003",
                "Unidades" => "1"
            ]
        ]
    ];
    $response = $a3Erp->post('pedidoVenta', $data);
    var_dump($response);
}
*/

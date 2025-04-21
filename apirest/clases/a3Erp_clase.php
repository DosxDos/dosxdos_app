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
            foreach ($listadatos as $key => $value) {
                $this->baseUrl = $value['urlApi'];
                $this->apiKey = $value['apiKey'];
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
                'ApiKey: ' . $this->apiKey
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

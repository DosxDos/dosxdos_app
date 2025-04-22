<?php

require_once __DIR__ . '\..\..\vendor\autoload.php';


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class JwtHandler
{
    private string $clave;
    public bool $validacion = false;
    public array|null $payload = null;
    public array|null $error = null;

    function __construct()
    {
        try {
            $listadatos = $this->datosConstructor();

            if (isset($listadatos['secret'])) {
                $this->clave = $listadatos['secret'];
            } else {
                throw new Exception("La clave 'secret' no está presente en jwt.json");
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
            $jsondata = file_get_contents($direccion . "/jwt.json");
            $data = json_decode($jsondata, true);
            if (!is_array($data)) {
                throw new Exception("JSON de datos inválido o vacío");
            }
            return $data;
        } catch (Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
            return []; // 💡 Devuelve array vacío explícitamente
        }
    }

    public function generarToken(array $datos, string|null $duracion = '15m')
    {
        try {
            $claims = array_merge($datos, ['iat' => time()]);

            // Si se pasa duración explícita como null, NO agregar exp
            if ($duracion !== null) {
                $claims['exp'] = $this->calcularExpiracion($duracion);
            }

            return JWT::encode($claims, $this->clave, 'HS256');
        } catch (\Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
            return null;
        }
    }


    public function validarToken(string $token)
    {
        try {
            $this->validacion = false;
            $this->payload = null;
            $this->error = null;

            $decodificado = JWT::decode($token, new Key($this->clave, 'HS256'));
            $claims = (array)$decodificado;

            // Si tiene exp, validamos expiración manualmente
            if (isset($claims['exp']) && time() > $claims['exp']) {
                $this->error = [
                    'line' => 82,
                    'file' => "jwtHandler.php",
                    'message' => "Token expirado"
                ];
                return false;
            }

            $this->validacion = true;
            $this->payload = $claims;
            return true;
        } catch (ExpiredException $e) {
            $this->error = [
                'line' => $e->getLine() | 92,
                'file' => $e->getFile() | "jwtHandler.php",
                'message' => $e->getMessage() | "Token expirado"
            ];
            return false;
        } catch (\Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
            return false;
        }
    }

    private function calcularExpiracion(string $duracion)
    {
        try {
            $tipo = strtolower(substr($duracion, -1));
            $valor = (int)substr($duracion, 0, -1);

            return match ($tipo) {
                'm' => time() + ($valor * 60),
                'h' => time() + ($valor * 3600),
                'd' => time() + ($valor * 86400),
                default => time() + 900 // 15min por defecto
            };
        } catch (\Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
            return null;
        }
    }
}

/*
$jwt = new JwtHandler();
$token = $jwt->generarToken(['usuario_id' => "CRM"], 1m);
var_dump($token);
if ($jwt->error) {
    var_dump($jwt->error);
}
*/

/*
$jwt = new JwtHandler();
$token = $jwt->validarToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c3VhcmlvX2lkIjoiQ1JNIiwiaWF0IjoxNzQ1MzI5NDkxLCJleHAiOjE3NDUzMjk1NTF9.e0llebg_Wq4RN-ePaAKl_uVwtWzrLitxk4lHrhgp6A8");
var_dump($token);
var_dump($jwt->validacion);
if ($jwt->error) {
    var_dump($jwt->error);
} else {
    var_dump($jwt->payload);
}
*/

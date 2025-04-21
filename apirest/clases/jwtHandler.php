<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class JwtHandler
{
    private string $clave;
    public bool $validacion = false;
    public array|null $payload = null;
    public $error = null;

    function __construct()
    {
        try {
            $listadatos = $this->datosConstructor();
            foreach ($listadatos as $key => $value) {
                $this->clave = $value['secret'];
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
            return $data;
        } catch (Throwable $e) {
            $this->error = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function generarToken(array $datos, string|null $duracion = null): string
    {
        $claims = array_merge($datos, ['iat' => time()]);

        // Si no se pasa duración, usamos 15 minutos por defecto
        if ($duracion === null) {
            $duracion = '15min';
        }

        if (strtolower($duracion) !== 'none') {
            $claims['exp'] = $this->calcularExpiracion($duracion);
        }

        return JWT::encode($claims, $this->clave, 'HS256');
    }


    public function validarToken(string $token): void
    {
        $this->validacion = false;
        $this->payload = null;
        $this->error = null;

        try {
            $decodificado = JWT::decode($token, new Key($this->clave, 'HS256'));
            $claims = (array)$decodificado;

            // Si tiene exp, validamos expiración manualmente
            if (isset($claims['exp']) && time() > $claims['exp']) {
                $this->error = 'Token expirado';
                return;
            }

            $this->validacion = true;
            $this->payload = $claims;
        } catch (ExpiredException $e) {
            $this->error = 'Token expirado';
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    private function calcularExpiracion(string $duracion): int
    {
        $tipo = strtolower(substr($duracion, -1));
        $valor = (int)substr($duracion, 0, -1);

        return match ($tipo) {
            'm' => time() + ($valor * 60),
            'h' => time() + ($valor * 3600),
            'd' => time() + ($valor * 86400),
            default => time() + 900 // 15min por defecto
        };
    }
}

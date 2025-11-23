<?php

namespace OrionERP\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'orionerp_secret_key_change_in_production';
    }

    public function generarToken(int $usuarioId, string $rol): string
    {
        $payload = [
            'usuario_id' => $usuarioId,
            'rol' => $rol,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 horas
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validarToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}


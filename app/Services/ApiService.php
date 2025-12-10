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

    public function generarTokenRefresh(int $usuarioId): string
    {
        $payload = [
            'usuario_id' => $usuarioId,
            'tipo' => 'refresh',
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24 * 30) // 30 días
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function renovarToken(string $refreshToken): ?string
    {
        $decoded = $this->validarToken($refreshToken);
        
        if (!$decoded || ($decoded['tipo'] ?? '') !== 'refresh') {
            return null;
        }

        // Obtener rol del usuario desde la base de datos
        $db = \OrionERP\Core\Database::getInstance();
        $usuario = $db->fetchOne(
            "SELECT rol FROM usuarios WHERE id = ?",
            [$decoded['usuario_id']]
        );

        if (!$usuario) {
            return null;
        }

        return $this->generarToken($decoded['usuario_id'], $usuario['rol']);
    }

    public function revocarToken(string $token): bool
    {
        // En una implementación real, se guardaría el token en una blacklist
        // Por ahora, simplemente validamos que el token sea válido
        return $this->validarToken($token) !== null;
    }
}


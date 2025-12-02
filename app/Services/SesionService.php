<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class SesionService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function crearSesion(int $usuarioId, string $token, string $ipAddress, string $userAgent, int $duracionHoras = 24): int
    {
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$duracionHoras hours"));
        
        $this->db->query(
            "INSERT INTO sesiones (usuario_id, token, ip_address, user_agent, activa, expires_at) 
             VALUES (?, ?, ?, ?, 1, ?)",
            [$usuarioId, $token, $ipAddress, $userAgent, $expiresAt]
        );

        return (int) $this->db->lastInsertId();
    }

    public function cerrarSesion(string $token): bool
    {
        $this->db->query(
            "UPDATE sesiones SET activa = 0 WHERE token = ?",
            [$token]
        );
        return true;
    }

    public function cerrarTodasLasSesiones(int $usuarioId): bool
    {
        $this->db->query(
            "UPDATE sesiones SET activa = 0 WHERE usuario_id = ?",
            [$usuarioId]
        );
        return true;
    }

    public function getSesionesActivas(int $usuarioId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM sesiones 
             WHERE usuario_id = ? AND activa = 1 AND expires_at > NOW()
             ORDER BY created_at DESC",
            [$usuarioId]
        );
    }

    public function limpiarSesionesExpiradas(): int
    {
        $this->db->query(
            "UPDATE sesiones SET activa = 0 WHERE expires_at < NOW() AND activa = 1"
        );
        
        $result = $this->db->fetchOne(
            "SELECT ROW_COUNT() as total"
        );
        
        return (int) ($result['total'] ?? 0);
    }
}


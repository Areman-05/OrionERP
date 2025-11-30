<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class SeguridadService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function registrarIntentoLogin(string $email, bool $exitoso, string $ipAddress): void
    {
        $this->db->query(
            "INSERT INTO logs (accion, tabla, datos_nuevos, ip_address) 
             VALUES (?, ?, ?, ?)",
            [
                $exitoso ? 'login_exitoso' : 'login_fallido',
                'usuarios',
                json_encode(['email' => $email]),
                $ipAddress
            ]
        );
    }

    public function verificarIntentosFallidos(string $ipAddress, int $maxIntentos = 5, int $ventanaMinutos = 15): bool
    {
        $fechaInicio = date('Y-m-d H:i:s', strtotime("-$ventanaMinutos minutes"));
        
        $intentos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM logs 
             WHERE accion = 'login_fallido' 
             AND ip_address = ? 
             AND created_at >= ?",
            [$ipAddress, $fechaInicio]
        );
        
        return ($intentos['total'] ?? 0) < $maxIntentos;
    }

    public function bloquearIP(string $ipAddress, int $minutos = 30): void
    {
        $this->db->query(
            "INSERT INTO logs (accion, tabla, datos_nuevos, ip_address) 
             VALUES (?, ?, ?, ?)",
            [
                'ip_bloqueada',
                'seguridad',
                json_encode(['minutos' => $minutos, 'fecha' => date('Y-m-d H:i:s')]),
                $ipAddress
            ]
        );
    }
}


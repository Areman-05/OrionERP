<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class LogService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function log(string $accion, ?int $usuarioId = null, ?string $tabla = null, ?int $registroId = null, array $datosAnteriores = [], array $datosNuevos = [], ?string $ipAddress = null): void
    {
        $sql = "INSERT INTO logs (usuario_id, accion, tabla, registro_id, datos_anteriores, datos_nuevos, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $usuarioId,
            $accion,
            $tabla,
            $registroId,
            json_encode($datosAnteriores),
            json_encode($datosNuevos),
            $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }

    public function getLogsByUsuario(int $usuarioId, int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM logs WHERE usuario_id = ? ORDER BY created_at DESC LIMIT ?",
            [$usuarioId, $limit]
        );
    }

    public function getLogsByTabla(string $tabla, int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM logs WHERE tabla = ? ORDER BY created_at DESC LIMIT ?",
            [$tabla, $limit]
        );
    }
}


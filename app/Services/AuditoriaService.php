<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class AuditoriaService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function registrarAccion(int $usuarioId, string $accion, string $tabla, ?int $registroId = null, array $datos = []): void
    {
        $this->db->query(
            "INSERT INTO logs (usuario_id, accion, tabla, registro_id, datos, ip_address, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [
                $usuarioId,
                $accion,
                $tabla,
                $registroId,
                json_encode($datos),
                $_SERVER['REMOTE_ADDR'] ?? null
            ]
        );
    }

    public function getLogsPorUsuario(int $usuarioId, int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT l.*, u.nombre as usuario_nombre 
             FROM logs l
             LEFT JOIN usuarios u ON l.usuario_id = u.id
             WHERE l.usuario_id = ?
             ORDER BY l.created_at DESC
             LIMIT ?",
            [$usuarioId, $limit]
        );
    }

    public function getLogsPorTabla(string $tabla, int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT l.*, u.nombre as usuario_nombre 
             FROM logs l
             LEFT JOIN usuarios u ON l.usuario_id = u.id
             WHERE l.tabla = ?
             ORDER BY l.created_at DESC
             LIMIT ?",
            [$tabla, $limit]
        );
    }
}

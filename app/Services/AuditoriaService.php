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

    public function registrarAccion(string $accion, ?int $usuarioId = null, ?string $tabla = null, ?int $registroId = null, array $datosAnteriores = [], array $datosNuevos = []): void
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $this->db->query(
            "INSERT INTO logs (usuario_id, accion, tabla, registro_id, datos_anteriores, datos_nuevos, ip_address) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $usuarioId,
                $accion,
                $tabla,
                $registroId,
                json_encode($datosAnteriores),
                json_encode($datosNuevos),
                $ipAddress
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

    public function getLogsPorFecha(string $fechaInicio, string $fechaFin): array
    {
        return $this->db->fetchAll(
            "SELECT l.*, u.nombre as usuario_nombre 
             FROM logs l
             LEFT JOIN usuarios u ON l.usuario_id = u.id
             WHERE DATE(l.created_at) BETWEEN ? AND ?
             ORDER BY l.created_at DESC",
            [$fechaInicio, $fechaFin]
        );
    }

    public function getResumenAuditoria(): array
    {
        $hoy = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM logs WHERE DATE(created_at) = CURDATE()"
        );
        
        $semana = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        
        $mes = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        
        return [
            'hoy' => (int) ($hoy['total'] ?? 0),
            'semana' => (int) ($semana['total'] ?? 0),
            'mes' => (int) ($mes['total'] ?? 0)
        ];
    }
}



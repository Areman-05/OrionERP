<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class NotificacionService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function crear(string $tipo, string $titulo, string $mensaje, int $usuarioId, ?string $enlace = null): int
    {
        $sql = "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, enlace) 
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [$usuarioId, $tipo, $titulo, $mensaje, $enlace]);
        
        return (int) $this->db->lastInsertId();
    }

    public function notificarStockBajo(): void
    {
        $productos = $this->db->fetchAll(
            "SELECT id, nombre, stock_actual, stock_minimo FROM productos 
             WHERE stock_actual <= stock_minimo AND activo = 1"
        );
        
        $admins = $this->db->fetchAll(
            "SELECT id FROM usuarios WHERE rol = 'admin' AND activo = 1"
        );
        
        foreach ($admins as $admin) {
            foreach ($productos as $producto) {
                $this->crear(
                    'warning',
                    'Stock bajo',
                    "El producto {$producto['nombre']} tiene stock bajo ({$producto['stock_actual']} unidades)",
                    $admin['id'],
                    "/productos/{$producto['id']}"
                );
            }
        }
    }

    public function notificarFacturasPendientes(): void
    {
        $facturas = $this->db->fetchAll(
            "SELECT COUNT(*) as cantidad FROM facturas 
             WHERE estado = 'pendiente' AND fecha_vencimiento < CURDATE()"
        );
        
        if ($facturas[0]['cantidad'] > 0) {
            $admins = $this->db->fetchAll(
                "SELECT id FROM usuarios WHERE rol IN ('admin', 'gerente') AND activo = 1"
            );
            
            foreach ($admins as $admin) {
                $this->crear(
                    'danger',
                    'Facturas vencidas',
                    "Hay {$facturas[0]['cantidad']} facturas vencidas pendientes de pago",
                    $admin['id'],
                    '/facturas?estado=vencida'
                );
            }
        }
    }

    public function getNoLeidas(int $usuarioId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM notificaciones 
             WHERE usuario_id = ? AND leida = 0 
             ORDER BY created_at DESC",
            [$usuarioId]
        );
    }

    public function marcarLeida(int $notificacionId, int $usuarioId): bool
    {
        $this->db->query(
            "UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?",
            [$notificacionId, $usuarioId]
        );
        return true;
    }
}


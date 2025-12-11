<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;
use OrionERP\Services\EmailService;
use OrionERP\Services\NotificacionService;

class StockAlertaService
{
    private $db;
    private $emailService;
    private $notificacionService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->emailService = new EmailService();
        $this->notificacionService = new NotificacionService();
    }

    public function verificarStockBajo(): array
    {
        $productos = $this->db->fetchAll(
            "SELECT p.*, c.nombre as categoria_nombre
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.stock_actual <= p.stock_minimo 
             AND p.activo = 1
             AND p.alerta_stock = 1
             ORDER BY (p.stock_actual - p.stock_minimo) ASC"
        );

        return $productos;
    }

    public function enviarAlertasStockBajo(): int
    {
        $productos = $this->verificarStockBajo();
        $enviadas = 0;

        foreach ($productos as $producto) {
            // Crear notificación
            $this->notificacionService->crear(
                'stock_bajo',
                "Stock bajo: {$producto['nombre']}",
                "El producto {$producto['nombre']} tiene stock bajo ({$producto['stock_actual']} unidades).",
                null,
                ['producto_id' => $producto['id']]
            );

            // Enviar email si está configurado
            if ($producto['enviar_email_alerta'] ?? false) {
                $this->emailService->enviarAlertaStockBajo($producto);
            }

            $enviadas++;
        }

        return $enviadas;
    }

    public function getProductosStockCritico(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.nombre as categoria_nombre
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.stock_actual = 0 
             AND p.activo = 1
             ORDER BY p.nombre"
        );
    }

    public function configurarAlerta(int $productoId, bool $activa, bool $enviarEmail = false): bool
    {
        return $this->db->query(
            "UPDATE productos 
             SET alerta_stock = ?, enviar_email_alerta = ?
             WHERE id = ?",
            [$activa ? 1 : 0, $enviarEmail ? 1 : 0, $productoId]
        );
    }
}


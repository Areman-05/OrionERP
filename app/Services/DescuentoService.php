<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class DescuentoService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function calcularDescuento(float $subtotal, string $tipoDescuento, float $valorDescuento): array
    {
        $descuento = 0;
        
        if ($tipoDescuento === 'porcentaje') {
            $descuento = ($subtotal * $valorDescuento) / 100;
        } elseif ($tipoDescuento === 'fijo') {
            $descuento = min($valorDescuento, $subtotal);
        }

        $total = $subtotal - $descuento;

        return [
            'subtotal' => $subtotal,
            'tipo_descuento' => $tipoDescuento,
            'valor_descuento' => $valorDescuento,
            'descuento' => round($descuento, 2),
            'total' => round($total, 2)
        ];
    }

    public function aplicarDescuentoProducto(int $productoId, string $tipoDescuento, float $valorDescuento, string $fechaInicio, string $fechaFin): bool
    {
        return $this->db->query(
            "INSERT INTO descuentos (producto_id, tipo, valor, fecha_inicio, fecha_fin, activo)
             VALUES (?, ?, ?, ?, ?, 1)",
            [$productoId, $tipoDescuento, $valorDescuento, $fechaInicio, $fechaFin]
        );
    }

    public function getDescuentosActivos(int $productoId = null): array
    {
        $where = "activo = 1 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()";
        $params = [];

        if ($productoId !== null) {
            $where .= " AND (producto_id = ? OR producto_id IS NULL)";
            $params[] = $productoId;
        }

        return $this->db->fetchAll(
            "SELECT * FROM descuentos WHERE $where ORDER BY fecha_inicio DESC",
            $params
        );
    }

    public function getDescuentoAplicable(int $productoId, float $precio): float
    {
        $descuentos = $this->getDescuentosActivos($productoId);
        $descuentoTotal = 0;

        foreach ($descuentos as $descuento) {
            if ($descuento['tipo'] === 'porcentaje') {
                $descuentoTotal += ($precio * $descuento['valor']) / 100;
            } elseif ($descuento['tipo'] === 'fijo') {
                $descuentoTotal += $descuento['valor'];
            }
        }

        return min($descuentoTotal, $precio);
    }
}


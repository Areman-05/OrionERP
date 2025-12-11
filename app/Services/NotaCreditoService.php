<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;
use OrionERP\Models\Factura;

class NotaCreditoService
{
    private $db;
    private $facturaModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->facturaModel = new Factura();
    }

    public function crearNotaCredito(int $facturaId, array $lineas, string $motivo): int
    {
        $factura = $this->facturaModel->findById($facturaId);
        if (!$factura) {
            throw new \Exception('Factura no encontrada');
        }

        $this->db->beginTransaction();

        try {
            // Calcular totales
            $subtotal = 0;
            foreach ($lineas as $linea) {
                $subtotal += ($linea['cantidad'] * $linea['precio_unitario']) - ($linea['descuento'] ?? 0);
            }

            $impuestos = $subtotal * 0.21; // IVA 21%
            $total = $subtotal + $impuestos;

            // Crear nota de crédito
            $notaCreditoId = $this->db->query(
                "INSERT INTO notas_credito 
                 (factura_id, numero, fecha, motivo, subtotal, impuestos, total, estado)
                 VALUES (?, ?, CURDATE(), ?, ?, ?, ?, 'activa')",
                [
                    $facturaId,
                    $this->generarNumeroNotaCredito(),
                    $motivo,
                    $subtotal,
                    $impuestos,
                    $total
                ]
            );

            $notaCreditoId = (int) $this->db->lastInsertId();

            // Crear líneas de nota de crédito
            foreach ($lineas as $linea) {
                $this->db->query(
                    "INSERT INTO lineas_nota_credito 
                     (nota_credito_id, producto_id, descripcion, cantidad, precio_unitario, descuento, impuesto)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $notaCreditoId,
                        $linea['producto_id'] ?? null,
                        $linea['descripcion'],
                        $linea['cantidad'],
                        $linea['precio_unitario'],
                        $linea['descuento'] ?? 0,
                        $linea['impuesto'] ?? 21
                    ]
                );
            }

            // Actualizar estado de la factura
            $this->facturaModel->update($facturaId, ['estado' => 'con_nota_credito']);

            $this->db->commit();
            return $notaCreditoId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function generarNumeroNotaCredito(): string
    {
        $ultimoNumero = $this->db->fetchOne(
            "SELECT MAX(CAST(SUBSTRING(numero, 4) AS UNSIGNED)) as ultimo
             FROM notas_credito
             WHERE numero LIKE 'NC-%'"
        );

        $siguiente = (int) ($ultimoNumero['ultimo'] ?? 0) + 1;
        return 'NC-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
    }

    public function getNotasCreditoPorFactura(int $facturaId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM notas_credito 
             WHERE factura_id = ? 
             ORDER BY fecha DESC",
            [$facturaId]
        );
    }

    public function anularNotaCredito(int $notaCreditoId, string $motivo): bool
    {
        return $this->db->query(
            "UPDATE notas_credito 
             SET estado = 'anulada', motivo_anulacion = ?
             WHERE id = ? AND estado = 'activa'",
            [$motivo, $notaCreditoId]
        );
    }
}

<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class NotaCreditoService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function crearNotaCredito(int $facturaId, array $lineas, string $motivo, int $usuarioId): int
    {
        $factura = $this->db->fetchOne(
            "SELECT * FROM facturas WHERE id = ?",
            [$facturaId]
        );

        if (!$factura) {
            throw new \Exception("Factura no encontrada");
        }

        $total = 0;
        foreach ($lineas as $linea) {
            $total += ($linea['cantidad'] * $linea['precio_unitario']) - ($linea['descuento'] ?? 0);
        }

        $numero = $this->generarNumeroNotaCredito();

        $this->db->beginTransaction();
        
        try {
            $this->db->query(
                "INSERT INTO notas_credito (factura_id, numero, fecha, motivo, total, estado, usuario_id) 
                 VALUES (?, ?, CURDATE(), ?, ?, 'pendiente', ?)",
                [$facturaId, $numero, $motivo, $total, $usuarioId]
            );

            $notaCreditoId = (int) $this->db->lastInsertId();

            foreach ($lineas as $linea) {
                $this->db->query(
                    "INSERT INTO lineas_nota_credito (nota_credito_id, producto_id, cantidad, precio_unitario, descuento) 
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $notaCreditoId,
                        $linea['producto_id'],
                        $linea['cantidad'],
                        $linea['precio_unitario'],
                        $linea['descuento'] ?? 0
                    ]
                );
            }

            $this->db->commit();
            return $notaCreditoId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function generarNumeroNotaCredito(): string
    {
        $ano = date('Y');
        $ultima = $this->db->fetchOne(
            "SELECT numero FROM notas_credito 
             WHERE numero LIKE ? 
             ORDER BY id DESC LIMIT 1",
            ["NC-$ano-%"]
        );

        if ($ultima) {
            $numero = (int) substr($ultima['numero'], -4);
            $numero++;
        } else {
            $numero = 1;
        }

        return sprintf("NC-%s-%04d", $ano, $numero);
    }

    public function getNotasCreditoPorFactura(int $facturaId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM notas_credito WHERE factura_id = ? ORDER BY fecha DESC",
            [$facturaId]
        );
    }
}


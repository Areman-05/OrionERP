<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class VarianteProducto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByProducto(int $productoId): array
    {
        return $this->db->fetchAll(
            "SELECT v.* FROM variantes_producto v WHERE v.producto_id = ? AND v.activo = 1 ORDER BY v.codigo",
            [$productoId]
        );
    }

    public function findById(int $id): ?array
    {
        $variante = $this->db->fetchOne(
            "SELECT v.*, p.nombre as producto_nombre 
             FROM variantes_producto v
             LEFT JOIN productos p ON v.producto_id = p.id
             WHERE v.id = ?",
            [$id]
        );

        if ($variante) {
            $variante['atributos'] = $this->getAtributos($id);
        }

        return $variante;
    }

    public function getAtributos(int $varianteId): array
    {
        return $this->db->fetchAll(
            "SELECT va.*, a.nombre as atributo_nombre, v.valor as valor_nombre
             FROM variante_atributos va
             LEFT JOIN atributos_producto a ON va.atributo_id = a.id
             LEFT JOIN valores_atributo v ON va.valor_id = v.id
             WHERE va.variante_id = ?",
            [$varianteId]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO variantes_producto (producto_id, codigo, sku, precio_venta, precio_compra, stock_actual, stock_minimo, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['producto_id'],
            $data['codigo'],
            $data['sku'] ?? null,
            $data['precio_venta'] ?? null,
            $data['precio_compra'] ?? null,
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 0,
            $data['activo'] ?? 1
        ]);

        $varianteId = (int) $this->db->lastInsertId();

        // Agregar atributos si existen
        if (!empty($data['atributos']) && is_array($data['atributos'])) {
            foreach ($data['atributos'] as $atributo) {
                $this->agregarAtributo($varianteId, $atributo);
            }
        }

        return $varianteId;
    }

    public function agregarAtributo(int $varianteId, array $atributo): int
    {
        $sql = "INSERT INTO variante_atributos (variante_id, atributo_id, valor_id, valor_texto) 
                VALUES (?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $varianteId,
            $atributo['atributo_id'],
            $atributo['valor_id'] ?? null,
            $atributo['valor_texto'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE variantes_producto SET codigo = ?, sku = ?, precio_venta = ?, precio_compra = ?, stock_actual = ?, stock_minimo = ?, activo = ? WHERE id = ?";
        
        $this->db->query($sql, [
            $data['codigo'],
            $data['sku'] ?? null,
            $data['precio_venta'] ?? null,
            $data['precio_compra'] ?? null,
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 0,
            $data['activo'] ?? 1,
            $id
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("UPDATE variantes_producto SET activo = 0 WHERE id = ?", [$id]);
        return true;
    }
}


<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class BuscadorService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function buscarProductos(array $filtros): array
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.activo = 1";
        
        $params = [];
        
        // Búsqueda por texto
        if (!empty($filtros['texto'])) {
            $sql .= " AND (p.nombre LIKE ? OR p.codigo LIKE ? OR p.descripcion LIKE ?)";
            $texto = '%' . $filtros['texto'] . '%';
            $params[] = $texto;
            $params[] = $texto;
            $params[] = $texto;
        }
        
        // Filtro por categoría
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND p.categoria_id = ?";
            $params[] = $filtros['categoria_id'];
        }
        
        // Filtro por rango de precio
        if (!empty($filtros['precio_min'])) {
            $sql .= " AND p.precio_venta >= ?";
            $params[] = $filtros['precio_min'];
        }
        
        if (!empty($filtros['precio_max'])) {
            $sql .= " AND p.precio_venta <= ?";
            $params[] = $filtros['precio_max'];
        }
        
        // Filtro por stock
        if (isset($filtros['stock_minimo']) && $filtros['stock_minimo'] === true) {
            $sql .= " AND p.stock_actual <= p.stock_minimo";
        }
        
        $sql .= " ORDER BY p.nombre ASC";
        
        // Límite de resultados
        if (!empty($filtros['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int) $filtros['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }

    public function autocompletar(string $termino, int $limit = 10): array
    {
        $sql = "SELECT p.id, p.codigo, p.nombre, p.precio_venta, p.stock_actual 
                FROM productos p 
                WHERE p.activo = 1 
                AND (p.nombre LIKE ? OR p.codigo LIKE ?)
                ORDER BY p.nombre ASC
                LIMIT ?";
        
        $termino = '%' . $termino . '%';
        
        return $this->db->fetchAll($sql, [$termino, $termino, $limit]);
    }

    public function buscarClientes(array $filtros): array
    {
        $sql = "SELECT * FROM clientes WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['texto'])) {
            $sql .= " AND (nombre LIKE ? OR codigo LIKE ? OR email LIKE ?)";
            $texto = '%' . $filtros['texto'] . '%';
            $params[] = $texto;
            $params[] = $texto;
            $params[] = $texto;
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filtros['estado'];
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
}


<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class IntegracionService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function sincronizarProductos(array $productosExternos): array
    {
        $resultados = [
            'creados' => 0,
            'actualizados' => 0,
            'errores' => []
        ];

        foreach ($productosExternos as $productoExterno) {
            try {
                $producto = $this->db->fetchOne(
                    "SELECT id FROM productos WHERE codigo = ?",
                    [$productoExterno['codigo']]
                );

                if ($producto) {
                    // Actualizar producto existente
                    $this->db->query(
                        "UPDATE productos SET nombre = ?, precio_venta = ?, stock_actual = ? WHERE id = ?",
                        [
                            $productoExterno['nombre'],
                            $productoExterno['precio'] ?? 0,
                            $productoExterno['stock'] ?? 0,
                            $producto['id']
                        ]
                    );
                    $resultados['actualizados']++;
                } else {
                    // Crear nuevo producto
                    $this->db->query(
                        "INSERT INTO productos (codigo, nombre, precio_venta, stock_actual, activo) 
                         VALUES (?, ?, ?, ?, 1)",
                        [
                            $productoExterno['codigo'],
                            $productoExterno['nombre'],
                            $productoExterno['precio'] ?? 0,
                            $productoExterno['stock'] ?? 0
                        ]
                    );
                    $resultados['creados']++;
                }
            } catch (\Exception $e) {
                $resultados['errores'][] = [
                    'codigo' => $productoExterno['codigo'] ?? 'desconocido',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $resultados;
    }
}


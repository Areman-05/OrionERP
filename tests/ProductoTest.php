<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use OrionERP\Models\Producto;
use OrionERP\Core\Database;

class ProductoTest extends TestCase
{
    private $productoModel;

    protected function setUp(): void
    {
        $this->productoModel = new Producto();
    }

    public function testProductoPuedeSerCreado(): void
    {
        $data = [
            'codigo' => 'TEST-' . time(),
            'nombre' => 'Producto de Prueba',
            'descripcion' => 'Descripción del producto',
            'precio_venta' => 100.00,
            'precio_compra' => 50.00,
            'stock_actual' => 10,
            'stock_minimo' => 5,
            'activo' => 1
        ];

        $id = $this->productoModel->create($data);
        
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testProductoPuedeSerEncontradoPorId(): void
    {
        $productos = $this->productoModel->getAll();
        
        if (!empty($productos)) {
            $producto = $this->productoModel->findById($productos[0]['id']);
            $this->assertNotNull($producto);
            $this->assertArrayHasKey('id', $producto);
            $this->assertArrayHasKey('nombre', $producto);
        }
    }

    public function testProductoPuedeSerEncontradoPorCodigo(): void
    {
        $codigo = 'TEST-CODE-' . time();
        
        $data = [
            'codigo' => $codigo,
            'nombre' => 'Producto Test',
            'precio_venta' => 50.00,
            'activo' => 1
        ];
        
        $this->productoModel->create($data);
        $producto = $this->productoModel->findByCodigo($codigo);
        
        $this->assertNotNull($producto);
        $this->assertEquals($codigo, $producto['codigo']);
    }
}

